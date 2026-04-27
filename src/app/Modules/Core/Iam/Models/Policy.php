<?php

namespace App\Modules\Core\Iam\Models;

use App\Modules\Core\Iam\Security\PolicyDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Policy extends Model
{
    protected $guarded = [];

    protected $casts = [
        'statement' => 'array',
        'is_emr_managed' => 'boolean',
        'metadata' => 'array',
    ];

    private ?PolicyDocument $document = null;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_policy')
            ->withTimestamps();
    }

    public function getDocument(): PolicyDocument
    {
        if (!$this->document) {
            $this->document = PolicyDocument::fromArray([
                'Version' => $this->version,
                'Statement' => $this->statement,
            ]);
        }
        return $this->document;
    }

    public function setDocument(PolicyDocument $document): void
    {
        $this->document = $document;
        $this->version = $document->getVersion();
        $this->statement = $document->toArray()['Statement'];
    }

    public function updateDocument(PolicyDocument $document): void
    {
        $this->setDocument($document);
        $this->save();
    }

    public static function createFromDocument(string $name, PolicyDocument $document, string $description = null): self
    {
        $policy = new self([
            'name' => $name,
            'description' => $description,
            'version' => $document->getVersion(),
            'statement' => $document->toArray()['Statement'],
            'is_emr_managed' => false,
        ]);

        $policy->document = $document;
        $policy->save();

        return $policy;
    }
}
