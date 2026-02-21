<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\SmsBulk\Filament\Resources\CampaignResource\Pages\CreateCampaign;
use Haida\SmsBulk\Filament\Resources\CampaignResource\Pages\EditCampaign;
use Haida\SmsBulk\Filament\Resources\CampaignResource\Pages\ListCampaigns;
use Haida\SmsBulk\Jobs\EnqueueCampaignJob;
use Haida\SmsBulk\Jobs\SyncReportsJob;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Models\SmsBulkQuietHoursProfile;

class CampaignResource extends Resource
{
    protected static ?string $model = SmsBulkCampaign::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.campaigns');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.campaign.view', 'sms-bulk.campaign.manage']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament-sms-bulk::messages.sections.campaign'))
                ->schema([
                    Select::make('provider_connection_id')
                        ->label(__('filament-sms-bulk::messages.nav.connections'))
                        ->options(fn () => SmsBulkProviderConnection::query()->pluck('display_name', 'id'))
                        ->required()
                        ->searchable(),
                    TextInput::make('name')->label(__('filament-sms-bulk::messages.fields.name'))->required(),
                    Select::make('mode')->label(__('filament-sms-bulk::messages.fields.mode'))->options([
                        'standard' => 'standard',
                        'pattern' => 'pattern',
                        'phonebook' => 'phonebook',
                        'file' => 'file',
                        'geo' => 'geo',
                    ])->required(),
                    Select::make('language')->label(__('filament-sms-bulk::messages.fields.language'))->options(['fa' => 'fa', 'en' => 'en', 'ar' => 'ar'])->default('fa'),
                    Select::make('encoding')->label(__('filament-sms-bulk::messages.fields.encoding'))->options(['auto' => 'auto', 'gsm' => 'gsm', 'unicode' => 'unicode'])->default('auto'),
                    TextInput::make('sender')->label(__('filament-sms-bulk::messages.fields.sender'))->required(),
                    DateTimePicker::make('schedule_at')->label(__('filament-sms-bulk::messages.fields.schedule_at')),
                    Select::make('quiet_hours_profile_id')
                        ->label(__('filament-sms-bulk::messages.nav.quiet_hours'))
                        ->options(fn () => SmsBulkQuietHoursProfile::query()->pluck('name', 'id'))
                        ->searchable(),
                    Textarea::make('payload_snapshot.message')->label(__('filament-sms-bulk::messages.fields.message'))->required()->columnSpanFull(),
                    Textarea::make('payload_snapshot.recipients_text')
                        ->label(__('filament-sms-bulk::messages.fields.recipients'))
                        ->helperText(__('filament-sms-bulk::messages.helpers.recipients_one_per_line'))
                        ->afterStateHydrated(function ($component, $state, ?SmsBulkCampaign $record): void {
                            if (! $record) {
                                return;
                            }

                            $component->state(implode("\n", (array) (($record->payload_snapshot['recipients'] ?? []))));
                        })
                        ->dehydrateStateUsing(fn (?string $state): array => ['recipients' => array_values(array_filter(array_map('trim', explode("\n", (string) $state))))])
                        ->columnSpanFull(),
                    Select::make('approval_state')->label(__('filament-sms-bulk::messages.fields.approval_state'))->options([
                        'draft' => 'draft',
                        'pending' => 'pending',
                        'approved' => 'approved',
                        'rejected' => 'rejected',
                    ])->default('draft')->required(),
                    Select::make('status')->label(__('filament-sms-bulk::messages.fields.status'))->options([
                        'draft' => 'draft',
                        'queued' => 'queued',
                        'sending' => 'sending',
                        'paused' => 'paused',
                        'cancelled' => 'cancelled',
                        'completed' => 'completed',
                        'failed' => 'failed',
                    ])->default('draft')->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('filament-sms-bulk::messages.fields.name'))->searchable(),
            TextColumn::make('mode')->label(__('filament-sms-bulk::messages.fields.mode'))->badge(),
            TextColumn::make('approval_state')->label(__('filament-sms-bulk::messages.fields.approval_state'))->badge(),
            TextColumn::make('status')->label(__('filament-sms-bulk::messages.fields.status'))->badge(),
            TextColumn::make('cost_estimate')->label(__('filament-sms-bulk::messages.fields.cost_estimate')),
            TextColumn::make('schedule_at')->label(__('filament-sms-bulk::messages.fields.schedule_at'))->jalaliDateTime(),
        ])->actions([
            Action::make('submit_campaign')
                ->label(__('filament-sms-bulk::messages.actions.submit_campaign'))
                ->action(fn (SmsBulkCampaign $record) => EnqueueCampaignJob::dispatch($record->tenant_id, (int) $record->getKey())),
            Action::make('pause_campaign')
                ->label(__('filament-sms-bulk::messages.actions.pause_campaign'))
                ->action(fn (SmsBulkCampaign $record) => $record->update(['status' => 'paused'])),
            Action::make('resume_campaign')
                ->label(__('filament-sms-bulk::messages.actions.resume_campaign'))
                ->action(fn (SmsBulkCampaign $record) => $record->update(['status' => 'queued'])),
            Action::make('cancel_campaign')
                ->label(__('filament-sms-bulk::messages.actions.cancel_campaign'))
                ->action(fn (SmsBulkCampaign $record) => $record->update(['status' => 'cancelled'])),
            Action::make('sync_reports')
                ->label(__('filament-sms-bulk::messages.actions.sync_reports'))
                ->action(fn (SmsBulkCampaign $record) => SyncReportsJob::dispatch($record->tenant_id, (int) $record->getKey())),
            EditAction::make(),
            DeleteAction::make(),
        ])->defaultSort('updated_at', 'desc');
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data = self::normalizePayloadRecipients($data);
        $data['idempotency_key'] = $data['idempotency_key'] ?? sha1(json_encode($data['payload_snapshot'] ?? []) ?: '');

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        return self::normalizePayloadRecipients($data);
    }

    /** @param array<string,mixed> $data */
    protected static function normalizePayloadRecipients(array $data): array
    {
        if (isset($data['payload_snapshot']['recipients']) && is_array($data['payload_snapshot']['recipients'])) {
            return $data;
        }

        $value = $data['payload_snapshot'] ?? [];
        if (isset($value['recipients']) && is_array($value['recipients'])) {
            $data['payload_snapshot'] = $value;
        }

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaigns::route('/'),
            'create' => CreateCampaign::route('/create'),
            'edit' => EditCampaign::route('/{record}/edit'),
        ];
    }
}
