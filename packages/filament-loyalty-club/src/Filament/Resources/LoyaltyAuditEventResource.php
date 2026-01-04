<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyAuditEventResource\Pages\ListLoyaltyAuditEvents;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyAuditEventResource\Pages\ViewLoyaltyAuditEvent;
use Haida\FilamentLoyaltyClub\Models\LoyaltyAuditEvent;

class LoyaltyAuditEventResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.audit';

    protected static ?string $model = LoyaltyAuditEvent::class;

    protected static ?string $navigationLabel = 'ممیزی';

    protected static ?string $pluralModelLabel = 'ممیزی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(required: false),
                TextInput::make('action')->label('اقدام')->disabled(),
                TextInput::make('subject_type')->label('نوع هدف')->disabled(),
                TextInput::make('subject_id')->label('شناسه هدف')->disabled(),
                TextInput::make('occurred_at')->label('زمان')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('action')->label('اقدام')->searchable(),
                TextColumn::make('subject_type')->label('نوع هدف'),
                TextColumn::make('subject_id')->label('شناسه هدف'),
                TextColumn::make('occurred_at')->label('زمان'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyAuditEvents::route('/'),
            'view' => ViewLoyaltyAuditEvent::route('/{record}'),
        ];
    }
}
