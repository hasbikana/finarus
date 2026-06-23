<?php

namespace App\Services;

use App\Models\Account;
use App\Models\SavingGoal;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    public function createTransaction(array $data): Transaction
    {
        $data['user_id'] = Auth::id();

        $transaction = Transaction::create($data);

        $isPending = $data['is_pending'] ?? true;

        if (!$isPending) {
            $this->applyBalance($transaction);
        }

        return $transaction->load(['category', 'account', 'savingGoal']);
    }

    public function approvePending(Transaction $transaction, array $data): Transaction
    {
        $transaction->update($data);

        $this->applyBalance($transaction);

        return $transaction->load(['category', 'account', 'savingGoal']);
    }

    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        $oldAccountId = $transaction->account_id;
        $oldSavingGoalId = $transaction->saving_goal_id;
        $oldAmount = $transaction->amount;
        $oldType = $transaction->type;

        $transaction->update($data);

        if ($oldAccountId) {
            Account::find($oldAccountId)?->recalculateBalance();
        }

        if ($transaction->account_id && $transaction->account_id !== $oldAccountId) {
            $transaction->account->recalculateBalance();
        } elseif ($transaction->account_id) {
            $transaction->account->recalculateBalance();
        }

        if ($oldSavingGoalId && $oldType === 'expense') {
            $oldGoal = SavingGoal::find($oldSavingGoalId);
            if ($oldGoal) {
                $oldGoal->decrement('current_amount', $oldAmount);
            }
        }

        if ($transaction->saving_goal_id && $transaction->type === 'expense') {
            $newGoal = SavingGoal::find($transaction->saving_goal_id);
            if ($newGoal) {
                $newGoal->increment('current_amount', $transaction->amount);
            }
        }

        return $transaction->load(['category', 'account', 'savingGoal']);
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        $accountId = $transaction->account_id;
        $savingGoalId = $transaction->saving_goal_id;
        $amount = $transaction->amount;
        $type = $transaction->type;

        $isPending = $transaction->is_pending;

        $transaction->delete();

        if (!$isPending) {
            if ($accountId) {
                Account::find($accountId)?->recalculateBalance();
            }

            if ($savingGoalId && $type === 'expense') {
                $goal = SavingGoal::find($savingGoalId);
                if ($goal) {
                    $goal->decrement('current_amount', $amount);
                }
            }
        }
    }

    protected function applyBalance(Transaction $transaction): void
    {
        if ($transaction->account_id) {
            $transaction->account->recalculateBalance();
        }

        if ($transaction->saving_goal_id && $transaction->type === 'expense') {
            $goal = SavingGoal::find($transaction->saving_goal_id);
            if ($goal) {
                $goal->increment('current_amount', $transaction->amount);
            }
        }
    }
}