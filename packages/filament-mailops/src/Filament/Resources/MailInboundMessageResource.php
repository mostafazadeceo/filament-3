<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailOps\Filament\Resources\MailInboundMessageResource\Pages\ListMailInboundMessages;
use Haida\FilamentMailOps\Filament\Resources\MailInboundMessageResource\Pages\ViewMailInboundMessage;
use Haida\FilamentMailOps\Models\MailInboundMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MailInboundMessageResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailInboundMessage::class;

    protected static ?string $modelLabel = 'پیام دریافتی';

    protected static ?string $pluralModelLabel = 'پیام‌های دریافتی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox';

    protected static string|\UnitEnum|null $navigationGroup = 'ایمیل';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allows('mailops.inbound.view');
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allows('mailops.inbound.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('from_email')
                    ->label('فرستنده')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('subject')
                    ->label('عنوان')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('to_emails')
                    ->label('گیرندگان')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn (?array $state) => $state ? implode(', ', $state) : '-'),
                TextInput::make('cc_emails')
                    ->label('کپی')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn (?array $state) => $state ? implode(', ', $state) : '-'),
                TextInput::make('bcc_emails')
                    ->label('کپی مخفی')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn (?array $state) => $state ? implode(', ', $state) : '-'),
                Textarea::make('text_body')
                    ->label('متن ساده')
                    ->rows(8)
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
                Textarea::make('html_body')
                    ->label('متن HTML')
                    ->rows(8)
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('mailbox'))
            ->columns([
                TextColumn::make('mailbox.email')
                    ->label('صندوق')
                    ->sortable(),
                TextColumn::make('from_email')
                    ->label('فرستنده')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('عنوان')
                    ->wrap(),
                TextColumn::make('received_at')
                    ->label('تاریخ دریافت')
                    ->jalaliDateTime(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->defaultSort('received_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailInboundMessages::route('/'),
            'view' => ViewMailInboundMessage::route('/{record}'),
        ];
    }
}
