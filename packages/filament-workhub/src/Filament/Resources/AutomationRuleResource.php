<?php

namespace Haida\FilamentWorkhub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Haida\FilamentWorkhub\Filament\Resources\AutomationRuleResource\Pages\CreateAutomationRule;
use Haida\FilamentWorkhub\Filament\Resources\AutomationRuleResource\Pages\EditAutomationRule;
use Haida\FilamentWorkhub\Filament\Resources\AutomationRuleResource\Pages\ListAutomationRules;
use Haida\FilamentWorkhub\Models\AutomationRule;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Support\AutomationRegistry;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AutomationRuleResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'workhub.automation';

    protected static ?string $model = AutomationRule::class;

    protected static ?string $navigationLabel = 'اتوماسیون';

    protected static ?string $pluralModelLabel = 'اتوماسیون';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static string|\UnitEnum|null $navigationGroup = 'رهگیری کارها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                Select::make('project_id')
                    ->label('پروژه')
                    ->options(fn () => Project::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('trigger_type')
                    ->label('تریگر')
                    ->options(fn () => app(AutomationRegistry::class)->triggerOptions())
                    ->required(),
                KeyValue::make('trigger_config')
                    ->label('پیکربندی تریگر')
                    ->keyLabel('کلید')
                    ->valueLabel('مقدار')
                    ->nullable(),
                Repeater::make('conditions')
                    ->label('شرایط')
                    ->schema([
                        Select::make('type')
                            ->label('نوع شرط')
                            ->options(fn () => app(AutomationRegistry::class)->conditionOptions())
                            ->required(),
                        KeyValue::make('config')
                            ->label('پیکربندی')
                            ->keyLabel('کلید')
                            ->valueLabel('مقدار')
                            ->nullable(),
                    ])
                    ->default([])
                    ->collapsible(),
                Repeater::make('actions')
                    ->label('اقدامات')
                    ->schema([
                        Select::make('type')
                            ->label('نوع اقدام')
                            ->options(fn () => app(AutomationRegistry::class)->actionOptions())
                            ->required(),
                        KeyValue::make('config')
                            ->label('پیکربندی')
                            ->keyLabel('کلید')
                            ->valueLabel('مقدار')
                            ->nullable(),
                    ])
                    ->default([])
                    ->collapsible(),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('project.name')->label('پروژه'),
                TextColumn::make('trigger_type')->label('تریگر'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAutomationRules::route('/'),
            'create' => CreateAutomationRule::route('/create'),
            'edit' => EditAutomationRule::route('/{record}/edit'),
        ];
    }
}
