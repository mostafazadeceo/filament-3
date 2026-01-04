<?php

namespace Haida\FilamentCommerceCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceFraudRuleResource\Pages\CreateCommerceFraudRule;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceFraudRuleResource\Pages\EditCommerceFraudRule;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceFraudRuleResource\Pages\ListCommerceFraudRules;
use Haida\FilamentCommerceCore\Models\CommerceFraudRule;
use Illuminate\Database\Eloquent\Model;

class CommerceFraudRuleResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = CommerceFraudRule::class;

    protected static ?string $modelLabel = 'قاعده ضدتقلب';

    protected static ?string $pluralModelLabel = 'قواعد ضدتقلب';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'انطباق';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['commerce.compliance.view', 'commerce.compliance.manage']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allowsAny(['commerce.compliance.view', 'commerce.compliance.manage'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('key')
                    ->label('کلید')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
                Textarea::make('thresholds')
                    ->label('آستانه‌ها (JSON)')
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
                TextColumn::make('key')
                    ->label('کلید')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommerceFraudRules::route('/'),
            'create' => CreateCommerceFraudRule::route('/create'),
            'edit' => EditCommerceFraudRule::route('/{record}/edit'),
        ];
    }
}
