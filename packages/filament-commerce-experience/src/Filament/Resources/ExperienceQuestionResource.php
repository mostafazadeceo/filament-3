<?php

namespace Haida\FilamentCommerceExperience\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceQuestionResource\Pages\CreateExperienceQuestion;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceQuestionResource\Pages\EditExperienceQuestion;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceQuestionResource\Pages\ListExperienceQuestions;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceQuestionResource\RelationManagers\ExperienceAnswerRelationManager;
use Haida\FilamentCommerceExperience\Models\ExperienceQuestion;
use Illuminate\Database\Eloquent\Model;

class ExperienceQuestionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = ExperienceQuestion::class;

    protected static ?string $modelLabel = 'پرسش';

    protected static ?string $pluralModelLabel = 'پرسش‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

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
                TextInput::make('customer_id')
                    ->label('شناسه مشتری')
                    ->numeric()
                    ->nullable(),
                Textarea::make('question')
                    ->label('پرسش')
                    ->rows(3)
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'approved' => 'تایید شده',
                        'rejected' => 'رد شده',
                    ])
                    ->default('pending')
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_id')
                    ->label('محصول'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ExperienceAnswerRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExperienceQuestions::route('/'),
            'create' => CreateExperienceQuestion::route('/create'),
            'edit' => EditExperienceQuestion::route('/{record}/edit'),
        ];
    }
}
