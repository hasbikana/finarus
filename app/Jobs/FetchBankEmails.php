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

        $scopes = $user->accounts()
            ->where('type', '!=', 'cash')
            ->whereNotNull('email_scopes')
            ->pluck('email_scopes')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->all();

        $after = $user->settings->last_fetch_at;
        $messages = $gmail->fetchNewEmails($token, $scopes, $this->maxEmails, $after);
        $processed = 0;

        foreach ($messages as $meta) {
            $email = $gmail->fetchMessageContent($token, $meta->getId());
            if (!$email) continue;

            $parsed = $parser->parseEmail($email['from'], $email['subject'], $email['body'], $this->userId);
            if ($parsed && $parser->saveAsPendingNotification($parsed, $this->userId, $email['message_id'], $email['body'], $email['from'])) {
                $processed++;
            }
        }

        $user->settings->update(['last_fetch_at' => now()]);

        Log::info('FetchBankEmails done', [
            'user_id' => $this->userId,
            'fetched' => count($messages),
            'processed' => $processed,
        ]);
    }
}
