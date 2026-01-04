<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailtrap\Resources\MailtrapOfferResource\Pages\CreateMailtrapOffer;
use Haida\FilamentMailtrap\Resources\MailtrapOfferResource\Pages\EditMailtrapOffer;
use Haida\FilamentMailtrap\Resources\MailtrapOfferResource\Pages\ListMailtrapOffers;
use Haida\MailtrapCore\Models\MailtrapOffer;
use Haida\MailtrapCore\Services\MailtrapOfferService;

class MailtrapOfferResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailtrapOffer::class;

    protected static ?string $permissionPrefix = 'mailtrap.offer';

    protected static ?string $modelLabel = 'پکیج Mailtrap';

    protected static ?string $pluralModelLabel = 'پکیج‌های Mailtrap';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'پکیج‌ها';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('مشخصات پکیج')
                ->schema([
                    static::tenantSelect(),
                    TextInput::make('name')
                        ->label('نام')
                        ->required()
                        ->maxLength(150),
                    TextInput::make('slug')
                        ->label('اسلاگ')
                        ->helperText('اختیاری؛ در صورت خالی بودن خودکار تولید می‌شود.'),
                    Select::make('status')
                        ->label('وضعیت')
                        ->options([
                            'active' => 'فعال',
                            'inactive' => 'غیرفعال',
                        ])
                        ->default('active'),
                    Textarea::make('description')
                        ->label('توضیحات')
                        ->rows(3)
                        ->columnSpanFull(),
                    TextInput::make('duration_days')
                        ->label('مدت (روز)')
                        ->numeric()
                        ->required(),
                    TagsInput::make('feature_keys')
                        ->label('کلیدهای قابلیت')
                        ->helperText('مثال: mailtrap.connection.view')
                        ->required(),
                    KeyValue::make('limits')
                        ->label('محدودیت‌ها (اختیاری)')
                        ->keyLabel('کلید')
                        ->valueLabel('مقدار')
                        ->columnSpanFull(),
                    TextInput::make('price')
                        ->label('قیمت')
                        ->numeric()
                        ->required(),
                    TextInput::make('currency')
                        ->label('ارز')
                        ->default('USD')
                        ->maxLength(10)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('duration_days')->label('مدت')->suffix(' روز')->toggleable(),
                TextColumn::make('price')->label('قیمت')->toggleable(),
                TextColumn::make('currency')->label('ارز')->toggleable(),
                IconColumn::make('catalog_product_id')
                    ->label('انتشار در فروشگاه')
                    ->boolean(),
            ])
            ->actions([
                Action::make('publish')
                    ->label('انتشار در فروشگاه')
                    ->icon('heroicon-o-arrow-up-right')
                    ->action(function (MailtrapOffer $record, MailtrapOfferService $service): void {
                        $service->publishToCatalog($record);
                        Notification::make()->title('پکیج در فروشگاه منتشر شد.')->success()->send();
                    }),
                EditAction::make()->label('ویرایش'),
                DeleteAction::make()->label('حذف'),
            ])
            ->emptyStateHeading('پکیجی ثبت نشده است')
            ->emptyStateDescription('برای فروش Mailtrap، یک پکیج تعریف کنید.')
            ->emptyStateActions([
                CreateAction::make()->label('ایجاد پکیج'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailtrapOffers::route('/'),
            'create' => CreateMailtrapOffer::route('/create'),
            'edit' => EditMailtrapOffer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-mailtrap.navigation.group', 'یکپارچه‌سازی‌ها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-mailtrap.navigation.sort', 44);
    }
}
