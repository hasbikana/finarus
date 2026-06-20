<?php

namespace App\Parsers;

use App\DTO\ParsedTransaction;
use Carbon\Carbon;

class BniParser extends BaseParser
{
    public function provider(): string { return 'bni'; }

    public function canParse(string $from, string $subject): bool
    {
        return str_contains(strtolower($from), 'bni.co.id') || str_contains(strtolower($from), 'bni');
    }

    public function parse(string $from, string $subject, string $body): ?ParsedTransaction
    {
        $amount = $this->extractAmount($body);
        if (!$amount) return null;

        $type = $this->isDebit($body) ? 'expense' : 'income';
        $desc = $this->extractDescription($subject, $body);
        $date = $this->extractDate($body) ?? Carbon::now();
        $id = 'bni-' . md5($subject . $amount . $date->format('Y-m-d'));

        return new ParsedTransaction($type, $amount, $desc, $date, $id, provider: 'bni');
    }
}
