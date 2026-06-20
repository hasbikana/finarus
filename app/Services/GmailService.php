<?php

namespace App\Services;

use App\Models\UserOAuthToken;
use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message as GmailMessage;
use Illuminate\Support\Facades\Log;

class GmailService
{
    protected array $senderEmails = [
        'bca' => ['info@bca.co.id', 'bca@notify.bca.co.id'],
        'mandiri' => ['mandiri@email.mandiri.co.id'],
        'bni' => ['bnicustomer@bni.co.id'],
        'bri' => ['bri-info@bri.co.id'],
        'gopay' => ['notification@gopay.co.id'],
        'ovo' => ['notification@ovo.id'],
        'dana' => ['no-reply@dana.id'],
        'linkaja' => ['no-reply@linkaja.id'],
    ];

    public function fetchNewEmails(UserOAuthToken $token, int $maxResults = 10): array
    {
        $client = $this->createClient($token);
        $gmail = new Gmail($client);
        $query = $this->buildSenderQuery();

        try {
            $messages = [];
            $pageToken = null;

            do {
                $opt = ['maxResults' => min(500, $maxResults), 'q' => $query];
                if ($pageToken) $opt['pageToken'] = $pageToken;

                $res = $gmail->users_messages->listUsersMessages('me', $opt);
                $messages = array_merge($messages, $res->getMessages() ?? []);
                $pageToken = $res->getNextPageToken();
            } while ($pageToken && count($messages) < $maxResults);

            return array_slice($messages, 0, $maxResults);
        } catch (\Exception $e) {
            Log::error('Gmail fetch failed: ' . $e->getMessage(), ['user_id' => $token->user_id]);
            return [];
        }
    }

    public function fetchMessageContent(UserOAuthToken $token, string $messageId): ?array
    {
        $client = $this->createClient($token);
        $gmail = new Gmail($client);

        try {
            $msg = $gmail->users_messages->get('me', $messageId, ['format' => 'full']);
            $headers = $this->parseHeaders($msg);
            $body = $this->extractBody($msg);

            return [
                'message_id' => $messageId,
                'from' => $headers['from'] ?? '',
                'subject' => $headers['subject'] ?? '',
                'date' => $headers['date'] ?? '',
                'body' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('Gmail msg fetch failed', ['msg' => $messageId, 'err' => $e->getMessage()]);
            return null;
        }
    }

    public function refreshToken(UserOAuthToken $token): bool
    {
        if (!$token->refresh_token) return false;
        $client = $this->createClient($token);

        try {
            $new = $client->fetchAccessTokenWithRefreshToken($token->refresh_token);
            if (!isset($new['error'])) {
                $token->access_token = $new['access_token'];
                $token->expires_at = isset($new['expires_in']) ? now()->addSeconds($new['expires_in']) : null;
                $token->save();
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Token refresh failed: ' . $e->getMessage());
        }
        return false;
    }

    protected function createClient(UserOAuthToken $token): Client
    {
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken($token->access_token);
        $client->addScope(Gmail::GMAIL_READONLY);
        return $client;
    }

    protected function buildSenderQuery(): string
    {
        $parts = [];
        foreach ($this->senderEmails as $emails) {
            foreach ($emails as $e) $parts[] = "from:$e";
        }
        return '{' . implode(' ', $parts) . '}';
    }

    protected function parseHeaders(GmailMessage $message): array
    {
        $h = [];
        foreach ($message->getPayload()->getHeaders() as $header) {
            $h[strtolower($header->getName())] = $header->getValue();
        }
        return $h;
    }

    protected function extractBody(GmailMessage $message): string
    {
        $payload = $message->getPayload();
        $body = '';

        if ($payload->getBody()->getData()) {
            $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload->getBody()->getData()));
        }

        if (empty($body) && $payload->getParts()) {
            foreach ($payload->getParts() as $part) {
                if (in_array($part->getMimeType(), ['text/plain', 'text/html']) && $part->getBody()->getData()) {
                    $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $part->getBody()->getData()));
                    if ($part->getMimeType() === 'text/html') {
                        $body = html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    }
                    break;
                }
            }
        }

        return trim(preg_replace('/\s+/', ' ', $body));
    }

    public function getProviderFromSender(string $from): ?string
    {
        $from = strtolower(trim($from));
        foreach ($this->senderEmails as $provider => $emails) {
            foreach ($emails as $email) {
                if (str_contains($from, $email)) return $provider;
            }
        }
        return null;
    }
}
