<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\SmsBulk\Filament\Resources\QuietHoursProfileResource\Pages\CreateQuietHoursProfile;
use Haida\SmsBulk\Filament\Resources\QuietHoursProfileResource\Pages\EditQuietHoursProfile;
use Haida\SmsBulk\Filament\Resources\QuietHoursProfileResource\Pages\ListQuietHoursProfiles;
use Haida\SmsBulk\Models\SmsBulkQuietHoursProfile;

class QuietHoursProfileResource extends Resource
{
    protected static ?string $model = SmsBulkQuietHoursProfile::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.quiet_hours');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.policy.view', 'sms-bulk.policy.manage']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')->label(__('filament-sms-bulk::messages.fields.name'))->required(),
                TextInput::make('timezone')->label(__('filament-sms-bulk::messages.fields.timezone'))->default('Asia/Tehran')->required(),
                Select::make('allowed_days')
                    ->label(__('filament-sms-bulk::messages.fields.allowed_days'))
                    ->multiple()
                    ->options(['0' => 'Sun', '1' => 'Mon', '2' => 'Tue', '3' => 'Wed', '4' => 'Thu', '5' => 'Fri', '6' => 'Sat'])
                    ->required(),
                TextInput::make('start_time')->label(__('filament-sms-bulk::messages.fields.start_time'))->default('08:00')->required(),
                TextInput::make('end_time')->label(__('filament-sms-bulk::messages.fields.end_time'))->default('22:00')->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('filament-sms-bulk::messages.fields.name'))->searchable(),
            TextColumn::make('timezone')->label(__('filament-sms-bulk::messages.fields.timezone')),
            TextColumn::make('start_time')->label(__('filament-sms-bulk::messages.fields.start_time')),
            TextColumn::make('end_time')->label(__('filament-sms-bulk::messages.fields.end_time')),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuietHoursProfiles::route('/'),
            'create' => CreateQuietHoursProfile::route('/create'),
            'edit' => EditQuietHoursProfile::route('/{record}/edit'),
        ];
    }
}
