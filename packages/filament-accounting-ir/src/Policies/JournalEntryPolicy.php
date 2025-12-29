<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\JournalEntry;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class JournalEntryPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.journal.view');
    }

    public function view(JournalEntry $entry): bool
    {
        return $this->allow('accounting.journal.view', $entry);
    }

    public function create(): bool
    {
        return $this->allow('accounting.journal.create');
    }

    public function update(JournalEntry $entry): bool
    {
        return $this->allow('accounting.journal.create', $entry);
    }

    public function delete(JournalEntry $entry): bool
    {
        return $this->allow('accounting.journal.void', $entry);
    }

    public function submit(JournalEntry $entry): bool
    {
        return $this->allow('accounting.journal.submit', $entry);
    }

    public function approve(JournalEntry $entry): bool
    {
        return $this->allow('accounting.journal.approve', $entry);
    }

    public function post(JournalEntry $entry): bool
    {
        return $this->allow('accounting.journal.post', $entry);
    }

    public function reverse(JournalEntry $entry): bool
    {
        return $this->allow('accounting.journal.reverse', $entry);
    }

    public function export(JournalEntry $entry): bool
    {
        return $this->allow('accounting.journal.export', $entry);
    }
}
