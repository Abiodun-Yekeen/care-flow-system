<?php

namespace App\Modules\Core\Iam\Security;

class Arn
{
   // private const PATTERN = '/^arn:([^:]+):([^:]+):([^:]*):([^:]*):(.+)$/';
    private const PATTERN = '/^arn:([^:]+):([^:]+):([^:]*):([^:]*):?(.+)?$/';
    private string $partition;
    private string $service;
    private ?string $region;
    private ?string $account;
    private string $resource;

    public function __construct( string $partition,string $service, ?string $region, ?string $account,string $resource)
    {
        $this->partition = $partition;
        $this->service = $service;
        $this->region = $region;
        $this->account = $account;
        $this->resource = $resource;
    }

    public function getPartition(): string
    {
        return $this->partition;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getResourceType(): string
    {
        if (str_contains($this->resource, ':')) {
            return explode(':', $this->resource)[0];
        }

        if (str_contains($this->resource, '/')) {
            return explode('/', $this->resource)[0];
        }

        return $this->resource;
    }

    public function getResourceId(): ?string
    {
        // Use # as a delimiter instead of /
        $parts = preg_split('#[:/]#', $this->resource, 2);
        return $parts[1] ?? null;

    }

    public function toString(): string
    {
        return  "arn:{$this->partition}:{$this->service}:{$this->resource}";

    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function matches(string $pattern): bool
    {
        if ($pattern === '*') return true;
        if (strcasecmp($pattern, $this->toString()) === 0) return true;

        // Use a different delimiter (#) so colons don't need escaping
        // and add the 'i' flag for case-insensitivity
        $regex = str_replace('\*', '.*', preg_quote($pattern, '#'));
        return (bool) preg_match('#^' . $regex . '$#i', $this->toString());
    }

    public static function fromString(string $arn): ?self
    {
        if (!str_starts_with($arn, 'arn:')) {
            return null;
        }

        $parts = explode(':', $arn);

        if (count($parts) < 4) {
            return null;
        }

        // Format: arn:partition:service:resourceType[:resourceId]
        $partition = $parts[1] ?? '';
        $service = $parts[2] ?? '';
        $resource = implode(':', array_slice($parts, 3));

        return new self(
            $partition,
            $service,
            null,
            null,
            $resource
        );
    }


    public static function build(
        string $service,
        string $resource,
        string $partition = null,
        string $region = null,
        string $account = null
    ): self {
        return new self(
            $partition ?? config('iam.partition', 'cf'),
            $service,
            $region ?? config('iam.region', 'global'),
            $account ?? config('iam.account_id', 'default'),
            $resource
        );
    }
}
