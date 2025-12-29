<?php

namespace Haida\FilamentWorkhub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Haida\FilamentWorkhub\Filament\Resources\TransitionResource\Pages\CreateTransition;
use Haida\FilamentWorkhub\Filament\Resources\TransitionResource\Pages\EditTransition;
use Haida\FilamentWorkhub\Filament\Resources\TransitionResource\Pages\ListTransitions;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Transition;
use Haida\FilamentWorkhub\Models\Workflow;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransitionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'workhub.transition';

    protected static ?string $model = Transition::class;

    protected static ?string $navigationLabel = 'انتقال‌ها';

    protected static ?string $pluralModelLabel = 'انتقال‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-right-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'رهگیری کارها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('workflow_id')
                    ->label('گردش‌کار')
                    ->options(fn () => Workflow::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required()
                    ->reactive(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                Select::make('from_status_id')
                    ->label('از وضعیت')
                    ->options(fn (Get $get) => Status::query()
                        ->where('workflow_id', $get('workflow_id'))
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->required(),
                Select::make('to_status_id')
                    ->label('به وضعیت')
                    ->options(fn (Get $get) => Status::query()
                        ->where('workflow_id', $get('workflow_id'))
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->required(),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                Toggle::make('validators.requires_assignee')
                    ->label('نیازمند مسئول'),
                Toggle::make('validators.requires_due_date')
                    ->label('نیازمند تاریخ سررسید'),
                KeyValue::make('post_actions')
                    ->label('اقدامات پس از انتقال')
                    ->keyLabel('کلید')
                    ->valueLabel('مقدار')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workflow.name')->label('گردش‌کار'),
                TextColumn::make('name')->label('نام'),
                TextColumn::make('fromStatus.name')->label('از وضعیت'),
                TextColumn::make('toStatus.name')->label('به وضعیت'),
                TextColumn::make('sort_order')->label('ترتیب'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransitions::route('/'),
            'create' => CreateTransition::route('/create'),
            'edit' => EditTransition::route('/{record}/edit'),
        ];
    }
}
