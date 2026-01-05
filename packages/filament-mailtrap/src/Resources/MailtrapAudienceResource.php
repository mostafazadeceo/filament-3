<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailtrap\Resources\MailtrapAudienceResource\Pages\CreateMailtrapAudience;
use Haida\FilamentMailtrap\Resources\MailtrapAudienceResource\Pages\EditMailtrapAudience;
use Haida\FilamentMailtrap\Resources\MailtrapAudienceResource\Pages\ListMailtrapAudiences;
use Haida\FilamentMailtrap\Resources\MailtrapAudienceResource\Pages\ViewMailtrapAudience;
use Haida\FilamentMailtrap\Resources\MailtrapAudienceResource\RelationManagers\MailtrapAudienceContactsRelationManager;
use Haida\MailtrapCore\Models\MailtrapAudience;
use Haida\MailtrapCore\Support\MailtrapLabels;
use Illuminate\Database\Eloquent\Builder;

class MailtrapAudienceResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailtrapAudience::class;

    protected static ?string $permissionPrefix = 'mailtrap.audience';

    protected static ?string $modelLabel = 'لیست مخاطبان';

    protected static ?string $pluralModelLabel = 'لیست‌های مخاطبان';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'مخاطبان';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 45;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('مشخصات لیست مخاطبان')
                ->schema([
                    static::tenantSelect(),
                    TextInput::make('name')
                        ->label('نام')
                        ->required()
                        ->maxLength(190),
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
                    ->formatStateUsing(fn ($state) => MailtrapLabels::audienceStatus($state)),
                TextColumn::make('contacts_count')->label('تعداد مخاطب')->numeric()->sortable(),
                TextColumn::make('updated_at')->label('آخرین بروزرسانی')->jalaliDateTime()->sortable(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('contacts'))
            ->actions([
                ViewAction::make()->label('مشاهده'),
                EditAction::make()->label('ویرایش'),
                DeleteAction::make()->label('حذف'),
            ])
            ->emptyStateHeading('لیست مخاطبی ثبت نشده است')
            ->emptyStateDescription('برای شروع، یک لیست مخاطب جدید بسازید.')
            ->emptyStateActions([
                CreateAction::make()->label('ایجاد لیست مخاطب'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('جزئیات لیست مخاطبان')
                ->schema([
                    TextEntry::make('name')->label('نام'),
                    TextEntry::make('status')
                        ->label('وضعیت')
                        ->formatStateUsing(fn ($state) => MailtrapLabels::audienceStatus($state)),
                    TextEntry::make('contacts_count')
                        ->label('تعداد مخاطب')
                        ->getStateUsing(fn (MailtrapAudience $record) => $record->contacts()->count()),
                    TextEntry::make('description')->label('توضیحات')->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            MailtrapAudienceContactsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailtrapAudiences::route('/'),
            'create' => CreateMailtrapAudience::route('/create'),
            'edit' => EditMailtrapAudience::route('/{record}/edit'),
            'view' => ViewMailtrapAudience::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-mailtrap.navigation.group', 'یکپارچه‌سازی‌ها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-mailtrap.navigation.sort', 45);
    }
}
