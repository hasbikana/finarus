<?php

namespace App\Parsers;

use App\DTO\ParsedTransaction;
use Carbon\Carbon;

class DanaParser extends BaseParser
{
    public function provider(): string { return 'dana'; }

    public function canParse(string $from, string $subject): bool
    {
        return str_contains(strtolower($from), 'dana') || str_contains($subject, 'DANA');
    }

    public function parse(string $from, string $subject, string $body): ?ParsedTransaction
    {
        $amount = $this->extractAmount($body);
        if (!$amount) return null;

        $type = $this->isTopup($subject, $body) ? 'income' : 'expense';
        $desc = $this->extractDescription($subject, $body);
        $date = $this->extractDate($body) ?? Carbon::now();
        $id = 'dana-' . md5($subject . $amount . $date->format('Y-m-d'));

        return new ParsedTransaction($type, $amount, $desc, $date, $id, provider: 'dana');
    }
}
