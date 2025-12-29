<?php

namespace Haida\FilamentWorkhub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Haida\FilamentWorkhub\Filament\Resources\CustomFieldResource\Pages\CreateCustomField;
use Haida\FilamentWorkhub\Filament\Resources\CustomFieldResource\Pages\EditCustomField;
use Haida\FilamentWorkhub\Filament\Resources\CustomFieldResource\Pages\ListCustomFields;
use Haida\FilamentWorkhub\Models\CustomField;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomFieldResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'workhub.custom_field';

    protected static ?string $model = CustomField::class;

    protected static ?string $navigationLabel = 'فیلدهای سفارشی';

    protected static ?string $pluralModelLabel = 'فیلدهای سفارشی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-vertical';

    protected static string|\UnitEnum|null $navigationGroup = 'رهگیری کارها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('scope')
                    ->label('دامنه')
                    ->options([
                        'work_item' => 'آیتم کاری',
                        'project' => 'پروژه',
                    ])
                    ->required(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('key')
                    ->label('کلید')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'text' => 'متن کوتاه',
                        'textarea' => 'متن بلند',
                        'number' => 'عدد',
                        'date' => 'تاریخ',
                        'boolean' => 'بله/خیر',
                        'select' => 'انتخابی',
                        'multi_select' => 'چند انتخابی',
                    ])
                    ->required(),
                KeyValue::make('settings')
                    ->label('تنظیمات')
                    ->keyLabel('کلید')
                    ->valueLabel('مقدار')
                    ->nullable(),
                TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_required')
                    ->label('الزامی')
                    ->default(false),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scope')->label('دامنه'),
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('key')->label('کلید'),
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('sort_order')->label('ترتیب'),
                IconColumn::make('is_required')->label('الزامی')->boolean(),
                IconColumn::make('is_active')->label('فعال')->boolean(),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomFields::route('/'),
            'create' => CreateCustomField::route('/create'),
            'edit' => EditCustomField::route('/{record}/edit'),
        ];
    }
}
