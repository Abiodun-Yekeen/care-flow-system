<?php

namespace App\Modules\Core\Shared\Repository\Contracts;

use Illuminate\Support\Collection;

interface LoadDataRepositoryInterface
{
    /**
     * Get specific data types
     */
    public function getRoles(): Collection;
    public function getDepartments(): Collection;

}




