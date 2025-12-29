<?php

namespace Vendor\FilamentAccountingIr\Listeners;

use Illuminate\Events\Dispatcher;
use Vendor\FilamentAccountingIr\Events\EInvoiceFailed;
use Vendor\FilamentAccountingIr\Events\EInvoiceSent;
use Vendor\FilamentAccountingIr\Events\InventoryDocPosted;
use Vendor\FilamentAccountingIr\Events\JournalEntryPosted;
use Vendor\FilamentAccountingIr\Events\PurchaseInvoicePosted;
use Vendor\FilamentAccountingIr\Events\SalesInvoicePosted;
use Vendor\FilamentAccountingIr\Events\TreasuryTransactionPosted;
use Vendor\FilamentAccountingIr\Events\VatReportSubmitted;
use Vendor\FilamentAccountingIr\Services\Webhooks\AccountingWebhookDispatcher;

class AccountingEventSubscriber
{
    public function __construct(protected AccountingWebhookDispatcher $dispatcher) {}

    public function handleJournalEntryPosted(JournalEntryPosted $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleSalesInvoicePosted(SalesInvoicePosted $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handlePurchaseInvoicePosted(PurchaseInvoicePosted $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleTreasuryTransactionPosted(TreasuryTransactionPosted $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleInventoryDocPosted(InventoryDocPosted $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleVatReportSubmitted(VatReportSubmitted $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleEInvoiceSent(EInvoiceSent $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleEInvoiceFailed(EInvoiceFailed $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(JournalEntryPosted::class, [self::class, 'handleJournalEntryPosted']);
        $events->listen(SalesInvoicePosted::class, [self::class, 'handleSalesInvoicePosted']);
        $events->listen(PurchaseInvoicePosted::class, [self::class, 'handlePurchaseInvoicePosted']);
        $events->listen(TreasuryTransactionPosted::class, [self::class, 'handleTreasuryTransactionPosted']);
        $events->listen(InventoryDocPosted::class, [self::class, 'handleInventoryDocPosted']);
        $events->listen(VatReportSubmitted::class, [self::class, 'handleVatReportSubmitted']);
        $events->listen(EInvoiceSent::class, [self::class, 'handleEInvoiceSent']);
        $events->listen(EInvoiceFailed::class, [self::class, 'handleEInvoiceFailed']);
    }
}
