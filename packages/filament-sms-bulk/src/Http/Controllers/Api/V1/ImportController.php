<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Jobs\ImportContactsJob;
use Haida\SmsBulk\Models\SmsBulkImportJob;
use Illuminate\Http\Request;

class ImportController extends ApiController
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'phonebook_id' => ['required', 'integer'],
            'input_path' => ['required', 'string'],
        ]);

        $job = SmsBulkImportJob::create([
            'tenant_id' => $this->tenantId(),
            'phonebook_id' => (int) $data['phonebook_id'],
            'type' => 'import',
            'status' => 'pending',
            'input_path' => $data['input_path'],
        ]);

        ImportContactsJob::dispatch($this->tenantId(), (int) $job->getKey());

        return $this->ok(['id' => $job->getKey()], 202);
    }

    public function show(int $id)
    {
        $job = SmsBulkImportJob::query()->where('tenant_id', $this->tenantId())->findOrFail($id);

        return $this->ok($job->toArray());
    }
}
