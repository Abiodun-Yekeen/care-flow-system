<?php

namespace App\Modules\OfficeFiles\Movement\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\OfficeFiles\Movement\Models\FileMovement;
use App\Modules\OfficeFiles\Registry\DTO\RegistryDTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MovementController extends Controller
{
    
   public function index(Request $request)
{
    $movements = FileMovement::query()
        ->with([
            'file', 
            'fromUser', 
            'toUser', 
            'fromDepartment', // CRITICAL: Must be loaded
            'toDepartment'   // CRITICAL: Must be loaded
        ])
        ->when($request->search, function ($query, $search) {
            $query->whereHas('file', function ($q) use ($search) {
                $q->where('file_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        })
        ->latest('acted_at')
        ->paginate(5)
        ->withQueryString()
        ->through(fn ($move) => [
            'id'            => $move->id,
            'file_number'   => $move->file?->file_number,
            'subject'       => $move->file?->subject,
            'actor_name'    => $move->from_user_name, // Your accessor
            'actor_dept'    => $move->fromDepartment?->name ?? 'N/A', // Dept Name
            'target_name'   => $move->to_user_name,   // Your accessor
            'target_dept'   => $move->toDepartment?->name ?? 'Registry', // Dept Name
            'type'          => $move->movement_type,
            'acted_at'      => $move->acted_at?->format('d M, Y H:i'),
        ]);

    return Inertia::render('modules/movement/pages/Index', [
        'movements' => $movements,
        'filters'   => $request->only(['search'])
    ]);
}

    

}
