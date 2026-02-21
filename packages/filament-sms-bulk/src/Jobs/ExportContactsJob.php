<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Models\SmsBulkContact;
use Haida\SmsBulk\Models\SmsBulkImportJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportContactsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $tenantId,
        public readonly int $exportJobId,
    ) {}

    public function handle(): void
    {
        TenantContext::setTenant(Tenant::query()->find($this->tenantId));

        $job = SmsBulkImportJob::query()
            ->where('tenant_id', $this->tenantId)
            ->findOrFail($this->exportJobId);

        $job->update(['status' => 'processing']);

        $contacts = SmsBulkContact::query()
            ->where('tenant_id', $this->tenantId)
            ->where('phonebook_id', $job->phonebook_id)
            ->orderBy('id')
            ->get(['msisdn', 'full_name']);

        $lines = ['msisdn,full_name'];
        foreach ($contacts as $contact) {
            $lines[] = sprintf('%s,%s', $contact->msisdn, str_replace(',', ' ', (string) $contact->full_name));
        }

        $path = 'sms-bulk/exports-'.$job->getKey().'.csv';
        Storage::disk('local')->put($path, implode("\n", $lines));

        $job->update([
            'status' => 'completed',
            'output_path' => $path,
            'total_rows' => $contacts->count(),
            'success_rows' => $contacts->count(),
            'failed_rows' => 0,
            'finished_at' => now(),
        ]);
    }
}
