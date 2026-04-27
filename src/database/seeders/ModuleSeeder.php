<?php

namespace Database\Seeders;

use App\Modules\Core\Shared\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::transaction(function () {

            foreach (config('resources.default') as $parentConfig) {

                $parent = $this->upsertModule($parentConfig);

                if (!empty($parentConfig['children'])) {

                    foreach ($parentConfig['children'] as $childConfig) {
                        $this->upsertModule($childConfig, $parent->id);
                    }
                }
            }
        });
    }

        protected function upsertModule(array $data, ?int $parentId = null): Module
    {
        return Module::updateOrCreate(
            ['key' => $data['key']],
            [
                'label'     => $data['label'],
                'icon'      => $data['icon'] ?? null,
                'route'     => $data['route'] ?? null,
                'parent_id' => $parentId,
                'order'     => $data['order'] ?? 0,
                'is_active' => true,
            ]
        );
    }


}
