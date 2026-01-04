<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\Pages;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Haida\FilamentAiCore\Models\AiFeedback;
use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource;
use Haida\FilamentWorkhub\Jobs\GenerateAiFieldRunJob;
use Haida\FilamentWorkhub\Models\AiSummary;
use Haida\FilamentWorkhub\Models\CustomField;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\WorkhubAiService;
use Haida\FilamentWorkhub\Services\WorkItemCreator;

class ViewWorkItem extends ViewRecord
{
    protected static string $resource = WorkItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ai_personal_summary')
                ->label('خلاصه فقط برای من')
                ->icon('heroicon-o-sparkles')
                ->visible(fn (WorkItem $record) => IamAuthorization::allows('workhub.ai.use', IamAuthorization::resolveTenantFromRecord($record)))
                ->form([
                    Toggle::make('include_comments')
                        ->label('در نظر گرفتن دیدگاه‌ها')
                        ->default(true),
                ])
                ->action(function (WorkItem $record, array $data, WorkhubAiService $service) {
                    $ttlMinutes = (int) config('filament-workhub.ai.personal_summary_ttl_minutes', 30);
                    $payload = $service->summarizeWorkItem(
                        $record->loadMissing(['project', 'status', 'assignee']),
                        'personal_summary',
                        'ttl',
                        ['include_comments' => (bool) ($data['include_comments'] ?? true), 'ttl_minutes' => $ttlMinutes],
                        auth()->user()
                    );

                    $this->notifyFromResult($payload['result'], 'خلاصه شخصی تولید شد.');
                }),
            Action::make('ai_shared_summary')
                ->label('خلاصه قابل اشتراک')
                ->icon('heroicon-o-share')
                ->visible(fn (WorkItem $record) => IamAuthorization::allows('workhub.ai.share', IamAuthorization::resolveTenantFromRecord($record)))
                ->form([
                    Toggle::make('include_comments')
                        ->label('در نظر گرفتن دیدگاه‌ها')
                        ->default(true),
                    Toggle::make('notify_watchers')
                        ->label('ارسال برای دنبال‌کننده‌ها')
                        ->default(false),
                ])
                ->action(function (WorkItem $record, array $data, WorkhubAiService $service) {
                    $payload = $service->summarizeWorkItem(
                        $record->loadMissing(['project', 'status', 'assignee']),
                        'shared_summary',
                        'shared',
                        ['include_comments' => (bool) ($data['include_comments'] ?? true)],
                        auth()->user()
                    );

                    $this->notifyFromResult($payload['result'], 'خلاصه اشتراکی تولید شد.');

                    if (($data['notify_watchers'] ?? false) && $payload['summary']) {
                        $this->dispatchAiSummaryTrigger($record);
                    }
                }),
            Action::make('ai_thread_summary')
                ->label('خلاصه گفتگو')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->visible(fn (WorkItem $record) => IamAuthorization::allows('workhub.ai.use', IamAuthorization::resolveTenantFromRecord($record)))
                ->action(function (WorkItem $record, WorkhubAiService $service) {
                    $ttlMinutes = (int) config('filament-workhub.ai.thread_summary_ttl_minutes', 60);
                    $payload = $service->summarizeThread($record, 'ttl', ['ttl_minutes' => $ttlMinutes], auth()->user());

                    $this->notifyFromResult($payload['result'], 'خلاصه گفتگو تولید شد.');
                }),
            Action::make('ai_create_task_from_summary')
                ->label('ایجاد تسک از خلاصه')
                ->icon('heroicon-o-document-plus')
                ->visible(function (WorkItem $record) {
                    return IamAuthorization::allows('workhub.ai.use', IamAuthorization::resolveTenantFromRecord($record))
                        && IamAuthorization::allows('workhub.work_item.manage', IamAuthorization::resolveTenantFromRecord($record))
                        && $record->aiSummaries->isNotEmpty();
                })
                ->form([
                    Textarea::make('title')
                        ->label('عنوان')
                        ->rows(2)
                        ->required()
                        ->default(fn (WorkItem $record) => 'پیگیری: '.$record->title),
                    Textarea::make('description')
                        ->label('توضیحات')
                        ->rows(6)
                        ->default(function (WorkItem $record) {
                            $summary = $record->aiSummaries->first();
                            if (! $summary) {
                                return '';
                            }

                            return $this->formatSummaryForTask((array) $summary->summary_json);
                        }),
                ])
                ->action(function (WorkItem $record, array $data, WorkItemCreator $creator) {
                    $newItem = $creator->create([
                        'tenant_id' => $record->tenant_id,
                        'project_id' => $record->project_id,
                        'title' => $data['title'],
                        'description' => $data['description'] ?? null,
                        'priority' => $record->priority ?? 'medium',
                        'reporter_id' => auth()->id(),
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);

                    Notification::make()
                        ->title('تسک جدید ایجاد شد.')
                        ->success()
                        ->send();
                }),
            Action::make('ai_progress_update')
                ->label('گزارش پیشرفت')
                ->icon('heroicon-o-chart-bar')
                ->visible(fn (WorkItem $record) => IamAuthorization::allows('workhub.ai.use', IamAuthorization::resolveTenantFromRecord($record)))
                ->form([
                    Select::make('window_days')
                        ->label('بازه')
                        ->options([
                            1 => 'امروز',
                            7 => '۷ روز اخیر',
                            30 => '۳۰ روز اخیر',
                        ])
                        ->default(7)
                        ->required(),
                ])
                ->action(function (WorkItem $record, array $data, WorkhubAiService $service) {
                    $windowDays = (int) ($data['window_days'] ?? 7);
                    $ttlMinutes = (int) config('filament-workhub.ai.progress_ttl_minutes', 30);
                    $payload = $service->progressUpdate($record, $windowDays, ['ttl_minutes' => $ttlMinutes], auth()->user());

                    $this->notifyFromResult($payload['result'], 'گزارش پیشرفت تولید شد.');
                }),
            Action::make('ai_find_similar')
                ->label('پیدا کردن مشابه')
                ->icon('heroicon-o-magnifying-glass')
                ->visible(fn (WorkItem $record) => IamAuthorization::allows('workhub.ai.use', IamAuthorization::resolveTenantFromRecord($record)))
                ->action(function (WorkItem $record, WorkhubAiService $service) {
                    $limit = (int) config('filament-workhub.ai.similarity_limit', 5);
                    $items = $service->findSimilarTasks($record, $limit);
                    if ($items->isEmpty()) {
                        Notification::make()
                            ->title('مورد مشابهی پیدا نشد.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $lines = $items->map(fn (WorkItem $item) => $item->key.' - '.$item->title)->implode("\n");
                    Notification::make()
                        ->title('موارد مشابه')
                        ->body($lines)
                        ->success()
                        ->send();
                }),
            Action::make('ai_generate_subtasks')
                ->label('ایجاد ساب‌تسک‌ها')
                ->icon('heroicon-o-list-bullet')
                ->visible(fn (WorkItem $record) => IamAuthorization::allows('workhub.ai.use', IamAuthorization::resolveTenantFromRecord($record))
                    && IamAuthorization::allows('workhub.work_item.manage', IamAuthorization::resolveTenantFromRecord($record)))
                ->form([
                    Textarea::make('items')
                        ->label('ساب‌تسک‌ها (هر خط یک مورد)')
                        ->rows(6)
                        ->default(function (WorkItem $record) {
                            $payload = app(WorkhubAiService::class)->suggestSubtasks($record, 8, auth()->user());
                            if (! $payload['result']->ok) {
                                return '';
                            }

                            return collect($payload['suggestions'])
                                ->pluck('title')
                                ->implode("\n");
                        }),
                ])
                ->action(function (WorkItem $record, array $data, WorkhubAiService $service) {
                    $lines = preg_split('/\\r?\\n/', (string) ($data['items'] ?? '')) ?: [];
                    $items = collect($lines)
                        ->map(fn ($line) => trim(ltrim($line, "- \t")))
                        ->filter()
                        ->map(fn ($line) => ['title' => $line])
                        ->values()
                        ->all();

                    $created = $service->createSubtasks($record, $items, auth()->user());
                    if ($created === []) {
                        Notification::make()
                            ->title('ساب‌تسکی ایجاد نشد.')
                            ->warning()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('ساب‌تسک‌ها ایجاد شد.')
                        ->success()
                        ->send();
                }),
            Action::make('ai_generate_fields')
                ->label('تولید فیلدهای هوش')
                ->icon('heroicon-o-cpu-chip')
                ->visible(fn (WorkItem $record) => IamAuthorization::allows('workhub.ai.fields.manage', IamAuthorization::resolveTenantFromRecord($record)))
                ->requiresConfirmation()
                ->modalHeading('تولید فیلدهای هوش مصنوعی')
                ->modalSubmitActionLabel('شروع')
                ->modalCancelActionLabel('انصراف')
                ->action(function (WorkItem $record) {
                    $fields = CustomField::query()
                        ->where('scope', 'work_item')
                        ->where('type', 'ai_field')
                        ->where('is_active', true)
                        ->get();

                    if ($fields->isEmpty()) {
                        Notification::make()
                            ->title('فیلد هوش فعالی یافت نشد.')
                            ->warning()
                            ->send();

                        return;
                    }

                    foreach ($fields as $field) {
                        GenerateAiFieldRunJob::dispatch(
                            $record->tenant_id,
                            $field->getKey(),
                            $record->getKey(),
                            auth()->id()
                        );
                    }

                    Notification::make()
                        ->title('تولید فیلدهای هوش در صف قرار گرفت.')
                        ->success()
                        ->send();
                }),
            Action::make('ai_rate_summary')
                ->label('امتیازدهی')
                ->icon('heroicon-o-star')
                ->visible(fn (WorkItem $record) => IamAuthorization::allows('workhub.ai.use', IamAuthorization::resolveTenantFromRecord($record)))
                ->form([
                    Select::make('summary_id')
                        ->label('خلاصه')
                        ->options(function (WorkItem $record) {
                            return $record->aiSummaries
                                ->mapWithKeys(fn (AiSummary $summary) => [
                                    $summary->getKey() => $summary->type.' - '.(string) data_get($summary->summary_json, 'kind', 'summary'),
                                ])
                                ->toArray();
                        })
                        ->required(),
                    Select::make('rating')
                        ->label('امتیاز')
                        ->options([
                            1 => '۱',
                            2 => '۲',
                            3 => '۳',
                            4 => '۴',
                            5 => '۵',
                        ])
                        ->required(),
                    Textarea::make('note')
                        ->label('یادداشت')
                        ->rows(3)
                        ->nullable(),
                ])
                ->action(function (WorkItem $record, array $data) {
                    $summaryId = (int) ($data['summary_id'] ?? 0);
                    if (! $summaryId) {
                        return;
                    }

                    AiFeedback::query()->create([
                        'tenant_id' => $record->tenant_id,
                        'actor_id' => auth()->id(),
                        'module' => 'workhub',
                        'action_type' => 'summary',
                        'target_type' => AiSummary::class,
                        'target_id' => $summaryId,
                        'rating' => (int) $data['rating'],
                        'note' => $data['note'] ?? null,
                    ]);

                    Notification::make()
                        ->title('بازخورد ثبت شد.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function notifyFromResult($result, string $successMessage): void
    {
        if (! $result->ok) {
            Notification::make()
                ->title('خطا در پردازش هوش مصنوعی')
                ->body($result->error ?: 'درخواست ناموفق بود.')
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title($successMessage)
            ->success()
            ->send();
    }

    protected function dispatchAiSummaryTrigger(WorkItem $record): void
    {
        if (! class_exists(TriggerDispatcher::class)) {
            return;
        }

        $panelId = (string) config('filament-workhub.notifications.panel', 'tenant');
        if ($panelId === '') {
            return;
        }

        try {
            app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $record, 'workhub.ai.summary.created');
        } catch (\Throwable) {
            // keep UI resilient
        }
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    protected function formatSummaryForTask(array $summary): string
    {
        $sections = (array) ($summary['sections'] ?? []);
        if ($sections === []) {
            return (string) ($summary['text'] ?? '');
        }

        $chunks = [];
        foreach ($sections as $title => $items) {
            $lines = array_map(fn ($item) => '- '.(string) $item, (array) $items);
            $chunks[] = '## '.$title."\n".implode("\n", $lines);
        }

        return implode("\n\n", $chunks);
    }
}
