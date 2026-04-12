<?php

namespace App\Modules\Core\Iam\Services;

use App\Modules\Core\Iam\Models\Policy;
use App\Modules\Core\Iam\Models\Resource;
use App\Modules\Core\Iam\Security\PolicyDocument;
use App\Modules\Core\Iam\Security\Statement;

class PolicyBuilderService
{
    protected static array $resourceMap = [];
    private PolicyDocument $document;
    private ?Statement $currentStatement = null;

    public function __construct()
    {
        $this->document = new PolicyDocument();
    }

    public function new(): self
    {
        $this->document = new PolicyDocument();
        $this->currentStatement = null;
        return $this;
    }

    public function version(string $version): self
    {
        $this->document = new PolicyDocument(
            $this->document->getStatements()->toArray(),
            $version
        );
        return $this;
    }

    public function statement(string $sid = null): self
    {
        if ($this->currentStatement) {
            $this->document->addStatement($this->currentStatement);
        }

        $this->currentStatement = null;
        return $this;
    }

    public function allow(): self
    {
        $this->ensureCurrentStatement();
        $this->currentStatement = new Statement(
            'Allow',
            $this->currentStatement?->getActions() ?? [],
            $this->currentStatement?->getResources() ?? [],
            $this->currentStatement?->getSid()
        );
        return $this;
    }

    public function deny(): self
    {
        $this->ensureCurrentStatement();
        $this->currentStatement = new Statement(
            'Deny',
            $this->currentStatement?->getActions() ?? [],
            $this->currentStatement?->getResources() ?? [],
            $this->currentStatement?->getSid()
        );
        return $this;
    }

    public function action(string|array $actions): self
    {
        $this->ensureCurrentStatement();

        $currentActions = $this->currentStatement?->getActions() ?? [];

        // Use array_unique to keep the policy JSON clean
        $newActions = array_unique(array_merge($currentActions, (array) $actions));

        $this->currentStatement = new Statement(
            $this->currentStatement?->getEffect() ?? 'Allow',
            $newActions,
            $this->currentStatement?->getResources() ?? [],
            $this->currentStatement?->getSid(),
            $this->currentStatement?->getConditions() ?? []
        );

        return $this;
    }

    public function resource(string|array $resources): self
    {

        $this->ensureCurrentStatement();

        $resources = (array) $resources;
        $resolvedArns = array_map(function ($resource) {
            // 1. IMPROVED: Match 'arn:' followed by anything, or wildcards/placeholders
            // This correctly identifies 'arn:emr:clinical:...' as a valid ARN
            if (str_starts_with($resource, 'arn:') || $resource === '*' || str_contains($resource, '${')) {
                return $resource;
            }

            // 2. Handle Instance syntax
            $parts = explode(':', $resource, 2); // Limit to 2 parts to be safe
            $key = $parts[0];
            $instance = $parts[1] ?? null;

            // 3. Check the static cache before hitting the database
            if (!isset(static::$resourceMap[$key])) {
                $model = Resource::where('key', $key)->first();

                // If the model isn't found, don't prefix with 'unknown' - just keep the key
                if (!$model) {
                    return $resource;
                }

                static::$resourceMap[$key] = $model->arn->toString();
            }

            $baseArn = static::$resourceMap[$key];

            // 4. Return the full ARN
            return $instance ? "{$baseArn}:{$instance}" : $baseArn;
        }, $resources);
        // Merge and update the Statement object
        $currentResources = $this->currentStatement?->getResources() ?? [];
        $mergedResources = array_unique(array_merge($currentResources, $resolvedArns));

        $this->currentStatement = new Statement(
            $this->currentStatement?->getEffect() ?? 'Allow',
            $this->currentStatement?->getActions() ?? [],
            $mergedResources,
            $this->currentStatement?->getSid(),
            $this->currentStatement?->getConditions() ?? []
        );

        return $this;
    }

    /**
     * Clear the static cache (Useful for long-running processes like Octane or Queues)
     */
    public static function clearResourceMap(): void
    {
        static::$resourceMap = [];
    }

    public function condition(string $operator, array $conditions): self
    {
        $this->ensureCurrentStatement();

        $currentConditions = $this->currentStatement?->getConditions() ?? [];
        $currentConditions[$operator] = $conditions;

        $this->currentStatement = new Statement(
            $this->currentStatement?->getEffect() ?? 'Allow',
            $this->currentStatement?->getActions() ?? [],
            $this->currentStatement?->getResources() ?? [],
            $this->currentStatement?->getSid(),
            $currentConditions
        );

        return $this;
    }

    public function end(): self
    {
        if ($this->currentStatement) {
            $this->document->addStatement($this->currentStatement);
            $this->currentStatement = null;
        }
        return $this;
    }

    public function getDocument(): PolicyDocument
    {
        $this->end();
        return $this->document;
    }

    public function create(string $name, string $description = null): Policy
    {
        $this->end();
        return Policy::createFromDocument($name, $this->document, $description);
    }

    public function fromJson(string $json, string $name, string $description = null): Policy
    {
        $this->document = PolicyDocument::fromJson($json);
        return $this->create($name, $description);
    }

    private function ensureCurrentStatement(): void
    {
        if (!$this->currentStatement) {
            $this->currentStatement = new Statement('Allow', [], []);
        }
    }
}
