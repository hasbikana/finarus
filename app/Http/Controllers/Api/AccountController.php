<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\UserOAuthToken;
use App\Services\EmailProviderRegistry;
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

    public function store(StoreAccountRequest $request, EmailProviderRegistry $registry): JsonResponse
    {
        $data = $request->validated();
        $data = $this->applyEmailScopes($data, $registry, $request->user());

        $account = $request->user()->accounts()->create($data);

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

    public function update(UpdateAccountRequest $request, Account $account, EmailProviderRegistry $registry): JsonResponse
    {
        $this->authorize('update', $account);

        $data = $request->validated();
        $data = $this->applyEmailScopes($data, $registry, $request->user(), $account);

        $account->update($data);

        return response()->json([
            'message' => 'Akun berhasil diperbarui',
            'account' => new AccountResource($account->fresh()),
        ]);
    }

    protected function applyEmailScopes(array $data, EmailProviderRegistry $registry, $user, ?Account $existing = null): array
    {
        $type = $data['type'] ?? $existing?->type;

        if ($type === 'cash') {
            $data['email_scopes'] = null;
            return $data;
        }

        if (!array_key_exists('email_scopes', $data)) {
            return $data;
        }

        $scopes = $data['email_scopes'] ?? [];

        if (empty($scopes) && !empty($data['provider'] ?? $existing?->provider)) {
            $scopes = $registry->getDefaultSenders($data['provider'] ?? $existing->provider);
        }

        if (!empty($scopes)) {
            $oauthEmail = UserOAuthToken::where('user_id', $user->id)
                ->where('provider', 'google')
                ->value('email');

            if ($oauthEmail) {
                $scopes = array_filter($scopes, fn($s) => strtolower($s) !== strtolower($oauthEmail));
            }

            $data['email_scopes'] = array_values(array_unique($scopes));
        } else {
            $data['email_scopes'] = null;
        }

        return $data;
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