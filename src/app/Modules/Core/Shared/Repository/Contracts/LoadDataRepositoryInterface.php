<?php

namespace App\Modules\Core\Shared\Repository\Contracts;

use Illuminate\Support\Collection;

interface LoadDataRepositoryInterface
{
    /**
     * Get specific data types
     */
    public function getGenders(): Collection;
    public function getTitles(): Collection;
    public function getMaritalStatuses(): Collection;
    public function getDepartments(): Collection;

}




