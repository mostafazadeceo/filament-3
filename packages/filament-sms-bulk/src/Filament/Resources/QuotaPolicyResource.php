<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\SmsBulk\Filament\Resources\QuotaPolicyResource\Pages\CreateQuotaPolicy;
use Haida\SmsBulk\Filament\Resources\QuotaPolicyResource\Pages\EditQuotaPolicy;
use Haida\SmsBulk\Filament\Resources\QuotaPolicyResource\Pages\ListQuotaPolicies;
use Haida\SmsBulk\Models\SmsBulkQuotaPolicy;

class QuotaPolicyResource extends Resource
{
    protected static ?string $model = SmsBulkQuotaPolicy::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.quotas');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.policy.view', 'sms-bulk.policy.manage']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('max_daily_recipients')->numeric()->label(__('filament-sms-bulk::messages.fields.max_daily_recipients')),
                TextInput::make('max_monthly_recipients')->numeric()->label(__('filament-sms-bulk::messages.fields.max_monthly_recipients')),
                TextInput::make('max_daily_spend')->numeric()->label(__('filament-sms-bulk::messages.fields.max_daily_spend')),
                TextInput::make('max_monthly_spend')->numeric()->label(__('filament-sms-bulk::messages.fields.max_monthly_spend')),
                TextInput::make('requires_approval_over_amount')->numeric()->label(__('filament-sms-bulk::messages.fields.requires_approval_over_amount')),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('max_daily_recipients')->label(__('filament-sms-bulk::messages.fields.max_daily_recipients')),
            TextColumn::make('max_monthly_recipients')->label(__('filament-sms-bulk::messages.fields.max_monthly_recipients')),
            TextColumn::make('max_daily_spend')->label(__('filament-sms-bulk::messages.fields.max_daily_spend')),
            TextColumn::make('max_monthly_spend')->label(__('filament-sms-bulk::messages.fields.max_monthly_spend')),
            TextColumn::make('requires_approval_over_amount')->label(__('filament-sms-bulk::messages.fields.requires_approval_over_amount')),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotaPolicies::route('/'),
            'create' => CreateQuotaPolicy::route('/create'),
            'edit' => EditQuotaPolicy::route('/{record}/edit'),
        ];
    }
}
