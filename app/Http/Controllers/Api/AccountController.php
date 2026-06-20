<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AccountController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $accounts = $request->user()->accounts()->orderBy('name')->get();

        $totalBalance = $accounts->sum('balance');

        return AccountResource::collection($accounts)
            ->additional([
                'meta' => [
                    'total_balance' => (float) $totalBalance,
                    'total_accounts' => $accounts->count(),
                ],
            ]);
    }

    public function store(StoreAccountRequest $request): JsonResponse
    {
        $account = $request->user()->accounts()->create($request->validated());

        return response()->json([
            'message' => 'Akun berhasil dibuat',
            'account' => new AccountResource($account),
        ], 201);
    }

    public function show(Request $request, Account $account): JsonResponse
    {
        $this->authorize('view', $account);

        return response()->json(new AccountResource($account));
    }

    public function update(UpdateAccountRequest $request, Account $account): JsonResponse
    {
        $this->authorize('update', $account);

        $account->update($request->validated());

        return response()->json([
            'message' => 'Akun berhasil diperbarui',
            'account' => new AccountResource($account->fresh()),
        ]);
    }

    public function destroy(Request $request, Account $account): JsonResponse
    {
        $this->authorize('delete', $account);

        if ($account->transactions()->exists()) {
            return response()->json([
                'message' => 'Tidak dapat menghapus akun yang memiliki transaksi. Hapus atau pindahkan transaksi terlebih dahulu.',
            ], 422);
        }

        $account->delete();

        return response()->json([
            'message' => 'Akun berhasil dihapus',
        ]);
    }
}