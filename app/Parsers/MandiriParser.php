<?php

namespace App\Parsers;

use App\DTO\ParsedTransaction;
use Carbon\Carbon;

class MandiriParser extends BaseParser
{
    public function provider(): string { return 'mandiri'; }

    public function canParse(string $from, string $subject): bool
    {
        $f = strtolower($from);
        return str_contains($f, 'mandiri') || str_contains($f, 'bankmandiri');
    }

    public function parse(string $from, string $subject, string $body): ?ParsedTransaction
    {
        $amount = $this->extractAmount($body);
        if (!$amount) return null;

        $type = $this->isDebit($body) ? 'expense' : 'income';
        $desc = $this->extractDescription($subject, $body);
        $date = $this->extractDate($body) ?? Carbon::now();
        $id = 'mandiri-' . md5($subject . $amount . $date->format('Y-m-d'));

        return new ParsedTransaction($type, $amount, $desc, $date, $id, provider: 'mandiri');
    }
}
