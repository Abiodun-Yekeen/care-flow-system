<?php

namespace App\Modules\OfficeFiles\Registry\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Modules\OfficeFiles\File\Models\File;
use App\Modules\OfficeFiles\Movement\Models\FileMovement;
use Inertia\Inertia;

class RegistryOverviewController extends Controller
{
        public function index()
        {
            return Inertia::render('modules/overview/ModuleOverview', [
                'stats' => [
        ['title' => 'Files Received', 'value' => File::count()],
        ['title' => 'Submitted', 'value' => File::where('status', 'submitted')->count()],
        // ...
    ],
    'tableData' => File::latest()->take(5)->get()->map(fn($f) => [
        'id' => $f->id,
        'identifier' => $f->file_number,
        'details' => $f->subject,
        'status' => $f->status,
        'time' => $f->created_at->diffForHumans(),
        'is_priority' => $f->status === 'pending'
    ]),
    'recentActivity' => FileMovement::latest()->take(3)->get()->map(fn($m) => [
        'id' => $m->id,
        'user' => $m->from_user_name,
        'action' => 'processed',
        'target' => $m->file?->file_number,
        'time' => $m->created_at->diffForHumans()
    ])
            ]);
        }

}
