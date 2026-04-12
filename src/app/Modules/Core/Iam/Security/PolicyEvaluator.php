<?php

namespace App\Modules\Core\Iam\Security;

use App\Modules\Core\Iam\Models\Policy;

class PolicyEvaluator
{

    public function evaluate(Policy $policy, string $action, string $resourceArn, array $context = []): ?bool
    {
        $document = $policy->getDocument();
        $conditionEvaluator = new ConditionEvaluator($context);

        $hasAllow = false;
        $hasDeny = false;

        foreach ($document->getStatements() as $statement) {
            if (!$statement->appliesToAction($action)) {
                continue;
            }

            if (!$statement->appliesToResource($resourceArn, $context)) {
                continue;
            }

            if ($statement->hasConditions()) {
                if (!$conditionEvaluator->evaluate($statement->getConditions())) {
                    continue;
                }
            }

            if ($statement->isDeny()) {
                $hasDeny = true;
            } elseif ($statement->isAllow()) {
                $hasAllow = true;
            }
        }

        if ($hasDeny) {
            return false;
        }

        if ($hasAllow) {
            return true;
        }

        return null;
    }

    public function evaluateMultiple(iterable $policies, string $action, string $resourceArn, array $context = []): bool
    {
        $hasAllow = false;

        foreach ($policies as $policy) {
            $result = $this->evaluate($policy, $action, $resourceArn, $context);

            if ($result === false) {
                return false;
            }

            if ($result === true) {
                $hasAllow = true;
            }
        }

        return $hasAllow;
    }

    public function getAllowedActions(iterable $policies, array $possibleActions, string $resourceArn, array $context = []): array
    {
        $allowed = [];

        foreach ($possibleActions as $action) {
            if ($this->evaluateMultiple($policies, $action, $resourceArn, $context)) {
                $allowed[] = $action;
            }
        }

        return $allowed;
    }
}
