<?php

namespace App\Modules\Core\Iam\Traits;
use Models\Models\Module;

trait HasSidebar
{
    public function getAuthorizedMenu()
    {


        return Module::topLevel()
            ->get() // We don't need .with('children') for the flat sidebar
            ->filter(fn($m) => auth()->user()->hasPermissionTo("{$m->key}.index"))
            ->map(fn($m) => [
                'key'   => $m->key,
                'label' => $m->label,
                'route' => $m->route,
                'icon'  => $m->icon,
            ])->values();

    }
}




