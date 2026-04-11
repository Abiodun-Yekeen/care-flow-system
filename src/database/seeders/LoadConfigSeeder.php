<?php

namespace Database\Seeders;

use App\Modules\Core\Shared\Models\Lga;
use App\Modules\Core\Shared\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class LoadConfigSeeder extends Seeder
{
    private const SYSTEM_USER = '0';
    private const DEPARTMENT = 'department';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::transaction(function () {
                $this->seedDefaultData();
            });
        } catch (\Throwable $e) {
            Log::error('Failed to seed default data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->command->error('Seeding failed: ' . $e->getMessage());
        }
    }

    /**
     * Seed all default data from configuration
     */
    private function seedDefaultData(): void
    {
        $defaultConfig = config('system_config.default', []);

        foreach ($defaultConfig as $table => $items) {
            $this->processTableData($table, $items);
        }
    }

    /**
     * Process data for a specific table
     */
    private function processTableData(string $table, $items): void
    {
        if (empty($items)) {
            return;
        }

        $modelClass = $this->getModelClass($table);
        // Define the processors as "Actions"
        $processors = [
            self::DEPARTMENT     => fn() => $this->processDepartment($modelClass, $items),
        ];

     // Execute the action if it exists, otherwise use fallback
        if (array_key_exists($table, $processors)) {
            $processors[$table]();
        } else {
            $this->processSimpleData($modelClass, $items);
        }
    }

    /**
     * Process simple key-value data (gender, marital_status, etc.)
     */
    private function processSimpleData(string $modelClass, array $items): void
    {
        foreach ($items as $item) {
            if (!is_array($item)) {
                $modelClass::updateOrCreate(
                    ['name' => Str::title($item)],
                    $this->getBaseAttributes()
                );
            }
        }
    }

    /**
     * Process encounter type data
     */

// Department
    private function processDepartment(string $modelClass, array $data): void
    {
        foreach ($data as $department) {

            $modelClass::updateOrCreate(
                ['name' => $department['name']],
                [
                    'type' => $department['type'],
                    ...$this->getBaseAttributes()
                ]
            );
        }
    }

    /**
     * Get base attributes for all records
     */
    private function getBaseAttributes(): array
    {
        return [
            'created_by' => self::SYSTEM_USER,
            'updated_at' => now(),
            'created_at' => now(),
        ];
    }

    /**
     * Get model class from table name
     */
    protected function getModelClass(string $name): string
    {
        $model = Str::studly($name);
        // Define the namespace map
        $map = [
            'Department'    => "App\Modules\Organization\Department\Models\\$model",
        ];

        // Fallback to a Core/Shared namespace if not in the map
        $namespace = $map[$model] ?? "App\Modules\Core\Shared\Models\\$model";

        return $namespace;
    }
}
