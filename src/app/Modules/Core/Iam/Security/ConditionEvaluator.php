<?php

namespace App\Modules\Core\Iam\Security;

class ConditionEvaluator
{
    private array $context;

    public function __construct(array $context = [])
    {
        $this->context = $context;
    }

    public function evaluate(array $conditions): bool
    {
        foreach ($conditions as $operator => $condition) {
            if (!$this->evaluateOperator($operator, $condition)) {
                return false;
            }
        }
        return true;
    }

    private function evaluateOperator(string $operator, array $condition): bool
    {
        return match ($operator) {
            'StringEquals' => $this->evaluateStringEquals($condition),
            'StringNotEquals' => $this->evaluateStringNotEquals($condition),
            'StringEqualsIgnoreCase' => $this->evaluateStringEqualsIgnoreCase($condition),
            'StringLike' => $this->evaluateStringLike($condition),
            'NumericEquals' => $this->evaluateNumericEquals($condition),
            'NumericGreaterThan' => $this->evaluateNumericGreaterThan($condition),
            'NumericLessThan' => $this->evaluateNumericLessThan($condition),
            'DateGreaterThan' => $this->evaluateDateGreaterThan($condition),
            'DateLessThan' => $this->evaluateDateLessThan($condition),
            'IpAddress' => $this->evaluateIpAddress($condition),
            'NotIpAddress' => $this->evaluateNotIpAddress($condition),
            'Bool' => $this->evaluateBool($condition),
            'Null' => $this->evaluateNull($condition),
            'TimeBetween' => $this->evaluateTimeBetween($condition),
            default => false,
        };
    }

    private function evaluateStringEquals(array $condition): bool
    {
        foreach ($condition as $key => $value) {
            if (($this->context[$key] ?? null) !== $value) {
                return false;
            }
        }
        return true;
    }

    private function evaluateStringNotEquals(array $condition): bool
    {
        foreach ($condition as $key => $value) {
            if (($this->context[$key] ?? null) === $value) {
                return false;
            }
        }
        return true;
    }

    private function evaluateStringEqualsIgnoreCase(array $condition): bool
    {
        foreach ($condition as $key => $value) {
            if (strtolower($this->context[$key] ?? '') !== strtolower($value)) {
                return false;
            }
        }
        return true;
    }

    private function evaluateStringLike(array $condition): bool
    {
        foreach ($condition as $key => $pattern) {
            $value = $this->context[$key] ?? null;
            if (!$value) {
                return false;
            }

            $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/i';
            if (!preg_match($regex, $value)) {
                return false;
            }
        }
        return true;
    }

    private function evaluateNumericEquals(array $condition): bool
    {
        foreach ($condition as $key => $value) {
            if (($this->context[$key] ?? 0) != $value) {
                return false;
            }
        }
        return true;
    }

    private function evaluateNumericGreaterThan(array $condition): bool
    {
        foreach ($condition as $key => $value) {
            if (($this->context[$key] ?? 0) <= $value) {
                return false;
            }
        }
        return true;
    }

    private function evaluateNumericLessThan(array $condition): bool
    {
        foreach ($condition as $key => $value) {
            if (($this->context[$key] ?? 0) >= $value) {
                return false;
            }
        }
        return true;
    }

    private function evaluateDateGreaterThan(array $condition): bool
    {
        foreach ($condition as $key => $value) {
            if (strtotime($this->context[$key] ?? 'now') <= strtotime($value)) {
                return false;
            }
        }
        return true;
    }

    private function evaluateDateLessThan(array $condition): bool
    {
        foreach ($condition as $key => $value) {
            if (strtotime($this->context[$key] ?? 'now') >= strtotime($value)) {
                return false;
            }
        }
        return true;
    }

    private function evaluateIpAddress(array $condition): bool
    {
        foreach ($condition as $key => $cidr) {
            if (!$this->ipInCidr($this->context[$key] ?? '', $cidr)) {
                return false;
            }
        }
        return true;
    }

    private function evaluateNotIpAddress(array $condition): bool
    {
        foreach ($condition as $key => $cidr) {
            if ($this->ipInCidr($this->context[$key] ?? '', $cidr)) {
                return false;
            }
        }
        return true;
    }

    private function evaluateBool(array $condition): bool
    {
        foreach ($condition as $key => $value) {
            if (($this->context[$key] ?? false) != $value) {
                return false;
            }
        }
        return true;
    }

    private function evaluateNull(array $condition): bool
    {
        foreach ($condition as $key => $shouldBeNull) {
            $isNull = !isset($this->context[$key]);
            if ($isNull != $shouldBeNull) {
                return false;
            }
        }
        return true;
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        if (!str_contains($cidr, '/')) {
            $cidr .= '/32';
        }

        [$subnet, $mask] = explode('/', $cidr);

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - $mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    private function evaluateTimeBetween(array $condition): bool
    {
        foreach ($condition as $key => $range) {
            [$start, $end] = $range;

            $current = $this->context[$key] ?? now()->format('H:i');
            $currentTime = strtotime($current);
            $startTime = strtotime($start);
            $endTime = strtotime($end);

            if ($currentTime < $startTime || $currentTime > $endTime) {
                return false;
            }
        }
        return true;
    }
}
