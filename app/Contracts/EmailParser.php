<?php

namespace App\Contracts;

use App\DTO\ParsedTransaction;

interface EmailParser
{
    public function provider(): string;

    public function canParse(string $from, string $subject): bool;

    public function parse(string $from, string $subject, string $body): ?ParsedTransaction;
}
