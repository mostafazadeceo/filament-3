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

class ImportContactsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $tenantId,
        public readonly int $importJobId,
    ) {}

    public function handle(): void
    {
        TenantContext::setTenant(Tenant::query()->find($this->tenantId));

        $job = SmsBulkImportJob::query()
            ->where('tenant_id', $this->tenantId)
            ->findOrFail($this->importJobId);

        $job->update(['status' => 'processing']);

        $rows = preg_split('/\r\n|\r|\n/', (string) Storage::disk('local')->get($job->input_path));
        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $lineNo => $row) {
            if (! $row || trim($row) === '') {
                continue;
            }

            $columns = str_getcsv($row);
            $msisdn = trim((string) ($columns[0] ?? ''));
            $fullName = trim((string) ($columns[1] ?? ''));

            if ($msisdn === '') {
                $failed++;
                $errors[] = [$lineNo + 1, 'missing_msisdn'];
                continue;
            }

            SmsBulkContact::query()->updateOrCreate(
                [
                    'tenant_id' => $this->tenantId,
                    'phonebook_id' => $job->phonebook_id,
                    'msisdn' => $msisdn,
                ],
                [
                    'full_name' => $fullName !== '' ? $fullName : null,
                ],
            );

            $success++;
        }

        $errorPath = null;
        if ($errors !== []) {
            $errorPath = 'sms-bulk/import-errors-'.$job->getKey().'.csv';
            $lines = array_map(fn (array $item): string => implode(',', $item), $errors);
            Storage::disk('local')->put($errorPath, implode("\n", $lines));
        }

        $job->update([
            'status' => 'completed',
            'total_rows' => $success + $failed,
            'success_rows' => $success,
            'failed_rows' => $failed,
            'output_path' => $errorPath,
            'finished_at' => now(),
        ]);
    }
}
