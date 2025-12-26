<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\AuditLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentAuditLogsWidget extends TableWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'audit.view';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('آخرین تغییرات')
            ->query(AuditLog::query()->latest('created_at'))
            ->columns([
                TextColumn::make('action')->label('عملیات'),
                TextColumn::make('actor.name')->label('کاربر'),
                TextColumn::make('subject_type')->label('نوع هدف')->limit(30),
                TextColumn::make('created_at')->label('زمان'),
            ])
            ->defaultPaginationPageOption(8)
            ->paginated([8]);
    }
}
