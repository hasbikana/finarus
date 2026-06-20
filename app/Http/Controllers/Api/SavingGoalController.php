<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSavingGoalRequest;
use App\Http\Requests\UpdateSavingGoalRequest;
use App\Http\Resources\SavingGoalResource;
use App\Models\SavingGoal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SavingGoalController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $goals = $request->user()->savingGoals()->orderBy('deadline')->get();

        return SavingGoalResource::collection($goals);
    }

    public function store(StoreSavingGoalRequest $request): JsonResponse
    {
        $goal = $request->user()->savingGoals()->create($request->validated());

        return response()->json([
            'message' => 'Tujuan tabungan berhasil dibuat',
            'saving_goal' => new SavingGoalResource($goal),
        ], 201);
    }

    public function show(Request $request, SavingGoal $savingGoal): JsonResponse
    {
        $this->authorize('view', $savingGoal);

        return response()->json(new SavingGoalResource($savingGoal));
    }

    public function update(UpdateSavingGoalRequest $request, SavingGoal $savingGoal): JsonResponse
    {
        $this->authorize('update', $savingGoal);

        $savingGoal->update($request->validated());

        return response()->json([
            'message' => 'Tujuan tabungan berhasil diperbarui',
            'saving_goal' => new SavingGoalResource($savingGoal->fresh()),
        ]);
    }

    public function destroy(Request $request, SavingGoal $savingGoal): JsonResponse
    {
        $this->authorize('delete', $savingGoal);

        $savingGoal->delete();

        return response()->json([
            'message' => 'Tujuan tabungan berhasil dihapus',
        ]);
    }
}