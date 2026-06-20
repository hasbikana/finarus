<?php

namespace App\Parsers;

use App\Contracts\EmailParser;
use App\DTO\ParsedTransaction;
use Carbon\Carbon;

abstract class BaseParser implements EmailParser
{
    abstract public function provider(): string;
    abstract public function canParse(string $from, string $subject): bool;

    protected function extractAmount(string $body): ?float
    {
        $patterns = [
            '/Rp\s*([\d.,]+)/i',
            '/IDR\s*([\d.,]+)/i',
            '/(?:total|nominal|jumlah)[\s:]*Rp\s*([\d.,]+)/i',
            '/sebesar\s*Rp\s*([\d.,]+)/i',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $body, $m)) {
                $clean = str_replace(['.', ','], ['', '.'], $m[1]);
                $clean = preg_replace('/[^0-9.]/', '', $clean);
                $v = (float) $clean;
                if ($v > 0 && $v < 1000000000) return $v;
            }
        }
        return null;
    }

    protected function extractDescription(string $subject, string $body): string
    {
        if (preg_match('/(?:di|kepada|ke|dari)\s+([A-Za-z0-9\s&.\-]+?)(?:\s+sebesar|\s+sejumlah|\s+senilai|\s*$)/i', $body, $m)) {
            return trim($m[1]);
        }
        return $subject ?: 'Transaksi';
    }

    protected function extractDate(string $body): ?Carbon
    {
        $months = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];
        if (preg_match('/(\d{1,2})\s+(januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)\s+(\d{4})/i', $body, $m)) {
            $mn = strtolower($m[2]);
            if (isset($months[$mn])) return Carbon::create((int) $m[3], $months[$mn], (int) $m[1]);
        }
        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/', $body, $m)) {
            return Carbon::create((int) $m[3], (int) $m[2], (int) $m[1]);
        }
        return null;
    }

    protected function isDebit(string $body): bool
    {
        $b = strtolower($body);
        return str_contains($b, 'debit') || str_contains($b, 'pembelian') || str_contains($b, 'pembayaran');
    }

    protected function isTopup(string $subject, string $body): bool
    {
        return str_contains(strtolower($subject), 'topup') || str_contains(strtolower($body), 'top up');
    }
}
