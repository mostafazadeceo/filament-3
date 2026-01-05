<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailOps\Filament\Resources\MailOutboundMessageResource\Pages\CreateMailOutboundMessage;
use Haida\FilamentMailOps\Filament\Resources\MailOutboundMessageResource\Pages\ListMailOutboundMessages;
use Haida\FilamentMailOps\Filament\Resources\MailOutboundMessageResource\Pages\ViewMailOutboundMessage;
use Haida\FilamentMailOps\Models\MailMailbox;
use Haida\FilamentMailOps\Models\MailOutboundMessage;
use Haida\FilamentMailOps\Support\MailOpsLabels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MailOutboundMessageResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailOutboundMessage::class;

    protected static ?string $modelLabel = 'ارسال ایمیل';

    protected static ?string $pluralModelLabel = 'ارسال‌های ایمیل';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static string|\UnitEnum|null $navigationGroup = 'ایمیل';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['mailops.outbound.view', 'mailops.outbound.send']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allows('mailops.outbound.view', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('mailops.outbound.send');
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
                Select::make('mailbox_id')
                    ->label('صندوق ارسال')
                    ->options(fn () => static::scopeByTenant(MailMailbox::query())
                        ->orderBy('email')
                        ->pluck('email', 'id')
                        ->toArray())
                    ->searchable()
                    ->required(),
                TagsInput::make('to_emails')
                    ->label('گیرندگان')
                    ->required(),
                TagsInput::make('cc_emails')
                    ->label('کپی')
                    ->nullable(),
                TagsInput::make('bcc_emails')
                    ->label('کپی مخفی')
                    ->nullable(),
                TextInput::make('subject')
                    ->label('عنوان')
                    ->maxLength(255),
                Textarea::make('text_body')
                    ->label('متن ساده')
                    ->rows(6)
                    ->columnSpanFull(),
                Textarea::make('html_body')
                    ->label('متن HTML')
                    ->rows(6)
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
                    ->label('فرستنده')
                    ->sortable(),
                TextColumn::make('to_emails')
                    ->label('گیرندگان')
                    ->formatStateUsing(function (mixed $state): string {
                        if (is_array($state)) {
                            return $state !== [] ? implode(', ', $state) : '-';
                        }

                        if (is_string($state)) {
                            $state = trim($state);
                            if ($state === '') {
                                return '-';
                            }

                            $decoded = json_decode($state, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                return $decoded !== [] ? implode(', ', $decoded) : '-';
                            }

                            return $state;
                        }

                        return '-';
                    })
                    ->wrap(),
                TextColumn::make('subject')
                    ->label('عنوان')
                    ->wrap(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => MailOpsLabels::sendStatus($state)),
                TextColumn::make('sent_at')
                    ->label('زمان ارسال')
                    ->jalaliDateTime(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailOutboundMessages::route('/'),
            'create' => CreateMailOutboundMessage::route('/create'),
            'view' => ViewMailOutboundMessage::route('/{record}'),
        ];
    }
}
