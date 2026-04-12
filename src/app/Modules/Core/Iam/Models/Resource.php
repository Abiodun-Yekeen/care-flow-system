<?php

namespace App\Modules\Core\Iam\Models;

use App\Modules\Core\Iam\Security\Arn;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table = 'resources';

    protected $fillable = ['key', 'name', 'module_key', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function getArnAttribute(): Arn
    {
        return Arn::build(
            $this->module_key,
            $this->key
        );
    }

    public function arnFor(string $resourceId): Arn
    {
        return Arn::build(
            $this->module_key,
            $this->key . ':' . $resourceId
        );
    }

    public function arnForMany(array $resourceIds): array
    {
        return array_map(fn($id) => $this->arnFor($id), $resourceIds);
    }

    public static function findByArn(Arn $arn): ?self
    {
        return self::where('module_key', $arn->getService())
            ->where('key', $arn->getResourceType())
            ->first();
    }

    public function getAvailableActions(): array
    {
        return config("iam.actions.{$this->key}", ['view', 'create', 'update', 'delete']);
    }
}
