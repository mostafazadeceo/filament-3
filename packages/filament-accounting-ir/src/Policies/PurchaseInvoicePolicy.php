<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\PurchaseInvoice;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class PurchaseInvoicePolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.purchase.view');
    }

    public function view(PurchaseInvoice $invoice): bool
    {
        return $this->allow('accounting.purchase.view', $invoice);
    }

    public function create(): bool
    {
        return $this->allow('accounting.purchase.manage');
    }

    public function update(PurchaseInvoice $invoice): bool
    {
        return $this->allow('accounting.purchase.manage', $invoice);
    }

    public function delete(PurchaseInvoice $invoice): bool
    {
        return $this->allow('accounting.purchase.manage', $invoice);
    }
}
