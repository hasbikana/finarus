<?php

namespace App\Parsers;

use App\DTO\ParsedTransaction;
use Carbon\Carbon;

class OvoParser extends BaseParser
{
    public function provider(): string { return 'ovo'; }

    public function canParse(string $from, string $subject): bool
    {
        return str_contains(strtolower($from), 'ovo') || str_contains($subject, 'OVO');
    }

    public function parse(string $from, string $subject, string $body): ?ParsedTransaction
    {
        $amount = $this->extractAmount($body);
        if (!$amount) return null;

        $type = $this->isTopup($subject, $body) ? 'income' : 'expense';
        $desc = $this->extractDescription($subject, $body);
        $date = $this->extractDate($body) ?? Carbon::now();
        $id = 'ovo-' . md5($subject . $amount . $date->format('Y-m-d'));

        return new ParsedTransaction($type, $amount, $desc, $date, $id, provider: 'ovo');
    }
}
