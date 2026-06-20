<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BudgetController extends Controller
{
    public function __construct(private BudgetService $budgetService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $budgets = $this->budgetService->getBudgetsWithSpent((int) $month, (int) $year);

        return BudgetResource::collection($budgets);
    }

    public function store(StoreBudgetRequest $request): JsonResponse
    {
        $budget = $request->user()->budgets()->create($request->validated());

        return response()->json([
            'message' => 'Anggaran berhasil dibuat',
            'budget' => new BudgetResource($budget->load('category')),
        ], 201);
    }

    public function show(Request $request, Budget $budget): JsonResponse
    {
        $this->authorize('view', $budget);

        return response()->json(new BudgetResource($budget->load('category')));
    }

    public function update(UpdateBudgetRequest $request, Budget $budget): JsonResponse
    {
        $this->authorize('update', $budget);

        $budget->update($request->validated());

        return response()->json([
            'message' => 'Anggaran berhasil diperbarui',
            'budget' => new BudgetResource($budget->fresh()->load('category')),
        ]);
    }

    public function destroy(Request $request, Budget $budget): JsonResponse
    {
        $this->authorize('delete', $budget);

        $budget->delete();

        return response()->json([
            'message' => 'Anggaran berhasil dihapus',
        ]);
    }
}