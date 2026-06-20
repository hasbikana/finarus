<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Http\Requests\StoreSavingGoalRequest;
use App\Http\Requests\UpdateSavingGoalRequest;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use App\Models\SavingGoal;
use App\Models\Account;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class WebCrudController extends Controller
{
    public function __construct(private TransactionService $txnService) {}

    public function storeTransaksi(StoreTransactionRequest $req): RedirectResponse
    {
        $this->txnService->createTransaction($req->validated());
        return back()->with('success', 'Transaksi berhasil dibuat.');
    }

    public function updateTransaksi(UpdateTransactionRequest $req, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);
        $this->txnService->updateTransaction($transaction, $req->validated());
        return back()->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroyTransaksi(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);
        $this->txnService->deleteTransaction($transaction);
        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function storeKategori(StoreCategoryRequest $req): RedirectResponse
    {
        Auth::user()->categories()->create($req->validated());
        return back()->with('success', 'Kategori berhasil dibuat.');
    }

    public function updateKategori(UpdateCategoryRequest $req, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);
        $category->update($req->validated());
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);
        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function storeAnggaran(StoreBudgetRequest $req): RedirectResponse
    {
        Auth::user()->budgets()->create($req->validated());
        return back()->with('success', 'Anggaran berhasil dibuat.');
    }

    public function updateAnggaran(UpdateBudgetRequest $req, Budget $budget): RedirectResponse
    {
        $this->authorize('update', $budget);
        $budget->update($req->validated());
        return back()->with('success', 'Anggaran berhasil diperbarui.');
    }

    public function destroyAnggaran(Budget $budget): RedirectResponse
    {
        $this->authorize('delete', $budget);
        $budget->delete();
        return back()->with('success', 'Anggaran berhasil dihapus.');
    }

    public function storeTabungan(StoreSavingGoalRequest $req): RedirectResponse
    {
        Auth::user()->savingGoals()->create($req->validated());
        return back()->with('success', 'Tujuan tabungan berhasil dibuat.');
    }

    public function updateTabungan(UpdateSavingGoalRequest $req, SavingGoal $savingGoal): RedirectResponse
    {
        $this->authorize('update', $savingGoal);
        $savingGoal->update($req->validated());
        return back()->with('success', 'Tujuan tabungan berhasil diperbarui.');
    }

    public function destroyTabungan(SavingGoal $savingGoal): RedirectResponse
    {
        $this->authorize('delete', $savingGoal);
        $savingGoal->delete();
        return back()->with('success', 'Tujuan tabungan berhasil dihapus.');
    }

    public function storeDompet(StoreAccountRequest $req): RedirectResponse
    {
        Auth::user()->accounts()->create($req->validated());
        return back()->with('success', 'Akun berhasil dibuat.');
    }

    public function updateDompet(UpdateAccountRequest $req, Account $account): RedirectResponse
    {
        $this->authorize('update', $account);
        $account->update($req->validated());
        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroyDompet(Account $account): RedirectResponse
    {
        $this->authorize('delete', $account);
        if ($account->transactions()->exists()) {
            return back()->with('error', 'Akun tidak bisa dihapus karena memiliki transaksi.');
        }
        $account->delete();
        return back()->with('success', 'Akun berhasil dihapus.');
    }
}
