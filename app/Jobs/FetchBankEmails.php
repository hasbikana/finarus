<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserOAuthToken;
use App\Services\EmailParserService;
use App\Services\GmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchBankEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        protected int $userId,
        protected int $maxEmails = 10,
    ) {}

    public function handle(GmailService $gmail, EmailParserService $parser): void
    {
        $user = User::find($this->userId);
        if (!$user?->settings?->email_fetch_enabled) {
            return;
        }

        $token = UserOAuthToken::where('user_id', $this->userId)->where('provider', 'google')->first();
        if (!$token) return;

        if ($token->isExpired() && !$gmail->refreshToken($token)) {
            Log::warning('FetchBankEmails: token refresh failed', ['user_id' => $this->userId]);
            return;
        }

        $messages = $gmail->fetchNewEmails($token, $this->maxEmails);
        $processed = 0;

        foreach ($messages as $meta) {
            $email = $gmail->fetchMessageContent($token, $meta->getId());
            if (!$email) continue;

            $parsed = $parser->parseEmail($email['from'], $email['subject'], $email['body']);
            if ($parsed && $parser->processParsedTransaction($parsed, $this->userId)) {
                $processed++;
            }
        }

        Log::info('FetchBankEmails done', [
            'user_id' => $this->userId,
            'fetched' => count($messages),
            'processed' => $processed,
        ]);
    }
}
