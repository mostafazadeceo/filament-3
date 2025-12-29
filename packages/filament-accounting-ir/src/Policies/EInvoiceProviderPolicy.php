<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\EInvoiceProvider;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class EInvoiceProviderPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.einvoice.view');
    }

    public function view(EInvoiceProvider $record): bool
    {
        return $this->allow('accounting.einvoice.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.einvoice.manage');
    }

    public function update(EInvoiceProvider $record): bool
    {
        return $this->allow('accounting.einvoice.manage', $record);
    }

    public function delete(EInvoiceProvider $record): bool
    {
        return $this->allow('accounting.einvoice.manage', $record);
    }

    public function restore(EInvoiceProvider $record): bool
    {
        return $this->allow('accounting.einvoice.manage', $record);
    }

    public function forceDelete(EInvoiceProvider $record): bool
    {
        return $this->allow('accounting.einvoice.manage', $record);
    }
}
