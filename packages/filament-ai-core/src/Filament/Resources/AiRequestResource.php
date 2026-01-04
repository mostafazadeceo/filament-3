<?php

namespace Haida\FilamentAiCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentAiCore\Filament\Resources\AiRequestResource\Pages\ListAiRequests;
use Haida\FilamentAiCore\Models\AiRequest;
use Illuminate\Database\Eloquent\Model;

class AiRequestResource extends Resource
{
    use InteractsWithTenant;

    protected static ?string $model = AiRequest::class;

    protected static ?string $navigationLabel = 'لاگ درخواست‌های هوش';

    protected static ?string $pluralModelLabel = 'لاگ درخواست‌های هوش';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'هوش مصنوعی';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('module')->label('ماژول')->disabled(),
                TextInput::make('action_type')->label('نوع عملیات')->disabled(),
                TextInput::make('status')->label('وضعیت')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری')->toggleable(),
                TextColumn::make('module')->label('ماژول'),
                TextColumn::make('action_type')->label('نوع عملیات'),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('latency_ms')->label('تأخیر (ms)')->numeric(),
                TextColumn::make('created_at')->label('زمان'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAiRequests::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return IamAuthorization::allows('ai.audit.view');
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allows('ai.audit.view', IamAuthorization::resolveTenantFromRecord($record));
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
}
