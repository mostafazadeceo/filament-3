<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\SmsBulk\Filament\Resources\CampaignRecipientResource\Pages\ListCampaignRecipients;
use Haida\SmsBulk\Models\SmsBulkCampaignRecipient;

class CampaignRecipientResource extends Resource
{
    protected static ?string $model = SmsBulkCampaignRecipient::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.reports');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.report.view', 'sms-bulk.report.export']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('campaign.name')->label(__('filament-sms-bulk::messages.nav.campaigns'))->searchable(),
            TextColumn::make('msisdn')->label(__('filament-sms-bulk::messages.fields.msisdn'))->searchable(),
            TextColumn::make('status')->label(__('filament-sms-bulk::messages.fields.status'))->badge(),
            TextColumn::make('cost')->label(__('filament-sms-bulk::messages.fields.cost_final')),
            TextColumn::make('delivered_at')->label(__('filament-sms-bulk::messages.fields.delivered_at'))->jalaliDateTime(),
            TextColumn::make('updated_at')->label(__('filament-sms-bulk::messages.fields.updated_at'))->jalaliDateTime(),
        ])->actions([
            Action::make('export_csv')
                ->label(__('filament-sms-bulk::messages.actions.export_csv'))
                ->url(fn () => route('sms-bulk.reports.export.csv'))
                ->openUrlInNewTab(),
        ])->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaignRecipients::route('/'),
        ];
    }
}
