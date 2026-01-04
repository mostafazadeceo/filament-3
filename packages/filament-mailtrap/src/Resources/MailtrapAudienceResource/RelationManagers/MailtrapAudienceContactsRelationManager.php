<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapAudienceResource\RelationManagers;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\MailtrapCore\Support\MailtrapLabels;

class MailtrapAudienceContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    protected static ?string $title = 'مخاطبان';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('email')
                ->label('ایمیل')
                ->email()
                ->required()
                ->maxLength(190),
            TextInput::make('name')
                ->label('نام')
                ->maxLength(190),
            Select::make('status')
                ->label('وضعیت')
                ->options([
                    'subscribed' => 'فعال',
                    'unsubscribed' => 'لغو عضویت',
                ])
                ->default('subscribed'),
            KeyValue::make('metadata')
                ->label('متادیتا')
                ->keyLabel('کلید')
                ->valueLabel('مقدار')
                ->columnSpanFull(),
        ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')->label('ایمیل')->searchable(),
                TextColumn::make('name')->label('نام')->toggleable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => MailtrapLabels::contactStatus($state)),
                TextColumn::make('unsubscribed_at')->label('لغو عضویت')->jalaliDateTime()->toggleable(),
                TextColumn::make('created_at')->label('ایجاد')->jalaliDateTime()->toggleable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $this->getOwnerRecord()->tenant_id;

        if (($data['status'] ?? 'subscribed') === 'unsubscribed') {
            $data['unsubscribed_at'] = $data['unsubscribed_at'] ?? now();
        } else {
            $data['unsubscribed_at'] = null;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? 'subscribed') === 'unsubscribed') {
            $data['unsubscribed_at'] = $data['unsubscribed_at'] ?? now();
        } else {
            $data['unsubscribed_at'] = null;
        }

        return $data;
    }
}
