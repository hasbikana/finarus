<?php

namespace App\Services;

class EmailProviderRegistry
{
    protected array $providers = [
        'bca' => ['senders' => ['info@bca.co.id', 'bca@notify.bca.co.id'], 'name' => 'BCA'],
        'mandiri' => ['senders' => ['mandiri@email.mandiri.co.id'], 'name' => 'Mandiri'],
        'bni' => ['senders' => ['bnicustomer@bni.co.id'], 'name' => 'BNI'],
        'bri' => ['senders' => ['bri-info@bri.co.id'], 'name' => 'BRI'],
        'gopay' => ['senders' => ['notification@gopay.co.id'], 'name' => 'GoPay'],
        'ovo' => ['senders' => ['notification@ovo.id'], 'name' => 'OVO'],
        'dana' => ['senders' => ['no-reply@dana.id'], 'name' => 'DANA'],
        'linkaja' => ['senders' => ['no-reply@linkaja.id'], 'name' => 'LinkAja'],
    ];

    public function getDefaultSenders(string $provider): array
    {
        $key = strtolower($provider);
        return $this->providers[$key]['senders'] ?? [];
    }

    public function getProviderName(string $key): string
    {
        return $this->providers[strtolower($key)]['name'] ?? $key;
    }

    public function allProviders(): array
    {
        return $this->providers;
    }

    public function getAllSenders(): array
    {
        $senders = [];
        foreach ($this->providers as $config) {
            array_push($senders, ...$config['senders']);
        }
        return $senders;
    }

    public function getProviderFromSender(string $from): ?string
    {
        $from = strtolower(trim($from));
        foreach ($this->providers as $key => $config) {
            foreach ($config['senders'] as $email) {
                if (str_contains($from, $email)) {
                    return $key;
                }
            }
        }
        return null;
    }
}
