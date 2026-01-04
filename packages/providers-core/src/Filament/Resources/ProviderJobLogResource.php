<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\ProvidersCore\Filament\Resources\ProviderJobLogResource\Pages\ListProviderJobLogs;
use Haida\ProvidersCore\Filament\Resources\ProviderJobLogResource\Pages\ViewProviderJobLog;
use Haida\ProvidersCore\Models\ProviderJobLog;
use Haida\ProvidersCore\Services\ProviderJobReprocessService;

class ProviderJobLogResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'provider.job_log';

    protected static ?string $model = ProviderJobLog::class;

    protected static ?string $modelLabel = 'لاگ Provider';

    protected static ?string $pluralModelLabel = 'لاگ‌های Provider';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static string|\UnitEnum|null $navigationGroup = 'یکپارچه‌سازی‌ها';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $tenant = TenantContext::getTenant();
        if ($tenant) {
            $query->where('tenant_id', $tenant->getKey());
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('provider_key')
                    ->label('Provider')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('job_type')
                    ->label('نوع عملیات')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('status')
                    ->label('وضعیت')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('attempts')
                    ->label('تلاش‌ها')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('connection_id')
                    ->label('اتصال')
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('error_message')
                    ->label('خطا')
                    ->rows(3)
                    ->disabled()
                    ->dehydrated(false),
                KeyValue::make('payload')
                    ->label('Payload')
                    ->disabled()
                    ->dehydrated(false),
                KeyValue::make('result')
                    ->label('نتیجه')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('started_at')
                    ->label('شروع')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('finished_at')
                    ->label('پایان')
                    ->disabled()
                    ->dehydrated(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider_key')->label('Provider')->searchable(),
                TextColumn::make('job_type')->label('نوع'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('attempts')->label('تلاش‌ها'),
                TextColumn::make('connection_id')->label('اتصال'),
                TextColumn::make('started_at')->label('شروع')->jalaliDateTime(),
                TextColumn::make('finished_at')->label('پایان')->jalaliDateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'running' => 'در حال اجرا',
                        'succeeded' => 'موفق',
                        'failed' => 'ناموفق',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('reprocess')
                    ->label('بازپردازش')
                    ->requiresConfirmation()
                    ->visible(fn (ProviderJobLog $record): bool => $record->status === 'failed')
                    ->action(function (ProviderJobLog $record): void {
                        app(ProviderJobReprocessService::class)->reprocess($record);
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProviderJobLogs::route('/'),
            'view' => ViewProviderJobLog::route('/{record}'),
        ];
    }
}
