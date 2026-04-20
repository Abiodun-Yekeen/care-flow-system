<?php

namespace App\Modules\OfficeFiles\File\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Modules\OfficeFiles\File\Models\File;
use App\Modules\OfficeFiles\Registry\DTO\RegistryDTO;
use App\Modules\OfficeFiles\Registry\Http\Requests\RegistryRequest;
use App\Modules\OfficeFiles\Registry\Models\FileReceive;
use App\Modules\OfficeFiles\Registry\Services\RegistryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MovementController extends Controller
{
    public function __construct( private RegistryService $registryService )
    {

    }
    public function index()
    {
        return Inertia::render('modules/registry/pages/Index', []);
    }

    public function create()
    {
        return Inertia::render('modules/registry/pages/Create', []);
    }

    public function store(RegistryRequest $request)
    {
        try {
            $is_draft = (bool)$request->is_draft;
            $user = $request->user();
            $dto = RegistryDTO::fromArray($request->validated());
            $this->registryService->submit($dto, $user, $is_draft);
            return redirect()->route('register.index')
                ->with('success', 'File registered successfully!');

        } catch (\RuntimeException $e) {
            // This catches  "No HOD" or "No Department" errors specifically
            Log::error($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {
            // This catches unexpected system crashes (Database down, etc.)
            Log::error('Submission Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An unexpected system error occurred. Please try again.');
        }
    }

    public function edit(FileReceive $register)
    {

        return Inertia::render('modules/registry/pages/Edit', [
            'file_receive' => $register->load('file.documents:id,documentable_id,file_path,original_name'),
        ]);
    }

    public function update(RegistryRequest $request, FileReceive $register)
    {
            if($register->status !=="draft") {
                return redirect()->back()->with('error', 'You cannot edit this file. Already submitted.');
            }

        try {

                $dto = RegistryDTO::fromArray($request->validated());

                $this->registryService->updateIsDraft($dto,$register);

                return redirect()->route('temp.file')
                ->with('success', 'File submitted successfully!');
        }catch (\RuntimeException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Submission Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An unexpected system error occurred. Please try again.');
        }

    }

    public function temporaryFile(Request $request)
    {
        return Inertia::render('modules/registry/pages/TempFile', [
            'temp_files' => $this->registryService->listTemporaryFiles($request->only('search')),
            'filters' => $request->only('search'),
        ]);
    }

}
