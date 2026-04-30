<?php

namespace App\Modules\OfficeFiles\File\Services;

use App\Modules\Core\Shared\Services\Cache\CacheManager;
use App\Modules\OfficeFiles\File\Models\File;
use App\Modules\Organization\Department\Models\Department;
use Illuminate\Support\Facades\DB;

class FileService
{

  public function __construct(
      private CacheManager $cacheManager
  )
  {

  }



    public function submitFileTreated($request, $file)
    {
        DB::transaction(function () use ($request, $file) {

            // Update the Main File to a final state
            $file->update([
                'status' => 'treated',
                // Set current holder to null because it's no longer "pending" with anyone
                'current_holder_user_id' => null,
            ]);

            //  Create the Final Movement Record (The Archive Entry)
            $file->movements()->create([
                'from_department_id'  => auth()->user()->department_id,
                'from_user_id'        => auth()->id(),

                // NO TARGET: This signifies the file has been finalized
                'to_department_id'    => null,
                'to_user_id'          => null,
                'acted_by_user_id'    => auth()->id(),
                'received_by_user_id' => auth()->id(), // Mark as received by self to close loop
                'movement_type'       => 'treated',
                'movement_status'     => 'finalized',
                'remarks'             => $request->remark,
                'minute'              => $request->minute,
                'acted_at'            => now(),
            ]);
        });
    }
    public function getUserInbox($request)
    {
        $query = File::where('current_holder_user_id', auth()->id())
            ->with([
                'currentDepartment',
                'fileReceive',
                'creator',
                'currentHolder',
                'documents', // MorphMany relationship
                'movements' => fn($q) => $q->latest('acted_at') // For the timeline
            ])
            ->latest('last_movement_at');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                    ->orWhere('official_file_number', 'like', "%{$request->search}%")
                    ->orWhere('temp_file_number', 'like', "%{$request->search}%")
                    ->orWhere('source_name', 'like', "%{$request->search}%")
                    ->orWhere('source_reference_no', 'like', "%{$request->search}%");
            });
        }

        // Use through() to map data while preserving pagination metadata
        $files = $query->paginate(5)->through(fn($file) => [
            'id' => $file->id,
            'uuid' => $file->uuid,
            'file_number' => $file->official_file_number ?? $file->temp_file_number,
            'subject' => $file->subject,
            'source_name' => $file->source_name,
            'reference' => $file->source_reference_no,
            'remark' => $file->remark,
            'status' => $file->status,
            'priority' => $file->priority,
            'department' => $file->currentDepartment?->name,
            'current_holder' => $file->currentHolder?->name,
            'creator_name' => ucfirst(strtolower($file->creator?->name)),
            'date_received' => $file->date_received->format('Y-m-d H:i'),
            'deadline'=>$file->fileReceive->first()?->deadline_at?->diffForHumans() ?? 'N/A',
            // Map movements for the Timeline component
            'movements' => $file->movements,
            // Map documents for the Attachments section
            'attachments' => $file->documents->map(fn($doc) => [
                'id' => $doc->id,
                'original_name' => $doc->original_name,
                'file_path' => $doc->file_path,
                'created_at' => $file->created_at->format('Y-m-d H:i'),
            ]),
        ]);

        return $files;

    }

    public function searchForMerge($request)
    {
        $query = $request->get('query');
        $excludeId = $request->get('exclude_id');

        return File::query()
            ->where('id', '!=', $excludeId)
            ->where('is_temporary', true)
            ->where(function($q) use ($query) {
                $q->where('official_file_number', 'ILIKE', "%{$query}%")
                    ->orWhere('subject', 'ILIKE', "%{$query}%")
                    // The logic for Temp Files
                    ->orWhere('source_name', 'ILIKE', "%{$query}%")
                    ->orWhere('source_reference_no', 'ILIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();
    }

    public function merge( $request)
    {
        $request->validate([
            'source_id' => 'required|exists:files,id',
            'target_id' => 'required|exists:files,id',
        ]);

        $sourceFile = File::findOrFail($request->source_id);
        $targetFile = File::findOrFail($request->target_id);

        DB::transaction(function () use ($sourceFile, $targetFile) {

            // Update Target File Number if it's currently null
            if (empty($targetFile->file_number)) {
                $targetFile->update([
                    'file_number' => $sourceFile->file_number
                ]);
            }

            // Re-parent Documents
            // Assuming documents are linked via 'documentable_id' and 'documentable_type'
            DB::table('documents')
                ->where('documentable_id', $sourceFile->id)
                ->where('documentable_type', File::class) // Or 'App\Models\File'
                ->update(['documentable_id' => $targetFile->id]);

            // Update File Movements
            // Move all history from the old file to the new one
            DB::table('file_movements')
                ->where('file_id', $sourceFile->id)
                ->update(['file_id' => $targetFile->id]);

            // Optional: Delete or Archive the source file
            // After merging, the source file is usually empty and redundant
            $sourceFile->delete();
        });

    }
}
