<?php

namespace App\Modules\OfficeFiles\File\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\OfficeFiles\File\Models\File;
use App\Modules\OfficeFiles\File\Services\FileService;
use App\Modules\Organization\Department\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FileController
{

    public function __construct(Private FileService $fileService){}

    public function index(Request $request)
    {
       $files = $this->fileService->getUserInbox($request);
        return Inertia::render('modules/mydesk/pages/index', [
            'files' => $files,
            'filters' => $request->only(['search'])
        ]);
    }

   public function treatFile(Request $request, File $file)
{
    if ($file->is_temporary){
        return back()->with('error', 'Temporary file can not be Treated, Merge or Open file');
    }

      $this->fileService->submitFileTreated($request, $file);

    return back()->with('success', 'File has been successfully treated and finalized.');
}

public function searchFileToMerger(Request $request)
{
    return $this->fileService->searchForMerge($request);
}


}

