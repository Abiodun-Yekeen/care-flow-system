<?php

namespace App\Modules\OfficeFiles\File\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Modules\OfficeFiles\File\Models\File;
use App\Modules\OfficeFiles\Movement\Models\FileAssignment;
use App\Modules\OfficeFiles\Movement\Models\FileMovement;
use App\Modules\OfficeFiles\Registry\Models\FileReceive;
use Inertia\Inertia;
use Inertia\Response;

class FileOverviewController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        return Inertia::render('modules/overview/ModuleOverview', [
            // These will load instantly with the page
            'stats' => fn () => [
                //  Total files currently on the user's desk
                ['title' => 'My Files', 'value' => FileAssignment::where('assigned_to_user_id', $user->id)
                    ->where('status', 'active')
                    ->count()],

                // Files on desk that are marked as 'pending'
                ['title' => 'Pending Action', 'value' => FileAssignment::where('assigned_to_user_id', $user->id)
                    ->where('status', 'active')
                    ->whereHas('file', fn($q) => $q->where('status', 'pending'))
                    ->count()],

                // Files on desk where the file's due_date has passed
                ['title' => 'Overdue', 'value' => FileAssignment::where('assigned_to_user_id', $user->id)
                    ->where('status', 'active')
                    ->whereHas('file.fileReceive', fn($q) => $q->where('deadline_at', '<', now()))
                    ->count()],

                // Drafts created by the user (from the receive/registry table)
                ['title' => 'Drafts', 'value' => FileReceive::where('created_by', $user->id)
                    ->where('status', 'draft')
                    ->count()]
            ],

            'tableData' => fn () =>
            FileAssignment::with('file')
                ->where('assigned_to_user_id', $user->id)
                ->where('status', 'active')
                ->latest('assigned_at')
                ->take(10)
                ->get()
                ->map(fn($assignment) => [
                    'id' => $assignment->file_id,
                    'identifier' => $assignment->file?->file_number,
                    'details' => $assignment->file?->subject,
                    'status' => $assignment->file?->status,
                    'time' => $assignment->assigned_at->diffForHumans(),
                ]),

            // RECENT HISTORY (From Movements)
            'recentActivity' => fn () =>
            FileMovement::where('from_user_id', $user->id)
                ->orWhere('to_user_id', $user->id)
                ->with('file')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn($m) => [
                    'id' => $m->id,
                    'user' => $m->from_user_name,
                    'action' => $m->to_user_id == $user->id ? 'received' : 'forwarded',
                    'target' => $m->file?->file_number,
                    'time' => $m->created_at->diffForHumans()
                ]),

            'overdueFiles' => fn () =>
            FileAssignment::query()
                ->join('file_receives', 'file_assignments.file_id', '=', 'file_receives.file_id')
                ->join('files', 'file_assignments.file_id', '=', 'files.id')
                ->where('file_assignments.assigned_to_user_id', $user->id)
                ->where('file_assignments.status', 'active')
                ->where('file_receives.deadline_at', '<', now())
                ->select('files.*', 'file_receives.deadline_at')
                ->get()


        ]);
    }

}
