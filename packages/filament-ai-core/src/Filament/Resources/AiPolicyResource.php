<?php

namespace Haida\FilamentAiCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentAiCore\Filament\Resources\AiPolicyResource\Pages\CreateAiPolicy;
use Haida\FilamentAiCore\Filament\Resources\AiPolicyResource\Pages\EditAiPolicy;
use Haida\FilamentAiCore\Filament\Resources\AiPolicyResource\Pages\ListAiPolicies;
use Haida\FilamentAiCore\Models\AiPolicy;
use Illuminate\Database\Eloquent\Model;

class AiPolicyResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = AiPolicy::class;

    protected static ?string $navigationLabel = 'سیاست‌های هوش مصنوعی';

    protected static ?string $pluralModelLabel = 'سیاست‌های هوش مصنوعی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'هوش مصنوعی';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Toggle::make('enabled')
                    ->label('فعال‌سازی هوش مصنوعی')
                    ->inline(false)
                    ->default((bool) config('filament-ai-core.enabled', false)),
                Select::make('provider')
                    ->label('ارائه‌دهنده')
                    ->options(static::providerOptions())
                    ->required()
                    ->default((string) config('filament-ai-core.default_provider', 'mock')),
                TextInput::make('retention_days')
                    ->label('روزهای نگهداری')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->default((int) config('filament-ai-core.retention_days', 30)),
                Toggle::make('consent_required_meetings')
                    ->label('الزام رضایت برای جلسات')
                    ->inline(false)
                    ->default((bool) config('filament-ai-core.consent_required_meetings', true)),
                Toggle::make('allow_store_transcripts')
                    ->label('اجازه ذخیره متن گفت‌وگو')
                    ->inline(false)
                    ->default((bool) config('filament-ai-core.allow_store_transcripts', false)),
                Section::make('قواعد حذف/ماسک اطلاعات')
                    ->schema([
                        Select::make('redaction_policy.emails')
                            ->label('ایمیل‌ها')
                            ->options([
                                'mask' => 'ماسک',
                                'remove' => 'حذف',
                                'keep' => 'نگه‌داری',
                            ])
                            ->default('mask'),
                        Select::make('redaction_policy.phones')
                            ->label('شماره‌ها')
                            ->options([
                                'mask' => 'ماسک',
                                'remove' => 'حذف',
                                'keep' => 'نگه‌داری',
                            ])
                            ->default('mask'),
                        Select::make('redaction_policy.ip')
                            ->label('IP')
                            ->options([
                                'remove' => 'حذف',
                                'keep' => 'نگه‌داری',
                            ])
                            ->default('remove'),
                        Select::make('redaction_policy.ua')
                            ->label('User Agent')
                            ->options([
                                'remove' => 'حذف',
                                'keep' => 'نگه‌داری',
                            ])
                            ->default('remove'),
                        TagsInput::make('redaction_policy.sensitive_terms')
                            ->label('عبارات حساس')
                            ->separator(','),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری')->toggleable(),
                IconColumn::make('enabled')->label('فعال')->boolean(),
                TextColumn::make('provider')->label('ارائه‌دهنده'),
                TextColumn::make('retention_days')->label('نگهداری (روز)'),
                IconColumn::make('consent_required_meetings')->label('رضایت جلسات')->boolean(),
                IconColumn::make('allow_store_transcripts')->label('ذخیره متن')->boolean(),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAiPolicies::route('/'),
            'create' => CreateAiPolicy::route('/create'),
            'edit' => EditAiPolicy::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return IamAuthorization::allows('ai.manage');
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allows('ai.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('ai.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('ai.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('ai.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    /**
     * @return array<string, string>
     */
    protected static function providerOptions(): array
    {
        $map = (array) config('filament-ai-core.provider_map', []);
        $options = [];

        foreach ($map as $key => $class) {
            if ($key !== 'mock' && ! config('filament-ai-core.providers.'.$key.'.enabled', false)) {
                continue;
            }

            $options[$key] = match ($key) {
                'mock' => 'شبیه‌ساز (Mock)',
                'n8n' => 'n8n',
                'openai' => 'OpenAI',
                default => $key,
            };
        }

        return $options;
    }
}
