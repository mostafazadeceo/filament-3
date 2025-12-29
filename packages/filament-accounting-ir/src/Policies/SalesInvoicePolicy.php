<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\SalesInvoice;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class SalesInvoicePolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.sales.view');
    }

    public function view(SalesInvoice $invoice): bool
    {
        return $this->allow('accounting.sales.view', $invoice);
    }

    public function create(): bool
    {
        return $this->allow('accounting.sales.manage');
    }

    public function update(SalesInvoice $invoice): bool
    {
        return $this->allow('accounting.sales.manage', $invoice);
    }

    public function delete(SalesInvoice $invoice): bool
    {
        return $this->allow('accounting.sales.manage', $invoice);
    }
}
