<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\UserInvitationResource\Pages\CreateUserInvitation;
use Filamat\IamSuite\Filament\Resources\UserInvitationResource\Pages\ListUserInvitations;
use Filamat\IamSuite\Models\UserInvitation;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserInvitationResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'user';

    protected static ?string $model = UserInvitation::class;

    protected static ?string $navigationLabel = 'دعوت‌نامه‌ها';

    protected static ?string $pluralModelLabel = 'دعوت‌نامه‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')->label('نام')->nullable()->dehydrated(false),
                TextInput::make('email')->label('ایمیل')->email()->required(),
                Textarea::make('reason')->label('دلیل')->required(),
                DateTimePicker::make('expires_at')->label('انقضا')->nullable(),
            ]);
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allowsAny(['user.invite', 'user.manage']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('email')->label('ایمیل')->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'در انتظار',
                        'accepted' => 'پذیرفته شده',
                        'revoked' => 'لغو شده',
                        'expired' => 'منقضی شده',
                        default => $state,
                    }),
                TextColumn::make('invitedBy.name')->label('دعوت کننده'),
                TextColumn::make('expires_at')->label('انقضا'),
                TextColumn::make('accepted_at')->label('پذیرش'),
                TextColumn::make('created_at')->label('ایجاد'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserInvitations::route('/'),
            'create' => CreateUserInvitation::route('/create'),
        ];
    }
}
