<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AlertController extends Controller
{
    public function daily(Request $request): JsonResponse
    {
        $user = $request->user();
        $cacheKey = 'alert_daily_' . $user->id . '_' . now()->toDateString();

        if (Cache::has($cacheKey)) {
            return response()->json(['alert' => null]);
        }

        $messages = [];

        foreach ($user->accounts as $account) {
            if ($account->balance < 0) {
                $messages[] = 'Saldo ' . $account->name . ' minus Rp ' . number_format(abs($account->balance), 0, ',', '.') . '! Segera cek uang di dompet atau e-wallet Anda.';
            }
        }

        $budgets = $user->budgets()
            ->with('category')
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->get();

        foreach ($budgets as $budget) {
            if ($budget->is_over_budget) {
                $messages[] = 'Anggaran ' . $budget->category->name . ' telah terlampaui!';
            } elseif ($budget->progress >= 80) {
                $messages[] = 'Anggaran ' . $budget->category->name . ' sudah mencapai ' . $budget->progress . '%!';
            }
        }

        Cache::put($cacheKey, true, now()->endOfDay());

        return response()->json([
            'alert' => $messages ? implode(' | ', $messages) : null,
        ]);
    }
}
