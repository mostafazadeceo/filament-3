<?php

namespace Haida\FilamentThreeCx\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxContactResource\Pages\CreateThreeCxContact;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxContactResource\Pages\EditThreeCxContact;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxContactResource\Pages\ListThreeCxContacts;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxContactResource\Pages\ViewThreeCxContact;
use Haida\FilamentThreeCx\Models\ThreeCxContact;

class ThreeCxContactResource extends Resource
{
    protected static ?string $model = ThreeCxContact::class;

    protected static ?string $modelLabel = 'مخاطب';

    protected static ?string $pluralModelLabel = 'مخاطبین';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'مخاطبین';

    protected static string|\UnitEnum|null $navigationGroup = '3CX';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allows('threecx.view');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('مخاطب')
                    ->schema([
                        TextInput::make('name')
                            ->label('نام')
                            ->required()
                            ->maxLength(255),
                        TagsInput::make('phones')
                            ->label('شماره‌ها')
                            ->placeholder('0912...')
                            ->columnSpanFull(),
                        TagsInput::make('emails')
                            ->label('ایمیل‌ها')
                            ->placeholder('example@domain.com')
                            ->columnSpanFull(),
                        TextInput::make('external_id')
                            ->label('شناسه خارجی')
                            ->maxLength(255),
                        TextInput::make('crm_url')
                            ->label('نشانی CRM')
                            ->maxLength(2048),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('phones')
                    ->label('شماره‌ها')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode('، ', $state) : $state)
                    ->toggleable(),
                TextColumn::make('emails')
                    ->label('ایمیل‌ها')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode('، ', $state) : $state)
                    ->toggleable(),
                TextColumn::make('external_id')->label('شناسه خارجی')->toggleable(),
                TextColumn::make('crm_url')->label('نشانی CRM')->toggleable(),
                TextColumn::make('updated_at')->label('به‌روزرسانی')->jalaliDateTime()->sortable(),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
                EditAction::make()->label('ویرایش'),
                DeleteAction::make()
                    ->label('حذف')
                    ->modalHeading('حذف مخاطب')
                    ->modalSubmitActionLabel('حذف')
                    ->modalCancelActionLabel('انصراف'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListThreeCxContacts::route('/'),
            'create' => CreateThreeCxContact::route('/create'),
            'edit' => EditThreeCxContact::route('/{record}/edit'),
            'view' => ViewThreeCxContact::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('مخاطب')
                    ->schema([
                        TextEntry::make('name')->label('نام'),
                        TextEntry::make('phones')
                            ->label('شماره‌ها')
                            ->formatStateUsing(fn ($state) => is_array($state) ? implode('، ', $state) : $state),
                        TextEntry::make('emails')
                            ->label('ایمیل‌ها')
                            ->formatStateUsing(fn ($state) => is_array($state) ? implode('، ', $state) : $state),
                        TextEntry::make('external_id')->label('شناسه خارجی'),
                        TextEntry::make('crm_url')->label('نشانی CRM'),
                    ])
                    ->columns(2),
            ]);
    }
}
