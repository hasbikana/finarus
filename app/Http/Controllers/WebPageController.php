<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingGoal;
use App\Models\Transaction;
use App\Models\UserSetting;
use App\Models\UserOAuthToken;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class WebPageController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();

        $this->ensureCashAccount($user);

        $totalIncome = $user->transactions()
            ->where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $totalExpense = $user->transactions()
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $balance = $user->accounts()->sum('balance');
        $cashBalance = $user->accounts()->where('type', 'cash')->sum('balance');
        $ewalletBalance = $user->accounts()->where('type', 'ewallet')->sum('balance');
        $bankBalance = $user->accounts()->whereIn('type', ['bank', 'credit_card'])->sum('balance');

        $cashAccounts = $user->accounts()->where('type', 'cash')->orderBy('name')->get();
        $ewalletAccounts = $user->accounts()->where('type', 'ewallet')->orderBy('name')->get();
        $bankAccounts = $user->accounts()->whereIn('type', ['bank', 'credit_card'])->orderBy('name')->get();

        $activeSavingGoals = $user->savingGoals()
            ->whereColumn('current_amount', '<', 'target_amount')
            ->count();

        $recentTransactions = $user->transactions()
            ->with(['category', 'account'])
            ->latest('transaction_date')
            ->latest('id')
            ->take(5)
            ->get();

        $budgetProgress = $user->budgets()
            ->with('category')
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->get();

        $budgetAlerts = $budgetProgress->filter(fn($b) => $b->is_over_budget || $b->progress >= 80)->values();

        return view('app.dashboard', compact(
            'balance', 'cashBalance', 'ewalletBalance', 'bankBalance',
            'cashAccounts', 'ewalletAccounts', 'bankAccounts',
            'totalIncome', 'totalExpense',
            'activeSavingGoals', 'recentTransactions', 'budgetProgress', 'budgetAlerts'
        ));
    }

    public function transaksi(Request $request): View
    {
        $user = Auth::user();
        $query = $user->transactions()
            ->with(['category', 'account']);

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest('transaction_date')
            ->latest('id')
            ->paginate(20)
            ->appends($request->query());

        $categories = $user->categories()->orderBy('name')->get();
        $accounts = $user->accounts()->orderBy('name')->get();

        return view('app.transaksi.index', compact('transactions', 'categories', 'accounts'));
    }

    public function kategori(): View
    {
        $user = Auth::user();
        $categories = $user->categories()
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        return view('app.kategori.index', compact('categories'));
    }

    public function anggaran(): View
    {
        $user = Auth::user();
        $budgets = $user->budgets()
            ->with('category')
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->get();

        $expenseCategories = $user->categories()
            ->where('type', '!=', 'income')
            ->orderBy('name')
            ->get();

        return view('app.anggaran.index', compact('budgets', 'expenseCategories'));
    }

    public function tabungan(): View
    {
        $user = Auth::user();
        $savingGoals = $user->savingGoals()->orderBy('deadline')->get();
        $accounts = $user->accounts()->orderBy('name')->get();

        return view('app.tabungan.index', compact('savingGoals', 'accounts'));
    }

    public function laporan(): View
    {
        return view('app.laporan.index');
    }

    public function dompetDigital(): View
    {
        $user = Auth::user();
        $accounts = $user->accounts()->where('type', '!=', 'cash')->orderBy('name')->get();
        $totalBalance = $accounts->sum('balance');

        return view('app.dompet-digital.index', compact('accounts', 'totalBalance'));
    }

    public function pengaturan(): View
    {
        $user = Auth::user();
        $settings = $user->settings ?? UserSetting::create(['user_id' => $user->id]);

        $oauthToken = UserOAuthToken::where('user_id', $user->id)
            ->where('provider', 'google')
            ->first();

        $oauthConnected = $oauthToken !== null;
        $oauthEmail = $oauthToken?->email;
        $oauthFetchEnabled = $settings->email_fetch_enabled;

        return view('app.pengaturan', compact(
            'settings', 'oauthConnected', 'oauthEmail', 'oauthFetchEnabled'
        ));
    }

    public function bantuan(): View
    {
        return view('app.bantuan');
    }

    protected function ensureCashAccount($user): void
    {
        if (!$user->accounts()->where('type', 'cash')->exists()) {
            $user->accounts()->create([
                'name' => 'Cash / Dompet',
                'provider' => 'Cash',
                'type' => 'cash',
                'balance' => 0,
            ]);
        }
    }

    public function reportMonthly(Request $request, ReportService $report): JsonResponse
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        return response()->json($report->getMonthlySummary($month, $year));
    }

    public function reportCategories(Request $request, ReportService $report): JsonResponse
    {
        $type = $request->input('type', 'expense');
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        return response()->json([
            'categories' => $report->getCategoryBreakdown($type, $month, $year),
        ]);
    }

    public function reportTrend(Request $request, ReportService $report): JsonResponse
    {
        $year = (int) $request->input('year', now()->year);
        return response()->json(['trend' => $report->getMonthlyTrend($year)]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'nullable|boolean',
            'budget_alerts' => 'nullable|boolean',
            'email_fetch_enabled' => 'nullable|boolean',
            'theme' => 'nullable|in:light,dark',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $settings = $user->settings ?? UserSetting::create(['user_id' => $user->id]);
        $settings->update($validator->validated());

        return response()->json(['message' => 'Tersimpan']);
    }
}