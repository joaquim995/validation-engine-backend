<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ValidationRule;
use App\Services\ValidationRuleEvaluator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ValidationRuleController extends Controller
{
    protected $evaluator;

    public function __construct(ValidationRuleEvaluator $evaluator)
    {
        $this->evaluator = $evaluator;
    }

    /**
     * Evaluate validation rules for form submission
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function evaluate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'object_type' => 'required|string',
                'form_data' => 'required|array'
            ]);

            $objectType = $validated['object_type'];
            $formData = $validated['form_data'];

            \Log::info('Validation request received', [
                'object_type' => $objectType,
                'form_data' => $formData
            ]);

            $errors = $this->evaluator->evaluateRules($objectType, $formData);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'errors' => ['Validation request is invalid: ' . implode(', ', array_flatten($e->errors()))]
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Evaluation error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'errors' => ['An error occurred during validation: ' . $e->getMessage()]
            ], 500);
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'errors' => $errors
            ], 422);
        }

        return response()->json([
            'success' => true,
            'errors' => []
        ]);
    }

    /**
     * Get all validation rules (for admin interface)
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $rules = ValidationRule::orderBy('object_type')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($rules);
    }

    /**
     * Create a new validation rule
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'object_type' => 'required|string',
            'expression' => 'required|string',
            'error_message' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $rule = ValidationRule::create($validated);

        return response()->json($rule, 201);
    }

    /**
     * Update a validation rule
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $rule = ValidationRule::findOrFail($id);

        $validated = $request->validate([
            'object_type' => 'sometimes|string',
            'expression' => 'sometimes|string',
            'error_message' => 'sometimes|string',
            'is_active' => 'sometimes|boolean'
        ]);

        $rule->update($validated);

        return response()->json($rule);
    }

    /**
     * Delete a validation rule
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $rule = ValidationRule::findOrFail($id);
        $rule->delete();

        return response()->json(['message' => 'Validation rule deleted successfully']);
    }
}
