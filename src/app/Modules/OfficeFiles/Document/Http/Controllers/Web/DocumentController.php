<?php

namespace App\Modules\OfficeFiles\Document\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\OfficeFiles\Document\Models\Document;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DocumentController extends Controller
{
    
   public function index(Request $request)
{
    $documents = \App\Modules\OfficeFiles\Document\Models\Document::query()
        ->with(['uploader.department']) 
        ->when($request->search, function ($query, $search) {
            $query->where('original_name', 'like', "%{$search}%")
                  ->orWhere('document_type', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(12)
        ->withQueryString()
        ->through(fn ($doc) => [
            'id'            => $doc->id,
            'name'          => $doc->original_name,
            'type'          => $doc->document_type,
            'mime'          => $doc->mime_type,
            'size'          => number_format($doc->file_size / 1024, 2) . ' KB',
            'uploader'      =>  'System',
            'department'    =>  'N/A',
            'date'          => $doc->created_at->format('d M, Y'),
            'path'          => $doc->file_path,
        ]);

    return Inertia::render('modules/document/pages/Index', [
        'documents' => $documents,
        'filters'   => $request->only(['search'])
    ]);
} 

}
