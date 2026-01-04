<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapCampaignResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\MailtrapCore\Support\MailtrapLabels;

class MailtrapCampaignSendsRelationManager extends RelationManager
{
    protected static string $relationship = 'sends';

    protected static ?string $title = 'ارسال‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')->label('ایمیل')->searchable(),
                TextColumn::make('name')->label('نام')->toggleable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => MailtrapLabels::sendStatus($state)),
                TextColumn::make('sent_at')->label('زمان ارسال')->jalaliDateTime()->toggleable(),
                TextColumn::make('error_message')->label('خطا')->limit(50)->toggleable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
