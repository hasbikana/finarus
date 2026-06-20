<?php

namespace App\Parsers;

use App\DTO\ParsedTransaction;
use Carbon\Carbon;

class BcaParser extends BaseParser
{
    public function provider(): string { return 'bca'; }

    public function canParse(string $from, string $subject): bool
    {
        $f = strtolower($from);
        return str_contains($f, 'bca.co.id') || str_contains($f, 'klikbca');
    }

    public function parse(string $from, string $subject, string $body): ?ParsedTransaction
    {
        $amount = $this->extractAmount($body);
        if (!$amount) return null;

        $type = $this->isDebit($body) ? 'expense' : 'income';
        $desc = $this->extractDescription($subject, $body);
        $date = $this->extractDate($body) ?? Carbon::now();
        $id = 'bca-' . md5($subject . $amount . $date->format('Y-m-d'));

        return new ParsedTransaction($type, $amount, $desc, $date, $id, provider: 'bca');
    }
}
