<?php

namespace App\Services;

use App\Contracts\EmailParser;
use App\DTO\ParsedTransaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class EmailParserService
{
    protected array $parsers = [];

    public function register(EmailParser $parser): void
    {
        $this->parsers[] = $parser;
    }

    public function getParsers(): array
    {
        return $this->parsers;
    }

    public function parseEmail(string $from, string $subject, string $body): ?ParsedTransaction
    {
        foreach ($this->parsers as $parser) {
            if ($parser->canParse($from, $subject)) {
                $result = $parser->parse($from, $subject, $body);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return null;
    }

    public function processParsedTransaction(ParsedTransaction $parsed, int $userId): ?Transaction
    {
        if (Transaction::where('user_id', $userId)
            ->where('email_message_id', $parsed->messageId)
            ->exists()) {
            return null;
        }

        $account = null;
        if ($parsed->provider) {
            $account = Account::where('user_id', $userId)
                ->where('provider', $parsed->provider)
                ->first();
        }

        $category = $this->findOrCreateCategory($userId, $parsed);

        $transaction = Transaction::create([
            'user_id' => $userId,
            'category_id' => $category->id,
            'account_id' => $account?->id,
            'type' => $parsed->type,
            'amount' => $parsed->amount,
            'description' => $parsed->description,
            'transaction_date' => $parsed->transactionDate,
            'email_message_id' => $parsed->messageId,
            'source' => 'email',
        ]);

        if ($account) {
            $account->recalculateBalance();
        }

        Log::info('Email transaction created', [
            'user_id' => $userId,
            'transaction_id' => $transaction->id,
            'provider' => $parsed->provider,
            'amount' => $parsed->amount,
        ]);

        return $transaction;
    }

    protected function findOrCreateCategory(int $userId, ParsedTransaction $parsed): Category
    {
        $name = $parsed->type === 'income' ? 'Pemasukan Auto' : 'Pengeluaran Auto';

        return Category::firstOrCreate(
            ['user_id' => $userId, 'name' => $name],
            ['type' => $parsed->type, 'icon' => '🤖', 'color' => '#6366f1']
        );
    }
}
