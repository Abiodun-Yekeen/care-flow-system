<?php
namespace App\Modules\OfficeFiles\Registry\Services\Contracts;
use App\Modules\OfficeFiles\Registry\Models\FileReceive;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RegistryInterface
{
    /**
     * Create a new file intake (draft stage).
     * Used when clerk captures incoming mail/file before submission to HOD.
     * @param array $data
     * @return FileReceive
     */
    public function create(array $data): FileReceive;

    /**
     * Update an existing file intake.
     * Useful for editing draft before submission.
     * @param FileReceive $fileReceive
     * @param array $data
     * @return bool
     */
    public function update(FileReceive $fileReceive, array $data): FileReceive;

    /**
     * Find a file intake by ID.
     * @param int $id
     * @return FileReceive|null
     */
    public function findById(int $id): ?FileReceive;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    /**
     * Get all pending/draft file intakes.
     * Useful for registry dashboard.
     * @return Collection
     */
    public function getDrafts(): Collection;
 /**
  * Delete a file intake.
  * Only allowed if still in draft state.
  * @param int $id
  * @return bool
  * */
    public function delete(FileReceive $fileReceive): bool;
    /**
     * Get all file intakes.
     */
    public function all(): Collection;
}
