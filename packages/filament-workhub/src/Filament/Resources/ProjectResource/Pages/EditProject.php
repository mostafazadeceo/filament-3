<?php

namespace Haida\FilamentWorkhub\Filament\Resources\ProjectResource\Pages;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Haida\FilamentWorkhub\Filament\Resources\ProjectResource;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Services\WorkhubAiService;
use Haida\FilamentWorkhub\Services\WorkItemCreator;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ai_executive_summary')
                ->label('خلاصه اجرایی')
                ->icon('heroicon-o-sparkles')
                ->visible(fn (Project $record) => IamAuthorization::allows('workhub.ai.project_reports.manage', IamAuthorization::resolveTenantFromRecord($record)))
                ->form([
                    Select::make('output')
                        ->label('خروجی')
                        ->options([
                            'project_update' => 'ذخیره به عنوان گزارش',
                            'work_item' => 'ایجاد آیتم کاری',
                        ])
                        ->default('project_update')
                        ->required(),
                ])
                ->action(function (Project $record, array $data, WorkhubAiService $service, WorkItemCreator $creator) {
                    $payload = $service->generateExecutiveSummary($record, [], auth()->user());
                    if (! $payload['result']->ok) {
                        $this->notifyFromResult($payload['result'], 'خلاصه اجرایی تولید شد.');

                        return;
                    }

                    if (($data['output'] ?? 'project_update') === 'work_item' && $payload['report']) {
                        $creator->create([
                            'tenant_id' => $record->tenant_id,
                            'project_id' => $record->getKey(),
                            'title' => 'خلاصه اجرایی: '.$record->name,
                            'description' => $payload['report']->body_markdown,
                            'priority' => 'medium',
                            'reporter_id' => auth()->id(),
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                        ]);
                    }

                    $this->notifyFromResult($payload['result'], 'خلاصه اجرایی تولید شد.');
                }),
            Action::make('ai_risk_report')
                ->label('گزارش ریسک')
                ->icon('heroicon-o-exclamation-triangle')
                ->visible(fn (Project $record) => IamAuthorization::allows('workhub.ai.project_reports.manage', IamAuthorization::resolveTenantFromRecord($record)))
                ->action(function (Project $record, WorkhubAiService $service) {
                    $payload = $service->generateExecutiveSummary($record, ['updated_since_days' => 30], auth()->user());
                    $this->notifyFromResult($payload['result'], 'گزارش ریسک تولید شد.');
                }),
            Action::make('ai_stuck_tasks')
                ->label('کارهای گیر کرده')
                ->icon('heroicon-o-clock')
                ->visible(fn (Project $record) => IamAuthorization::allows('workhub.ai.project_reports.manage', IamAuthorization::resolveTenantFromRecord($record)))
                ->action(function (Project $record, WorkhubAiService $service) {
                    $days = (int) config('filament-workhub.ai.stuck_days', 7);
                    $items = $service->stuckTasks($record, $days);
                    if ($items->isEmpty()) {
                        Notification::make()
                            ->title('کاری گیر نکرده است.')
                            ->success()
                            ->send();

                        return;
                    }

                    $lines = $items->map(fn ($item) => $item->key.' - '.$item->title)->implode("\n");
                    Notification::make()
                        ->title('کارهای گیر کرده')
                        ->body($lines)
                        ->warning()
                        ->send();
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
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
}
