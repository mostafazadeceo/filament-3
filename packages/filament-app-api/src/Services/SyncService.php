<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAppApi\Models\AppAttendanceRecord;
use Haida\FilamentAppApi\Models\AppSupportMessage;
use Haida\FilamentAppApi\Models\AppSupportTicket;
use Haida\FilamentAppApi\Models\AppSyncChange;
use Haida\FilamentAppApi\Models\AppTask;
use Illuminate\Support\Arr;

class SyncService
{
    public function recordChange(string $module, string $entity, string $recordId, string $action, array $payload = []): AppSyncChange
    {
        return AppSyncChange::create([
            'tenant_id' => TenantContext::getTenantId(),
            'module' => $module,
            'entity' => $entity,
            'record_id' => $recordId,
            'action' => $action,
            'payload' => $payload,
            'occurred_at' => now(),
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array{id: string, status: string, message?: string}>
     */
    public function push(array $items): array
    {
        $results = [];

        foreach ($items as $item) {
            $id = (string) ($item['id'] ?? '');
            $module = (string) ($item['module'] ?? '');
            $action = (string) ($item['action'] ?? '');
            $payload = (array) ($item['payload'] ?? []);

            $recordId = (string) ($payload['record_id'] ?? $payload['id'] ?? $id);
            $exists = AppSyncChange::query()
                ->where('tenant_id', TenantContext::getTenantId())
                ->where('module', $module)
                ->where('record_id', $recordId)
                ->where('action', $action)
                ->exists();

            if ($exists) {
                $results[] = ['id' => $id, 'status' => 'accepted'];
                continue;
            }

            try {
                $this->handleItem($module, $action, $recordId, $payload);
                $results[] = ['id' => $id, 'status' => 'accepted'];
            } catch (\Throwable $exception) {
                $results[] = ['id' => $id, 'status' => 'failed', 'message' => $exception->getMessage()];
            }
        }

        return $results;
    }

    /**
     * @return array{next_cursor: string, changes: array<int, array<string, mixed>>}
     */
    public function pull(?string $cursor): array
    {
        try {
            $cursorTime = $cursor ? \Carbon\Carbon::parse($cursor) : now()->subYears(5);
        } catch (\Throwable $exception) {
            $cursorTime = now()->subYears(5);
        }
        $limit = (int) config('filament-app-api.sync.pull_limit', 200);

        $changes = AppSyncChange::query()
            ->where('tenant_id', TenantContext::getTenantId())
            ->where('occurred_at', '>', $cursorTime)
            ->orderBy('occurred_at')
            ->limit($limit)
            ->get();

        $nextCursor = $changes->last()?->occurred_at?->toISOString() ?? now()->toISOString();

        return [
            'next_cursor' => $nextCursor,
            'changes' => $changes->map(fn (AppSyncChange $change) => [
                'module' => $change->module,
                'entity' => $change->entity,
                'id' => $change->record_id,
                'action' => $change->action,
                'payload' => $change->payload ?? [],
                'updated_at' => $change->occurred_at?->toISOString(),
            ])->all(),
        ];
    }

    private function handleItem(string $module, string $action, string $recordId, array $payload): void
    {
        if ($module === 'tasks') {
            $clientId = $recordId !== '' ? $recordId : null;
            $taskQuery = AppTask::query()->where('tenant_id', TenantContext::getTenantId());
            if ($clientId) {
                $taskQuery->where('client_id', $clientId);
            }

            $task = $taskQuery->first();
            if (! $task) {
                $task = new AppTask([
                    'tenant_id' => TenantContext::getTenantId(),
                    'client_id' => $clientId,
                ]);
            }

            $task->fill([
                'user_id' => auth()->id(),
                'title' => (string) Arr::get($payload, 'title', 'بدون عنوان'),
                'status' => (string) Arr::get($payload, 'status', 'open'),
                'meta' => Arr::get($payload, 'meta', []),
            ]);
            $task->save();
            $this->recordChange('tasks', 'task', (string) $task->getKey(), 'upsert', [
                'title' => $task->title,
                'status' => $task->status,
            ]);
            return;
        }

        if ($module === 'attendance') {
            $record = AppAttendanceRecord::create([
                'tenant_id' => TenantContext::getTenantId(),
                'user_id' => auth()->id(),
                'type' => (string) Arr::get($payload, 'type', $action),
                'status' => 'pending',
                'clocked_at' => Arr::get($payload, 'clocked_at', now()),
                'meta' => Arr::get($payload, 'meta', []),
            ]);

            $this->recordChange('attendance', 'record', (string) $record->getKey(), 'upsert', [
                'status' => $record->status,
                'clocked_at' => $record->clocked_at?->toISOString(),
            ]);
            return;
        }

        if ($module === 'support') {
            if ($action === 'ticket.create') {
                $ticket = AppSupportTicket::create([
                    'tenant_id' => TenantContext::getTenantId(),
                    'user_id' => auth()->id(),
                    'subject' => (string) Arr::get($payload, 'subject', 'بدون عنوان'),
                    'priority' => (string) Arr::get($payload, 'priority', 'normal'),
                    'status' => 'open',
                    'latest_message_at' => now(),
                ]);

                $this->recordChange('support', 'ticket', (string) $ticket->getKey(), 'upsert', [
                    'subject' => $ticket->subject,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                ]);
                return;
            }

            if ($action === 'message.create') {
                $ticketId = (int) Arr::get($payload, 'ticket_id', 0);
                $ticket = $ticketId ? AppSupportTicket::query()->find($ticketId) : null;
                if (! $ticket) {
                    throw new \RuntimeException('تیکت یافت نشد.');
                }

                $message = AppSupportMessage::create([
                    'tenant_id' => TenantContext::getTenantId(),
                    'ticket_id' => $ticket->getKey(),
                    'user_id' => auth()->id(),
                    'body' => (string) Arr::get($payload, 'body', ''),
                    'type' => (string) Arr::get($payload, 'type', 'text'),
                    'meta' => Arr::get($payload, 'meta', []),
                ]);

                $ticket->forceFill(['latest_message_at' => now()])->save();
                $this->recordChange('support', 'message', (string) $message->getKey(), 'upsert', [
                    'ticket_id' => $ticket->getKey(),
                    'type' => $message->type,
                ]);
                return;
            }
        }

        if ($module === 'pos') {
            $this->recordChange('pos', 'order', $recordId, 'upsert', $payload);
            return;
        }

        if ($module === 'meetings') {
            $this->recordChange('meetings', 'meeting', $recordId, 'upsert', $payload);
            return;
        }

        if ($module === 'support') {
            $this->recordChange('support', 'ticket', $recordId, 'upsert', $payload);
            return;
        }

        $this->recordChange($module, 'item', $recordId, 'upsert', $payload);
    }
}
