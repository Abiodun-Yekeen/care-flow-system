<?php

namespace App\Modules\Core\Iam\Security;

use Illuminate\Support\Collection;

class PolicyDocument
{
    private string $version;
    private Collection $statements;

    public function __construct(array $statements = [], string $version = '2024-01-01')
    {
        $this->version = $version;
        $this->statements = new Collection();

        foreach ($statements as $statement) {
            $this->addStatement($statement);
        }
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getStatements(): Collection
    {
        return $this->statements;
    }

    public function addStatement(Statement $statement): self
    {
        $this->statements->push($statement);
        return $this;
    }

    public function removeStatement(string $sid): self
    {
        $this->statements = $this->statements->reject(
            fn(Statement $s) => $s->getSid() === $sid
        );
        return $this;
    }

    public function hasStatements(): bool
    {
        return $this->statements->isNotEmpty();
    }

    public function toArray(): array
    {
        return [
            'Version' => $this->version,
            'Statement' => $this->statements->map(fn(Statement $s) => $s->toArray())->toArray(),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    public static function fromArray(array $data): self
    {
        $document = new self([], $data['Version'] ?? '2024-01-01');

        foreach ($data['Statement'] as $statementData) {
            $document->addStatement(Statement::fromArray($statementData));
        }

        return $document;
    }

    public static function fromJson(string $json): self
    {
        return self::fromArray(json_decode($json, true));
    }
}
