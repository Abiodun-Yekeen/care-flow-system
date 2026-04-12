<?php

namespace App\Modules\Core\Iam\Security;


class Statement
{
    private string $sid;
    private string $effect;
    private array $actions;
    private array $resources;
    private array $conditions;

    public function __construct(string $effect,array $actions, array $resources,?string $sid = null,array $conditions = [])
    {
        if (!in_array($effect, ['Allow', 'Deny'])) {
            throw new \InvalidArgumentException("Effect must be 'Allow' or 'Deny'");
        }

        $this->sid = $sid ?? uniqid('stmt-', true);
        $this->effect = $effect;
        $this->actions = $actions;
        $this->resources = $resources;
        $this->conditions = $conditions;
    }

    public function getSid(): string
    {
        return $this->sid;
    }

    public function getEffect(): string
    {
        return $this->effect;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getResources(): array
    {
        return $this->resources;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function appliesToAction(string $action): bool
    {
        foreach ($this->actions as $pattern) {
            if ($this->matchesPattern($pattern, $action)) {
                return true;
            }
        }
        return false;
    }

    public function appliesToResource(string $resourceArn, array $context = []): bool
    {
        foreach ($this->resources as $pattern) {

            $pattern = preg_replace_callback('/\$\{([^}]+)\}/', function ($matches) use ($context) {
                $value = data_get($context, $matches[1]);
                return $value === null ?'' :(string) $value;
            }, $pattern);


            if ($this->matchesPattern($pattern, $resourceArn)) {
                return true;
            }
        }
        return false;
    }


    public function isAllow(): bool
    {
        // Use strcasecmp to be safe against 'allow' vs 'Allow'
        return strcasecmp($this->effect, 'Allow') === 0;
    }

    public function isDeny(): bool
    {
        return strcasecmp($this->effect, 'Deny') === 0;
    }
    public function hasConditions(): bool
    {
        return !empty($this->conditions);
    }

    private function matchesPattern(string $pattern, string $value): bool
    {
        if ($pattern === '*') {
            return true;
        }

        // Escape special characters
        $regex = preg_quote($pattern, '/');

        // Replace the escaped asterisk (\*) with the regex wildcard (.*)
        $regex = str_replace('\*', '.*', $regex);

        // Add anchors and case-insensitive flag
        return (bool) preg_match('/^' . $regex . '$/i', $value);
    }

    public function toArray(): array
    {
        $data = [
            'Sid' => $this->sid,
            'Effect' => $this->effect,
            'Action' => $this->actions,
            'Resource' => $this->resources,
        ];

        if (!empty($this->conditions)) {
            $data['Condition'] = $this->conditions;
        }

        return $data;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['Effect'],
            (array) $data['Action'],
            (array) $data['Resource'],
            $data['Sid'] ?? null,
            $data['Condition'] ?? []
        );
    }
}
