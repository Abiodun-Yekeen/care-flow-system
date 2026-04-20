<?php

namespace App\Modules\OfficeFiles\Registry\Repository\Eloquent;

use App\Modules\OfficeFiles\Registry\Models\FileReceive;
use App\Modules\OfficeFiles\Registry\Repository\Contracts\RegistryInterface;
use Dotenv\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RegistryRepository implements RegistryInterface
{
    /**
     * Create a new file intake.
     * This is usually the first step when a clerk captures incoming file/mail.
     */
    public function create(array $data): FileReceive
    {
       return FileReceive::create($data);
    }

    /**
     * Update existing file intake.
     * Only draft records should be updated.
     */
    public function update(FileReceive $fileReceive, array $data): FileReceive
    {
        // Only draft records should be updated.
        if ($fileReceive->status !== 'draft') {
            throw new \Exception("Only draft records can be updated.");
        }
        $fileReceive->update($data);
        return $fileReceive->refresh();
    }
    /**
     * Find file intake by ID.
     */
    public function findById(int $id): ?FileReceive
    {
        return FileReceive::find($id);
    }
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return FileReceive::query()
            ->with([
                'file',            // main file record
                'department',      // receiving department
                'creator',         // clerk
            ])

             // Filter by status

            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })

            // Filter by department
            ->when($filters['department_id'] ?? null, function ($query, $departmentId) {
                $query->where('receive_department_id', $departmentId);
            })

            /**
             * Filter by date range
             */
            ->when($filters['date_from'] ?? null, function ($query, $dateFrom) {
                $query->whereDate('date_received', '>=', $dateFrom);
            })
            ->when($filters['date_to'] ?? null, function ($query, $dateTo) {
                $query->whereDate('date_received', '<=', $dateTo);
            })

            // Advanced search (VERY IMPORTANT)
            ->when($filters['search'] ?? null, function ($query, $search) {

                $query->where(function ($q) use ($search) {

                    // Search in intake table
                    $q->where('received_from', 'like', "%{$search}%")
                        ->orWhere('remark', 'like', "%{$search}%")

                        // Search in related file
                        ->orWhereHas('file', function ($fileQuery) use ($search) {
                            $fileQuery->where('subject', 'like', "%{$search}%")
                                ->orWhere('source_name', 'like', "%{$search}%")
                                ->orWhere('source_reference_no', 'like', "%{$search}%")
                                ->orWhere('temp_file_number', 'like', "%{$search}%")
                                ->orWhere('official_file_number', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('created_at')
             //Pagination
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get all draft file intakes.
     */
    public function getDrafts(): Collection
    {
        return FileReceive::where('status', 'draft')
            ->latest()
            ->get();
    }

    /**
     * Delete intake.
     *
     * Only draft records should be deletable.
     */
    public function delete(FileReceive $fileReceive): bool
    {
        if ($fileReceive->status !== 'draft') {
            throw new \Exception("Cannot delete a non-draft file.");
        }
        return $fileReceive->delete();
    }
    /**
     * Get all file intakes.
     */
    public function all(): Collection
    {
        return FileReceive::latest()->get();
    }
}
