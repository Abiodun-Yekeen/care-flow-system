<?php

namespace App\Modules\Dashboard\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\OfficeFiles\Movement\Models\FileAssignment;
use App\Modules\OfficeFiles\Movement\Models\FileMovement;
use App\Modules\OfficeFiles\Registry\Models\FileReceive;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{

    public function index(): Response
    {
        return Inertia::render('modules/dashboard/pages/index',[
            'summary' => fn () => [
                'active' => FileAssignment::where('status', 'active')->count(),

                'pending' => FileAssignment::where('status', 'active')
                    ->whereHas('file', fn($q) => $q->where('status', 'pending'))
                    ->count(),

                'completed' => FileAssignment::where('status', 'completed')
                    ->whereDate('completed_at', now())
                    ->count(),

                'overdue' => FileReceive::where('deadline_at', '<', now())
                    ->where('status', '!=', 'completed')
                    ->count(),
            ],

            // 2. Global Activity Feed (The last 5 movements in the whole system)
            'activities' => fn () =>
            FileMovement::with(['file', 'fromUser'])
                ->latest()
                ->take(5)
                ->get()
                ->map(fn($m) => [
                    'text' => "File {$m->file?->file_number} moved from {$m->from_user_name}",
                    'time' => $m->created_at->diffForHumans()
                ]),

            // Workload Panel (Grouped by Department)
            'workload' => fn () =>
            DB::table('file_assignments')
                ->join('departments', 'file_assignments.assigned_to_department_id', '=', 'departments.id')
                ->where('file_assignments.status', 'active')
                ->select('departments.name', DB::raw('count(*) as count'))
                ->groupBy('departments.name')
                ->get()
        ]);



    }
}
