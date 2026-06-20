<?php

namespace App\DTO;

use Carbon\Carbon;

class ParsedTransaction
{
    public function __construct(
        public readonly string $type,
        public readonly float $amount,
        public readonly string $description,
        public readonly Carbon $transactionDate,
        public readonly string $messageId,
        public readonly ?string $merchant = null,
        public readonly ?string $provider = null,
    ) {}
}
