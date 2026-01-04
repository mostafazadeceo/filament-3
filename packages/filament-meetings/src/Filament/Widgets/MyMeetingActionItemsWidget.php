<?php

namespace Haida\FilamentMeetings\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Haida\FilamentMeetings\Models\MeetingActionItem;

class MyMeetingActionItemsWidget extends TableWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'meetings.view';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $userId = auth()->id();

        return $table
            ->heading('اقدام‌های من از جلسات')
            ->query(
                MeetingActionItem::query()
                    ->with('meeting')
                    ->when($userId, fn ($query) => $query->where('assignee_id', $userId))
                    ->where('status', '!=', 'done')
                    ->latest('created_at')
            )
            ->columns([
                TextColumn::make('meeting.title')->label('جلسه')->searchable(),
                TextColumn::make('title')->label('اقدام')->searchable(),
                TextColumn::make('due_date')->label('سررسید'),
                TextColumn::make('status')->label('وضعیت'),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5]);
    }
}
