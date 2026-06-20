<?php

namespace App\Parsers;

use App\DTO\ParsedTransaction;
use Carbon\Carbon;

class GopayParser extends BaseParser
{
    public function provider(): string { return 'gopay'; }

    public function canParse(string $from, string $subject): bool
    {
        $f = strtolower($from);
        return str_contains($f, 'gopay') || str_contains($f, 'go-jek') || str_contains($subject, 'GoPay');
    }

    public function parse(string $from, string $subject, string $body): ?ParsedTransaction
    {
        $amount = $this->extractAmount($body);
        if (!$amount) return null;

        $type = $this->isTopup($subject, $body) ? 'income' : 'expense';
        $desc = $this->extractDescription($subject, $body);
        $date = $this->extractDate($body) ?? Carbon::now();
        $id = 'gopay-' . md5($subject . $amount . $date->format('Y-m-d'));

        return new ParsedTransaction($type, $amount, $desc, $date, $id, provider: 'gopay');
    }
}
