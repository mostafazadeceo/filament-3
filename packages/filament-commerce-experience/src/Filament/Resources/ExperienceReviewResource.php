<?php

namespace Haida\FilamentCommerceExperience\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceReviewResource\Pages\CreateExperienceReview;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceReviewResource\Pages\EditExperienceReview;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceReviewResource\Pages\ListExperienceReviews;
use Haida\FilamentCommerceExperience\Models\ExperienceReview;
use Illuminate\Database\Eloquent\Model;

class ExperienceReviewResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = ExperienceReview::class;

    protected static ?string $modelLabel = 'نظر';

    protected static ?string $pluralModelLabel = 'نظرات';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static string|\UnitEnum|null $navigationGroup = 'تجربه مشتری';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['experience.reviews.view', 'experience.reviews.moderate']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allowsAny(['experience.reviews.view', 'experience.reviews.moderate'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('experience.reviews.moderate');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('experience.reviews.moderate', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('experience.reviews.moderate', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('product_id')
                    ->label('شناسه محصول')
                    ->numeric()
                    ->nullable(),
                TextInput::make('order_id')
                    ->label('شناسه سفارش')
                    ->numeric()
                    ->nullable(),
                TextInput::make('customer_id')
                    ->label('شناسه مشتری')
                    ->numeric()
                    ->nullable(),
                Select::make('rating')
                    ->label('امتیاز')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ])
                    ->required(),
                TextInput::make('title')
                    ->label('عنوان')
                    ->maxLength(255)
                    ->nullable(),
                Textarea::make('body')
                    ->label('متن')
                    ->rows(4)
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'approved' => 'تایید شده',
                        'rejected' => 'رد شده',
                    ])
                    ->default('pending')
                    ->required(),
                Toggle::make('verified_purchase')
                    ->label('خرید تایید شده')
                    ->default(false),
                Toggle::make('abuse_flag')
                    ->label('پرچم سوءاستفاده')
                    ->default(false),
                TextInput::make('helpful_count')
                    ->label('مفید بودن')
                    ->numeric()
                    ->default(0),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_id')
                    ->label('محصول'),
                TextColumn::make('rating')
                    ->label('امتیاز'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('verified_purchase')
                    ->label('خرید تایید شده')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExperienceReviews::route('/'),
            'create' => CreateExperienceReview::route('/create'),
            'edit' => EditExperienceReview::route('/{record}/edit'),
        ];
    }
}
