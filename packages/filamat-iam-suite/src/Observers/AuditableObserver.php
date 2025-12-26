<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Observers;

use Filamat\IamSuite\Services\AuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AuditableObserver
{
    public function created(Model $model): void
    {
        app(AuditService::class)->log('created', $model, ['after' => $model->getAttributes()]);
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        if ($changes === []) {
            return;
        }

        $before = Arr::only($model->getOriginal(), array_keys($changes));

        app(AuditService::class)->log('updated', $model, ['before' => $before, 'after' => $changes]);
    }

    public function deleted(Model $model): void
    {
        app(AuditService::class)->log('deleted', $model, ['before' => $model->getAttributes()]);
    }
}
