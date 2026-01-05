<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoConnectionResource\Pages\CreateEsimGoConnection;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoConnectionResource\Pages\EditEsimGoConnection;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoConnectionResource\Pages\ListEsimGoConnections;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Services\EsimGoConnectionService;
use Haida\ProvidersEsimGoCore\Support\EsimGoLabels;

class EsimGoConnectionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = EsimGoConnection::class;

    protected static ?string $permissionPrefix = 'esim_go.connection';

    protected static ?string $modelLabel = 'اتصال eSIM Go';

    protected static ?string $pluralModelLabel = 'اتصال‌های eSIM Go';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?string $navigationLabel = 'اتصال‌ها';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('تنظیمات اتصال')
                ->schema([
                    static::tenantSelect(),
                    TextInput::make('name')
                        ->label('نام اتصال')
                        ->required()
                        ->maxLength(150)
                        ->scopedUnique(ignoreRecord: true)
                        ->validationMessages([
                            'unique' => 'این نام اتصال قبلاً برای همین فضای کاری ثبت شده است.',
                        ]),
                    TextInput::make('api_key')
                        ->label('کلید API')
                        ->password()
                        ->revealable()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->helperText('برای حفظ کلید فعلی خالی بگذارید.'),
                    Toggle::make('metadata.sandbox')
                        ->label('محیط آزمایشی')
                        ->helperText('در صورت فعال بودن از نشانی sandbox استفاده می‌شود.'),
                    TextInput::make('metadata.profile_id')
                        ->label('شناسه پروفایل (اختیاری)')
                        ->helperText('در صورت نیاز به پروفایل اختصاصی برای سفارش‌ها.'),
                    TextInput::make('metadata.callback_url')
                        ->label('نشانی وبهوک')
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(function ($component, ?EsimGoConnection $record) {
                            $connectionId = $record?->getKey();
                            $url = url('/api/v1/providers/esim-go/callback');
                            if ($connectionId) {
                                $url .= '?connection_id='.$connectionId;
                            }
                            $component->state($url);
                        }),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => EsimGoLabels::connectionStatus($state)),
                IconColumn::make('metadata.sandbox')
                    ->label('آزمایشی')
                    ->boolean(),
                TextColumn::make('last_tested_at')->label('آخرین آزمون')->jalaliDateTime(),
                TextColumn::make('updated_at')->label('آخرین بروزرسانی')->jalaliDateTime()->sortable(),
            ])
            ->actions([
                Action::make('test_connection')
                    ->label('آزمون اتصال')
                    ->icon('heroicon-o-beaker')
                    ->action(function (EsimGoConnection $record, EsimGoConnectionService $service) {
                        $service->testConnection($record, (bool) ($record->metadata['sandbox'] ?? false));
                    }),
                EditAction::make()->label('ویرایش'),
                DeleteAction::make()->label('حذف'),
            ])
            ->emptyStateHeading('اتصالی ثبت نشده است')
            ->emptyStateDescription('برای شروع، یک اتصال جدید بسازید و کلید API را وارد کنید.')
            ->emptyStateActions([
                CreateAction::make()->label('ایجاد اتصال'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEsimGoConnections::route('/'),
            'create' => CreateEsimGoConnection::route('/create'),
            'edit' => EditEsimGoConnection::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-providers-esim-go.navigation.group', 'Providerها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-providers-esim-go.navigation.sort', 30);
    }
}
