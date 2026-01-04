<?php

namespace Haida\FilamentMeetings\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Haida\FilamentMeetings\Models\Meeting;
use Illuminate\Database\Eloquent\Builder;

class TodayMeetingsWidget extends TableWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'meetings.view';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $userId = auth()->id();

        return $table
            ->heading('جلسات امروز من')
            ->query(
                Meeting::query()
                    ->whereDate('scheduled_at', now()->toDateString())
                    ->when($userId, function (Builder $query) use ($userId) {
                        $query->where('organizer_id', $userId)
                            ->orWhereHas('attendees', fn (Builder $attendee) => $attendee->where('user_id', $userId));
                    })
                    ->latest('scheduled_at')
            )
            ->columns([
                TextColumn::make('title')->label('عنوان')->searchable(),
                TextColumn::make('scheduled_at')->label('زمان')->dateTime('H:i'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft' => 'پیش‌نویس',
                        'scheduled' => 'برنامه‌ریزی شده',
                        'running' => 'در حال برگزاری',
                        'completed' => 'تکمیل شده',
                        'archived' => 'بایگانی شده',
                        default => $state,
                    }),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5]);
    }
}
