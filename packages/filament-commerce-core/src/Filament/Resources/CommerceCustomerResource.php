<?php

namespace Haida\FilamentCommerceCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceCustomerResource\Pages\CreateCommerceCustomer;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceCustomerResource\Pages\EditCommerceCustomer;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceCustomerResource\Pages\ListCommerceCustomers;
use Haida\FilamentCommerceCore\Models\CommerceCustomer;

class CommerceCustomerResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'commerce.customers';

    protected static ?string $model = CommerceCustomer::class;

    protected static ?string $modelLabel = 'مشتری';

    protected static ?string $pluralModelLabel = 'مشتریان';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'فروشگاه';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('user_id')
                    ->label('کاربر')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->nullable(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('ایمیل')
                    ->email()
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('phone')
                    ->label('تلفن')
                    ->maxLength(40)
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                        'blocked' => 'مسدود',
                    ])
                    ->default('active')
                    ->required(),
                Textarea::make('default_billing_address')
                    ->label('آدرس صورتحساب (JSON)')
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
                Textarea::make('default_shipping_address')
                    ->label('آدرس ارسال (JSON)')
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
                Textarea::make('notes')
                    ->label('یادداشت')
                    ->rows(3)
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
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('email')->label('ایمیل')->searchable(),
                TextColumn::make('phone')->label('تلفن'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('updated_at')->label('بروزرسانی')->jalaliDateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                        'blocked' => 'مسدود',
                    ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommerceCustomers::route('/'),
            'create' => CreateCommerceCustomer::route('/create'),
            'edit' => EditCommerceCustomer::route('/{record}/edit'),
        ];
    }
}
