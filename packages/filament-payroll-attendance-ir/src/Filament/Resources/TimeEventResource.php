<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\PrivacyEnforcer;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\TimeEventType;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimeEventResource\Pages\CreateTimeEvent;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimeEventResource\Pages\EditTimeEvent;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimeEventResource\Pages\ListTimeEvents;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimeEventResource\Pages\ViewTimeEvent;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class TimeEventResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.time_event';

    protected static ?string $model = TimeEvent::class;

    protected static ?string $modelLabel = 'رویداد زمانی';

    protected static ?string $pluralModelLabel = 'رویدادهای زمانی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'حضور و غیاب';

    protected static array $eagerLoad = ['employee'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('branch_id')
                    ->label('شعبه')
                    ->options(fn () => AccountingBranch::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('employee_id')
                    ->label('پرسنل')
                    ->options(fn () => PayrollEmployee::query()
                        ->selectRaw("id, CONCAT(first_name, ' ', last_name) as name")
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->required(),
                DateTimePicker::make('event_at')
                    ->label('زمان رویداد')
                    ->required(),
                Select::make('event_type')
                    ->label('نوع رویداد')
                    ->options(self::eventTypeOptions())
                    ->required(),
                Select::make('source')
                    ->label('منبع')
                    ->options(self::sourceOptions())
                    ->default('manual')
                    ->required(),
                TextInput::make('device_ref')
                    ->label('شناسه دستگاه')
                    ->nullable(),
                TextInput::make('manual_reason')
                    ->label('علت ثبت دستی')
                    ->required(fn (Get $get) => $get('source') === 'manual')
                    ->dehydrated(false),
                TextInput::make('latitude')
                    ->label('عرض جغرافیایی')
                    ->numeric()
                    ->nullable(),
                TextInput::make('longitude')
                    ->label('طول جغرافیایی')
                    ->numeric()
                    ->nullable(),
                TextInput::make('wifi_ssid')
                    ->label('WiFi SSID')
                    ->nullable(),
                TextInput::make('ip_address')
                    ->label('IP')
                    ->nullable(),
                Select::make('proof_type')
                    ->label('نوع اثبات')
                    ->options([
                        'geofence' => 'ژئوفنس',
                        'wifi_ssid' => 'WiFi',
                        'biometric' => 'بیومتریک',
                    ])
                    ->nullable(),
                KeyValue::make('proof_payload')
                    ->label('جزئیات اثبات')
                    ->keyLabel('کلید')
                    ->valueLabel('مقدار')
                    ->columnSpanFull(),
                Toggle::make('is_verified')
                    ->label('تایید شده')
                    ->default(false),
                DateTimePicker::make('verified_at')
                    ->label('زمان تایید')
                    ->nullable(),
                KeyValue::make('metadata')
                    ->label('متادیتا')
                    ->keyLabel('کلید')
                    ->valueLabel('مقدار')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('پرسنل')
                    ->formatStateUsing(fn ($state, TimeEvent $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name))
                    ->searchable(),
                TextColumn::make('event_at')->label('زمان')->jalaliDateTime()->sortable(),
                TextColumn::make('event_type')
                    ->label('نوع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => self::eventTypeOptions()[$state] ?? $state),
                TextColumn::make('source')
                    ->label('منبع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => self::sourceOptions()[$state] ?? $state),
                IconColumn::make('is_verified')->label('تایید')->boolean(),
            ])
            ->defaultSort('event_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTimeEvents::route('/'),
            'create' => CreateTimeEvent::route('/create'),
            'edit' => EditTimeEvent::route('/{record}/edit'),
            'view' => ViewTimeEvent::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('جزئیات رویداد')
                    ->schema([
                        TextEntry::make('employee_id')
                            ->label('پرسنل')
                            ->formatStateUsing(fn ($state, TimeEvent $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name)),
                        TextEntry::make('event_at')->label('زمان')->jalaliDateTime(),
                        TextEntry::make('event_type')
                            ->label('نوع')
                            ->formatStateUsing(fn ($state) => self::eventTypeOptions()[$state] ?? $state),
                        TextEntry::make('source')
                            ->label('منبع')
                            ->formatStateUsing(fn ($state) => self::sourceOptions()[$state] ?? $state),
                        TextEntry::make('device_ref')->label('شناسه دستگاه'),
                        TextEntry::make('wifi_ssid')->label('WiFi SSID'),
                        TextEntry::make('ip_address')->label('IP'),
                        TextEntry::make('proof_type')->label('نوع اثبات'),
                        TextEntry::make('is_verified')->label('تایید')->formatStateUsing(fn ($state) => $state ? 'بله' : 'خیر'),
                        TextEntry::make('verified_at')->label('زمان تایید')->jalaliDateTime(),
                        TextEntry::make('metadata')
                            ->label('متادیتا')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null)
                            ->columnSpanFull(),
                        TextEntry::make('proof_payload')
                            ->label('جزئیات اثبات')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * @return array<string, string>
     */
    public static function eventTypeOptions(): array
    {
        return [
            TimeEventType::ClockIn->value => 'ورود',
            TimeEventType::ClockOut->value => 'خروج',
            TimeEventType::BreakStart->value => 'شروع استراحت',
            TimeEventType::BreakEnd->value => 'پایان استراحت',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function sourceOptions(): array
    {
        return [
            'manual' => 'دستی',
            'mobile' => 'موبایل',
            'web' => 'وب',
            'kiosk' => 'کیوسک',
            'hardware' => 'دستگاه',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function sanitizePayload(array $data): array
    {
        $reason = trim((string) ($data['manual_reason'] ?? ''));
        unset($data['manual_reason']);

        if ($reason !== '') {
            $data['metadata'] = array_merge((array) ($data['metadata'] ?? []), [
                'reason' => $reason,
            ]);
        }

        return app(PrivacyEnforcer::class)->sanitizeTimeEventPayload($data);
    }
}
