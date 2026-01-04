<?php

namespace Haida\FilamentCommerceExperience\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceCsatSurveyResource\Pages\CreateExperienceCsatSurvey;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceCsatSurveyResource\Pages\EditExperienceCsatSurvey;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceCsatSurveyResource\Pages\ListExperienceCsatSurveys;
use Haida\FilamentCommerceExperience\Models\ExperienceCsatSurvey;
use Illuminate\Database\Eloquent\Model;

class ExperienceCsatSurveyResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = ExperienceCsatSurvey::class;

    protected static ?string $modelLabel = 'CSAT';

    protected static ?string $pluralModelLabel = 'CSAT/NPS';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-face-smile';

    protected static string|\UnitEnum|null $navigationGroup = 'تجربه مشتری';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['experience.csat.view', 'experience.csat.manage']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allowsAny(['experience.csat.view', 'experience.csat.manage'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('experience.csat.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('experience.csat.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('experience.csat.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('order_id')
                    ->label('شناسه سفارش')
                    ->numeric()
                    ->nullable(),
                TextInput::make('customer_id')
                    ->label('شناسه مشتری')
                    ->numeric()
                    ->nullable(),
                TextInput::make('channel')
                    ->label('کانال')
                    ->maxLength(64)
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'sent' => 'ارسال شده',
                        'answered' => 'پاسخ داده شده',
                    ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('sent_at')
                    ->label('ارسال')
                    ->nullable(),
                DateTimePicker::make('answered_at')
                    ->label('پاسخ')
                    ->nullable(),
                Textarea::make('metadata')
                    ->label('متادیتا (JSON)')
                    ->rows(3)
                    ->nullable()
                    ->rules(['nullable', 'json'])
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (! is_string($state) || trim($state) === '') {
                            return null;
                        }

                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')
                    ->label('سفارش'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('sent_at')
                    ->label('ارسال')
                    ->jalaliDateTime(),
                TextColumn::make('answered_at')
                    ->label('پاسخ')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExperienceCsatSurveys::route('/'),
            'create' => CreateExperienceCsatSurvey::route('/create'),
            'edit' => EditExperienceCsatSurvey::route('/{record}/edit'),
        ];
    }
}
