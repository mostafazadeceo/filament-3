<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\JournalEntryResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Vendor\FilamentAccountingIr\Filament\Resources\JournalEntryResource;
use Vendor\FilamentAccountingIr\Services\JournalEntryService;

class EditJournalEntry extends EditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submit')
                ->label('ارسال')
                ->visible(fn () => $this->record->status === 'draft' && auth()->user()?->can('submit', $this->record))
                ->action(fn () => app(JournalEntryService::class)->submit($this->record)),
            Action::make('approve')
                ->label('تایید')
                ->visible(fn () => $this->record->status === 'submitted' && auth()->user()?->can('approve', $this->record))
                ->action(fn () => app(JournalEntryService::class)->approve($this->record)),
            Action::make('post')
                ->label('قطعی')
                ->visible(fn () => $this->record->status === 'approved' && auth()->user()?->can('post', $this->record))
                ->action(fn () => app(JournalEntryService::class)->post($this->record)),
        ];
    }
}
