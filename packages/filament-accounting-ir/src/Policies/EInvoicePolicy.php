<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\EInvoice;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class EInvoicePolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.einvoice.view');
    }

    public function view(EInvoice $record): bool
    {
        return $this->allow('accounting.einvoice.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.einvoice.manage');
    }

    public function update(EInvoice $record): bool
    {
        return $this->allow('accounting.einvoice.manage', $record);
    }

    public function delete(EInvoice $record): bool
    {
        return $this->allow('accounting.einvoice.manage', $record);
    }

    public function restore(EInvoice $record): bool
    {
        return $this->allow('accounting.einvoice.manage', $record);
    }

    public function forceDelete(EInvoice $record): bool
    {
        return $this->allow('accounting.einvoice.manage', $record);
    }
}
