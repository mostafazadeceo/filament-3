<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\PermissionResource\Pages;

use Filamat\IamSuite\Filament\Resources\PermissionResource;
use Filamat\IamSuite\Services\AuditService;
use Filament\Resources\Pages\EditRecord;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    protected ?string $reason = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->reason = (string) ($data['reason'] ?? '');
        unset($data['reason']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record) {
            app(AuditService::class)->log('permission.updated', $this->record, [
                'reason' => $this->reason,
            ]);
        }
    }
}
