<?php

namespace Haida\FilamentPettyCashIr\Filament\Resources\PettyCashExpenseResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Haida\FilamentPettyCashIr\Application\Services\PettyCashAiService;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashExpenseResource;

class EditPettyCashExpense extends EditRecord
{
    protected static string $resource = PettyCashExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('apply_ai_suggestion')
                ->label('اعمال پیشنهاد هوشمند')
                ->icon('heroicon-o-sparkles')
                ->visible(fn () => app(PettyCashAiService::class)->aiEnabled() && app(PettyCashAiService::class)->canUseAi())
                ->action(function (): void {
                    $service = app(PettyCashAiService::class);
                    $suggestion = $service->applyExpenseSuggestion($this->getRecord(), auth()->id());

                    if (! $suggestion) {
                        Notification::make()
                            ->title('پیشنهادی برای اعمال وجود ندارد.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->refreshFormData(['category_id', 'description']);

                    Notification::make()
                        ->title('پیشنهاد اعمال شد.')
                        ->success()
                        ->send();
                }),
            Action::make('reject_ai_suggestion')
                ->label('رد پیشنهاد هوشمند')
                ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->visible(fn () => app(PettyCashAiService::class)->aiEnabled() && app(PettyCashAiService::class)->canUseAi())
                ->action(function (): void {
                    $service = app(PettyCashAiService::class);
                    $suggestion = $service->rejectExpenseSuggestion($this->getRecord(), auth()->id());

                    if (! $suggestion) {
                        Notification::make()
                            ->title('پیشنهادی برای رد وجود ندارد.')
                            ->warning()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('پیشنهاد رد شد.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
