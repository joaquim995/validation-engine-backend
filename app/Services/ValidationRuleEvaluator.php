<?php

namespace App\Services;

use App\Models\ValidationRule;
use Carbon\Carbon;

class ValidationRuleEvaluator
{
    /**
     * Evaluate validation rules for a given object type and form data
     *
     * @param string $objectType
     * @param array $formData
     * @return array
     */
    public function evaluateRules(string $objectType, array $formData): array
    {
        $rules = ValidationRule::active()
            ->forObjectType($objectType)
            ->get();

        $errors = [];

        foreach ($rules as $rule) {
            if ($this->evaluateExpression($rule->expression, $formData)) {
                $errors[] = $rule->error_message;
            }
        }

        return $errors;
    }

    /**
     * Evaluate a single expression against form data
     *
     * @param string $expression
     * @param array $formData
     * @return bool Returns true if validation fails (error condition)
     */
    private function evaluateExpression(string $expression, array $formData): bool
    {
        try {
            $processedExpression = $this->processExpression($expression, $formData);
            
            return $this->safeEval($processedExpression);
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * Process expression by replacing field names with values and handling functions
     *
     * @param string $expression
     * @param array $formData
     * @return string
     */
    private function processExpression(string $expression, array $formData): string
    {
        // Handle ISBLANK function
        $expression = preg_replace_callback('/ISBLANK\(([^)]+)\)/', function ($matches) use ($formData) {
            $fieldName = trim($matches[1]);
            $value = $formData[$fieldName] ?? '';
            return empty($value) ? 'true' : 'false';
        }, $expression);

        // Handle TODAY function
        $expression = preg_replace_callback('/TODAY\(\)/', function ($matches) {
            return "'" . Carbon::today()->format('Y-m-d') . "'";
        }, $expression);

        foreach ($formData as $fieldName => $value) {
            if (is_string($value)) {
                $value = "'" . addslashes($value) . "'";
            } elseif (is_null($value)) {
                $value = 'null';
            }
            
            $expression = preg_replace('/\b' . preg_quote($fieldName, '/') . '\b/', $value, $expression);
        }

        $expression = $this->processLogicalFunctions($expression);

        return $expression;
    }

    /**
     * Process AND and OR logical functions
     *
     * @param string $expression
     * @return string
     */
    private function processLogicalFunctions(string $expression): string
    {
        $expression = preg_replace_callback('/AND\(([^)]+)\)/', function ($matches) {
            $conditions = $matches[1];
            $conditions = preg_replace('/\bAND\([^)]+\)/', '', $conditions);
            $conditions = preg_replace('/\bOR\([^)]+\)/', '', $conditions);
            
            $conditionArray = array_map('trim', explode(',', $conditions));
            $result = [];
            
            foreach ($conditionArray as $condition) {
                if (!empty($condition)) {
                    $result[] = '(' . $condition . ')';
                }
            }
            
            return implode(' && ', $result);
        }, $expression);

        $expression = preg_replace_callback('/OR\(([^)]+)\)/', function ($matches) {
            $conditions = $matches[1];
            $conditions = preg_replace('/\bAND\([^)]+\)/', '', $conditions);
            $conditions = preg_replace('/\bOR\([^)]+\)/', '', $conditions);
            
            $conditionArray = array_map('trim', explode(',', $conditions));
            $result = [];
            
            foreach ($conditionArray as $condition) {
                if (!empty($condition)) {
                    $result[] = '(' . $condition . ')';
                }
            }
            
            return implode(' || ', $result);
        }, $expression);

        return $expression;
    }

    /**
     * Safely evaluate a PHP expression
     *
     * @param string $expression
     * @return bool
     */
    private function safeEval(string $expression): bool
    {
        $allowedPatterns = [
            '/^[a-zA-Z0-9\s\+\-\*\/\%\.\<\>\=\!\&\|\(\)\'\"\s]+$/',
        ];

        foreach ($allowedPatterns as $pattern) {
            if (!preg_match($pattern, $expression)) {
                throw new \InvalidArgumentException('Expression contains invalid characters');
            }
        }

        $expression = str_replace(['!=', '<=', '>=', '='], ['!==', '<=', '>=', '=='], $expression);

        try {
            $result = eval("return ($expression);");
            return (bool) $result;
        } catch (\ParseError $e) {
            throw new \InvalidArgumentException('Invalid expression syntax: ' . $e->getMessage());
        }
    }
}
