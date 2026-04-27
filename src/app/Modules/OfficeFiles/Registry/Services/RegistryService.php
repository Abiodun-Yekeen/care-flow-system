<?php
namespace App\Modules\OfficeFiles\Registry\Services;
use App\Events\FileSent;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Repository\Contracts\UserRepositoryInterface;
use App\Modules\Core\Shared\Services\Firebase\FcmService;
use App\Modules\OfficeFiles\File\Models\File;
use App\Modules\OfficeFiles\Registry\DTO\RegistryDTO;
use App\Modules\OfficeFiles\Registry\Models\FileReceive;
use App\Modules\OfficeFiles\Registry\Repository\Contracts\RegistryInterface;
use App\Modules\Organization\Department\Models\Department;
use App\Notifications\FileSubmittedToHOD;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegistryService
{
    public function __construct(
        private readonly RegistryInterface $registry,
        Private File $file, Private UserRepositoryInterface $userRepository
    )
    {
    }




    /**
     * Capture incoming file/mail, create temp file, attach scan,
     * and submit to HOD.
     */
    public function submit(RegistryDTO $dto, User $user, bool $is_draft = false): FileReceive
    {
        return DB::transaction(function () use ($dto, $user,$is_draft) {


            // We use the department assigned to the User
            $department = $user->primaryDepartment;

            // Check if department existsF
            if (!$department) {
                throw new \RuntimeException("Your account is not assigned to a primary department.");
            }
            $hodUserId = $department->current_hod_id;

            // Throw exception if HOD is missing
            if (!$hodUserId) {
                throw new \RuntimeException("No HOD currently appointed for $department->name");
            }


            //  Create main file record
            $file = File::create([
                'uuid' => (string)Str::uuid(),
                'official_file_number' => null,
                'temp_file_number' => $this->generateTempFileNumber($department),
                'is_temporary' => true,
                'subject' => $dto->subject,
                'description' => $dto->description ?? '',
                'source_name' => $dto->source_name,
                'source_reference_no' => $dto->source_reference_no,
                'remark' => $dto->remark,
                'status' => $is_draft ? 'draft' : 'pending',
                'priority' => $dto->priority ,
                'current_department_id' => $department->id,
                'current_holder_user_id' => $hodUserId, // The HOD is now the holder
                'created_by' => $user->id,
                'date_received' => $dto->date_received,
                'last_movement_at' => now(),
            ]);

            //  Create registry receive record
            $fileReceive = $this->registry->create([
                'uuid' => (string)Str::uuid(),
                'file_id' => $file->id,
                'receive_department_id' => $department->id,
                'created_by' => $user->id,
                'received_from' => $dto->received_from,
                'remark' => $dto->remark,
                'status' => $is_draft ? 'draft':'submitted',
                'date_received' => $dto->date_received,
                'deadline_at'=> $is_draft ? null:$dto->deadline_at ?? now()->addHours(24),
                'submitted_at' => $is_draft ? null:now(),
            ]);

            if (!$is_draft) {
                $this->validateFileUpload($dto, $file);
            }

            //  Upload documents (Handling UploadedFile objects from dd($dto))
                $this->uploadFile($dto, $file);

            //  Create movement record
            $file->movements()->create([
                'from_department_id' => $department->id,
                'from_user_id' => $user->id,
                'to_department_id' => $department->id,
                'to_user_id' => $hodUserId,
                'acted_by_user_id' => $user->id,
                'received_by_user_id' => null, // HOD hasn't received it yet
                'movement_type' => $is_draft ? 'draft' : 'submitted_to_hod',
                'movement_status' => 'pending_receipt',
                'remarks' => $dto->remark,
                'minute' => 'File submitted from registry to HOD.',
                'acted_at' => now(),
            ]);

            if (!$is_draft) {
                $hod = $this->userRepository->findById($hodUserId);
                if ($hod) {
                  $this->notifyUser($user,$file,$hod);
                    event(new FileSent($file, $hodUserId));
                  // Push Notification
//                    SendFcmNotification::dispatch(
//                        $user,
//                        "New File",
//                        "New file received: {$file->subject}",
//                        [
//                            'file_id' => $file->id,
//                            'action_type' => 'new_submission'
//                        ]
//                    );
                }
            }

            return $fileReceive->load(['file', 'department', 'creator']);
        });
    }

    /**
     * Generate temp file number.
     *
     * Example: TMP-FIN-2026-00001
     */
    private function generateTempFileNumber(Department $department): string
    {
        $prefix = 'TMP';
        $departmentCode = strtoupper($department->code ?? 'GEN');
        $year = now()->year;

        $count = File::whereYear('created_at', $year)->count() + 1;
        $sequence = str_pad((string)$count, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$departmentCode}-{$year}-{$sequence}";
    }

    public function listTemporaryFiles(array $filters)
    {
        return $this->paginate($filters);
    }

    private function paginate(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return $this->file->query()
            ->with(['fileReceive', 'currentDepartment', 'currentHolder', 'creator'])
            ->where('is_temporary', true)
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    // ILIKE is PostgreSQL's built-in case-insensitive search
                    $q->where('source_name', 'ILIKE', "%{$search}%")
                        ->orWhere('temp_file_number', 'ILIKE', "%{$search}%")
                        ->orWhere('status', 'ILIKE', "%{$search}%")
                        ->orWhere('source_reference_no', 'ILIKE', "%{$search}%");

                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();


    }

    private function uploadFile($dto,$file){
        if (!empty($dto->file_path) && is_array($dto->file_path)) {
            foreach ($dto->file_path as $index => $document) {
                if ($document instanceof \Illuminate\Http\UploadedFile) {
                    // Generate a safe name
                    $filename = $file->uuid . '_' . time() . '_' . $index . '.' . $document->getClientOriginalExtension();

                    // Store the file in the 'public' disk
                    $path = $document->storeAs('office-files/documents', $filename, 'public');

                    $file->documents()->create([
                        'original_name' => $document->getClientOriginalName(),
                        'stored_name' => $filename,
                        'file_path' => $path,
                        'mime_type' => $document->getMimeType(),
                        'file_size' => $document->getSize(),
                        'created_by' => auth()->id(),
                    ]);
                }
            }
        }
    }

    public function updateIsDraft(RegistryDTO $dto,FileReceive $fileReceive)
    {
        return DB::transaction(function () use ($dto, $fileReceive) {

            $file = $fileReceive->file;

            if ($fileReceive->status === 'draft') {
                $this->validateFileUpload($dto,$file);
            }

            $hod = $this->userRepository->findById($file->current_holder_user_id);

            //  Upload file record
            $file->update([
                'subject' => $dto->subject,
                'source_name' => $dto->source_name,
                'source_reference_no' => $dto->source_reference_no,
                'remark' => $dto->remark,
                'status' => 'pending',
                'priority' => $dto->priority,
            ]);

            //  Update registry receive record
            $fileReceive->update([
                'remark' => $dto->remark,
                'status' => 'submitted',
                'deadline_at' => $dto->deadline_at ?? now()->addHours(24),
                'submitted_at' => now(),
            ]);

            $file->movements()->update([
             
                'acted_by_user_id' => auth()->id,
                'movement_type' => 'submitted_to_hod',
                'movement_status' => 'pending',
                'remarks' => $dto->remark,
                'minute' => 'File submitted from registry to HOD.',
                'acted_at' => now(),
            ]);



            //  Upload documents (Handling UploadedFile objects from dd($dto))
            $this->uploadFile($dto, $file);

            //Send Notification
            if ($hod) {
                $hod->notify(new FileSubmittedToHOD($file,'new_submission'));
                event(new FileSent($file, $file->current_holder_user_id));
            }

        });
    }

    private function validateFileUpload(RegistryDTO $dto, File $file): void
    {
        // Is there a new file in the current request (DTO)?
        $hasNewUpload = !empty($dto->file_path);
        $hasExistingFiles = $file->documents()->exists();

        if (!$hasNewUpload && !$hasExistingFiles) {
            throw new \RuntimeException("You must attach at least one document before submitting.");
        }
    }

    private function notifyUser(User $user,$file, $hod)
    {
         $hod->notify(new FileSubmittedToHOD($file,'new_submission'));

        foreach ($user->fcmTokens as $token) {
            app(FcmService::class)->send(
                $token->token,
                "Test Notification",
                "This is a test push",
                ['test' => true]
            );
        }
    }
}
