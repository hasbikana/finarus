<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $request->user()->transactions()->with(['category', 'account', 'savingGoal']);

        $query->where(function ($q) {
            $q->where('is_pending', false)->orWhereNull('is_pending');
        });

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->has('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        return TransactionResource::collection($transactions);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['is_pending'] = false;
        $data['pending_source'] = $request->pending_source ?? 'manual';

        $transaction = $this->transactionService->createTransaction($data);

        return response()->json([
            'message' => 'Transaksi berhasil dibuat',
            'transaction' => new TransactionResource($transaction),
        ], 201);
    }

    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('view', $transaction);

        return response()->json(new TransactionResource($transaction->load(['category', 'account', 'savingGoal'])));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('update', $transaction);

        $transaction = $this->transactionService->updateTransaction($transaction, $request->validated());

        return response()->json([
            'message' => 'Transaksi berhasil diperbarui',
            'transaction' => new TransactionResource($transaction),
        ]);
    }

    public function destroy(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('delete', $transaction);

        $this->transactionService->deleteTransaction($transaction);

        return response()->json([
            'message' => 'Transaksi berhasil dihapus',
        ]);
    }

    public function pending(Request $request): AnonymousResourceCollection
    {
        $transactions = $request->user()->transactions()
            ->with(['category', 'account', 'savingGoal'])
            ->where('is_pending', true)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return TransactionResource::collection($transactions);
    }

    public function approve(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('update', $transaction);

        if (!$transaction->is_pending) {
            return response()->json(['message' => 'Transaksi sudah dikonfirmasi'], 400);
        }

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'account_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $updateData = array_merge($validated, [
            'is_pending' => false,
            'pending_source' => $transaction->pending_source,
        ]);

        $transaction = $this->transactionService->approvePending($transaction, $updateData);

        return response()->json([
            'message' => 'Transaksi dikonfirmasi',
            'transaction' => new TransactionResource($transaction),
        ]);
    }

    public function reject(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('delete', $transaction);

        if (!$transaction->is_pending) {
            return response()->json(['message' => 'Transaksi sudah dikonfirmasi'], 400);
        }

        $transaction->delete();

        return response()->json([
            'message' => 'Transaksi ditolak dan dihapus',
        ]);
    }
}