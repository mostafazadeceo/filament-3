<?php

namespace Haida\FilamentPettyCashIr\Application\DTO;

use Vendor\FilamentAccountingIr\Models\JournalEntry;

final class PostingResult
{
    public function __construct(
        public readonly ?JournalEntry $journalEntry,
        public readonly ?int $treasuryTransactionId = null
    ) {}
}
