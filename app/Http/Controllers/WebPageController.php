<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\PendingNotification;
use App\Models\SavingGoal;
use App\Models\Transaction;
use App\Models\UserSetting;
use App\Models\UserOAuthToken;
use App\Jobs\FetchBankEmails;
use App\Services\EmailProviderRegistry;
use App\Services\ReportService;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebPageController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();

        $this->ensureCashAccount($user);

        $totals = $user->transactions()
            ->selectRaw("type, SUM(amount) as total")
            ->whereIn('type', ['income', 'expense'])
            ->where(fn($q) => $q->where('is_pending', false)->orWhereNull('is_pending'))
            ->whereBetween('transaction_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('type')
            ->pluck('total', 'type');
        $totalIncome = (float) ($totals['income'] ?? 0);
        $totalExpense = (float) ($totals['expense'] ?? 0);

        $allAccounts = $user->accounts()->orderBy('type')->orderBy('name')->get();
        $balance = $allAccounts->sum('balance');
        $cashBalance = $allAccounts->where('type', 'cash')->sum('balance');
        $ewalletBalance = $allAccounts->where('type', 'ewallet')->sum('balance');
        $bankBalance = $allAccounts->whereIn('type', ['bank', 'credit_card'])->sum('balance');
        $cashAccounts = $allAccounts->where('type', 'cash')->values();
        $ewalletAccounts = $allAccounts->where('type', 'ewallet')->values();
        $bankAccounts = $allAccounts->whereIn('type', ['bank', 'credit_card'])->values();

        $activeSavingGoals = $user->savingGoals()
            ->whereColumn('current_amount', '<', 'target_amount')
            ->count();

        $recentTransactions = $user->transactions()
            ->with(['category', 'account'])
            ->where(fn($q) => $q->where('is_pending', false)->orWhereNull('is_pending'))
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

        $pendingCount = $user->pendingNotifications()
            ->where('status', 'pending')
            ->count();

        return view('app.dashboard', compact(
            'balance', 'cashBalance', 'ewalletBalance', 'bankBalance',
            'cashAccounts', 'ewalletAccounts', 'bankAccounts',
            'totalIncome', 'totalExpense',
            'activeSavingGoals', 'recentTransactions', 'budgetProgress', 'budgetAlerts',
            'pendingCount'
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

        $defaultProviders = app(EmailProviderRegistry::class)->allProviders();

        return view('app.dompet-digital.index', compact('accounts', 'totalBalance', 'defaultProviders'));
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

    public function notifikasi(): View
    {
        $user = Auth::user();

        $notifications = $user->pendingNotifications()
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        $categories = $user->categories()->orderBy('name')->get();
        $accounts = $user->accounts()->orderBy('name')->get();

        return view('app.notifikasi.index', compact('notifications', 'categories', 'accounts'));
    }

    public function approveNotification(Request $request, TransactionService $transactionService): RedirectResponse
    {
        $notification = PendingNotification::findOrFail($request->route('pending_notification'));

        if ($notification->user_id !== Auth::id() || $notification->status !== 'pending') {
            return back()->with('error', 'Notifikasi tidak valid');
        }

        $validated = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', Auth::id())],
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', Auth::id())],
            'type' => ['required', 'in:income,expense'],
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $transactionService->createTransaction([
                'user_id' => Auth::id(),
                'category_id' => $validated['category_id'],
                'account_id' => $validated['account_id'],
                'type' => $validated['type'],
                'amount' => $notification->amount,
                'description' => $validated['description'] ?? $notification->merchant ?? $notification->description,
                'transaction_date' => $notification->notification_date ?? now()->toDateString(),
                'is_pending' => false,
                'pending_source' => $notification->source,
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }

        $notification->update(['status' => 'confirmed']);

        return redirect()->route('notifikasi')->with('success', 'Transaksi berhasil dibuat');
    }

    public function rejectNotification(Request $request): RedirectResponse
    {
        $notification = PendingNotification::findOrFail($request->route('pending_notification'));

        if ($notification->user_id !== Auth::id() || $notification->status !== 'pending') {
            return back()->with('error', 'Notifikasi tidak valid');
        }

        $notification->update(['status' => 'rejected']);

        return redirect()->route('notifikasi')->with('success', 'Notifikasi ditolak');
    }

    public function fetchEmails(): RedirectResponse
    {
        $token = UserOAuthToken::where('user_id', Auth::id())->where('provider', 'google')->first();
        if (!$token) {
            return back()->with('error', 'Google akun belum terhubung.');
        }

        FetchBankEmails::dispatchSync(Auth::id());

        return back()->with('success', 'Fetch email sedang diproses. Buka halaman Notifikasi beberapa saat lagi.');
    }

    public function approvePendingTransaction(Request $request, Transaction $transaction, TransactionService $txnService): RedirectResponse
    {
        $this->authorize('update', $transaction);

        if (!$transaction->is_pending) {
            return back()->with('error', 'Transaksi sudah dikonfirmasi');
        }

        $validated = $request->validate([
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('user_id', Auth::id())],
            'account_id' => ['nullable', Rule::exists('accounts', 'id')->where('user_id', Auth::id())],
            'description' => 'nullable|string|max:1000',
        ]);

        $updateData = array_merge($validated, [
            'is_pending' => false,
            'pending_source' => $transaction->pending_source,
        ]);

        $txnService->approvePending($transaction, $updateData);

        return redirect()->route('notifikasi')->with('success', 'Transaksi berhasil dikonfirmasi');
    }

    public function rejectPendingTransaction(Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        if (!$transaction->is_pending) {
            return back()->with('error', 'Transaksi sudah dikonfirmasi');
        }

        $transaction->update(['pending_source' => 'rejected']);

        return redirect()->route('notifikasi')->with('success', 'Transaksi ditolak');
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