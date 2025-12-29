<?php

namespace Haida\FilamentWorkhub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Haida\FilamentWorkhub\Filament\Resources\ProjectResource\Pages\CreateProject;
use Haida\FilamentWorkhub\Filament\Resources\ProjectResource\Pages\EditProject;
use Haida\FilamentWorkhub\Filament\Resources\ProjectResource\Pages\ListProjects;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\Workflow;
use Haida\FilamentWorkhub\Support\EntityReferenceRegistry;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'workhub.project';

    protected static ?string $model = Project::class;

    protected static ?string $navigationLabel = 'پروژه‌ها';

    protected static ?string $pluralModelLabel = 'پروژه‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'رهگیری کارها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('key')
                    ->label('کلید')
                    ->required()
                    ->maxLength(16)
                    ->regex('/^[A-Z0-9_-]+$/')
                    ->dehydrateStateUsing(fn ($state) => strtoupper((string) $state))
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('نام پروژه')
                    ->required()
                    ->maxLength(255),
                Select::make('workflow_id')
                    ->label('گردش‌کار')
                    ->options(fn () => Workflow::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'archived' => 'بایگانی',
                    ])
                    ->default('active')
                    ->required(),
                Select::make('lead_user_id')
                    ->label('رهبر پروژه')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                DatePicker::make('start_date')->label('تاریخ شروع')->nullable(),
                DatePicker::make('due_date')->label('تاریخ سررسید')->nullable(),
                Select::make('allowed_link_types')
                    ->label('انواع لینک مجاز')
                    ->options(function () {
                        $registry = app(EntityReferenceRegistry::class);

                        return collect($registry->all())
                            ->mapWithKeys(fn (array $item) => [$item['type'] => $item['label']])
                            ->toArray();
                    })
                    ->multiple()
                    ->searchable()
                    ->helperText('اگر خالی باشد، همه انواع لینک مجاز هستند.')
                    ->nullable(),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(4)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('کلید')->searchable(),
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('workflow.name')->label('گردش‌کار'),
                TextColumn::make('lead.name')->label('رهبر پروژه'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active' => 'فعال',
                        'archived' => 'بایگانی',
                        default => $state,
                    }),
                TextColumn::make('work_items_count')->label('آیتم‌ها')->counts('workItems'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'archived' => 'بایگانی',
                    ]),
                SelectFilter::make('workflow_id')
                    ->label('گردش‌کار')
                    ->options(fn () => Workflow::query()->pluck('name', 'id')->toArray()),
                SelectFilter::make('lead_user_id')
                    ->label('رهبر پروژه')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray()),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['workflow', 'lead'])
            ->withCount('workItems');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
