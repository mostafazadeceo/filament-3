<?php

namespace Haida\FilamentWorkhub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tab;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\Pages\CreateWorkItem;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\Pages\EditWorkItem;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\Pages\ListWorkItems;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\Pages\ViewWorkItem;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers\AttachmentsRelationManager;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers\AuditEventsRelationManager;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers\CommentsRelationManager;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers\DecisionsRelationManager;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers\LinksRelationManager;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers\TimeEntriesRelationManager;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers\WatchersRelationManager;
use Haida\FilamentWorkhub\Models\CustomField;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Models\WorkType;
use Illuminate\Database\Eloquent\Builder;

class WorkItemResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'workhub.work_item';

    protected static ?string $model = WorkItem::class;

    protected static ?string $navigationLabel = 'آیتم‌های کاری';

    protected static ?string $pluralModelLabel = 'آیتم‌های کاری';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'رهگیری کارها';

    public static function form(Schema $schema): Schema
    {
        $customFields = static::customFieldComponents();

        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('project_id')
                    ->label('پروژه')
                    ->options(fn () => Project::query()->get()->mapWithKeys(fn (Project $project) => [
                        $project->getKey() => $project->key.' - '.$project->name,
                    ])->toArray())
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set): void {
                        if (! $state) {
                            $set('workflow_id', null);
                            $set('status_id', null);

                            return;
                        }

                        $workflowId = Project::query()->whereKey($state)->value('workflow_id');
                        $set('workflow_id', $workflowId);

                        $statusId = Status::query()
                            ->where('workflow_id', $workflowId)
                            ->orderByDesc('is_default')
                            ->orderBy('sort_order')
                            ->value('id');

                        $set('status_id', $statusId);
                    }),
                Select::make('work_type_id')
                    ->label('نوع کار')
                    ->options(fn () => WorkType::query()->where('is_active', true)->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('status_id')
                    ->label('وضعیت')
                    ->options(function (Get $get) {
                        $projectId = $get('project_id');
                        if (! $projectId) {
                            return Status::query()->pluck('name', 'id')->toArray();
                        }

                        $workflowId = Project::query()->whereKey($projectId)->value('workflow_id');

                        return Status::query()
                            ->where('workflow_id', $workflowId)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required(),
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(5)
                    ->nullable(),
                Select::make('priority')
                    ->label('اولویت')
                    ->options(config('filament-workhub.work_item.priorities'))
                    ->default('medium')
                    ->required(),
                Select::make('reporter_id')
                    ->label('گزارش‌دهنده')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->default(fn () => auth()->id())
                    ->required(),
                Select::make('assignee_id')
                    ->label('مسئول')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                DatePicker::make('due_date')->label('تاریخ سررسید')->nullable(),
                TextInput::make('estimate_minutes')
                    ->label('برآورد (دقیقه)')
                    ->numeric()
                    ->nullable(),
                Select::make('labels')
                    ->label('برچسب‌ها')
                    ->multiple()
                    ->relationship('labels', 'name')
                    ->saveRelationshipsUsing(function (WorkItem $record, array $state): void {
                        $tenantId = $record->tenant_id ?? TenantContext::getTenantId();
                        $syncData = collect($state)
                            ->mapWithKeys(fn ($labelId) => [$labelId => ['tenant_id' => $tenantId]])
                            ->toArray();

                        $record->labels()->sync($syncData);
                    })
                    ->preload()
                    ->searchable(),
                Hidden::make('workflow_id'),
                Section::make('فیلدهای سفارشی')
                    ->schema($customFields)
                    ->columns(2)
                    ->visible(fn () => $customFields !== []),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('کلید')->searchable(),
                TextColumn::make('title')->label('عنوان')->searchable()->limit(40),
                TextColumn::make('project.name')->label('پروژه'),
                TextColumn::make('status.name')
                    ->label('وضعیت')
                    ->badge()
                    ->color(fn (WorkItem $record) => match ($record->status?->category) {
                        'done' => 'success',
                        'in_progress' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('priority')
                    ->label('اولویت')
                    ->formatStateUsing(fn (string $state) => config('filament-workhub.work_item.priorities.'.$state, $state)),
                TextColumn::make('assignee.name')->label('مسئول'),
                TextColumn::make('due_date')->label('سررسید'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ])
            ->filters([
                SelectFilter::make('status_id')
                    ->label('وضعیت')
                    ->options(fn () => Status::query()->pluck('name', 'id')->toArray()),
                SelectFilter::make('assignee_id')
                    ->label('مسئول')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray()),
                SelectFilter::make('priority')
                    ->label('اولویت')
                    ->options(config('filament-workhub.work_item.priorities')),
                SelectFilter::make('labels')
                    ->label('برچسب')
                    ->multiple()
                    ->relationship('labels', 'name'),
                Filter::make('due_date')
                    ->form([
                        DatePicker::make('from')->label('از'),
                        DatePicker::make('until')->label('تا'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $query, $date) => $query->whereDate('due_date', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $query, $date) => $query->whereDate('due_date', '<=', $date));
                    }),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'project',
                'status',
                'assignee',
                'workType',
                'labels',
                'customFieldValues.field',
                'aiFieldRuns',
                'aiSummaries' => function (Builder $query) {
                    $userId = auth()->id();
                    $query->where(function (Builder $builder) use ($userId) {
                        $builder->where('type', 'shared');

                        if ($userId) {
                            $builder->orWhere(function (Builder $inner) use ($userId) {
                                $inner->where('type', 'ttl')
                                    ->where('created_by', $userId);
                            });
                        }
                    })
                        ->where(function (Builder $builder) {
                            $builder->whereNull('ttl_expires_at')
                                ->orWhere('ttl_expires_at', '>', now());
                        })
                        ->latest();
                },
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('work_item_tabs')
                    ->schema([
                        Tab::make('نمای کلی')
                            ->schema([
                                Section::make('مشخصات')
                                    ->schema([
                                        TextEntry::make('key')->label('کلید'),
                                        TextEntry::make('title')->label('عنوان'),
                                        TextEntry::make('project.name')->label('پروژه'),
                                        TextEntry::make('status.name')->label('وضعیت'),
                                        TextEntry::make('priority')
                                            ->label('اولویت')
                                            ->formatStateUsing(fn (string $state) => config('filament-workhub.work_item.priorities.'.$state, $state)),
                                        TextEntry::make('assignee.name')->label('مسئول'),
                                        TextEntry::make('reporter.name')->label('گزارش‌دهنده'),
                                        TextEntry::make('due_date')->label('سررسید'),
                                    ])
                                    ->columns(2),
                            ]),
                        Tab::make('شرح')
                            ->schema([
                                Section::make('توضیحات')
                                    ->schema([
                                        TextEntry::make('description')->label('شرح')->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('هوش مصنوعی')
                            ->schema([
                                Section::make('خلاصه‌های هوش مصنوعی')
                                    ->schema([
                                        RepeatableEntry::make('aiSummaries')
                                            ->label('خلاصه‌ها')
                                            ->schema([
                                                TextEntry::make('summary_json')
                                                    ->label('نوع خلاصه')
                                                    ->formatStateUsing(fn ($state) => data_get($state, 'kind', 'summary')),
                                                TextEntry::make('type')->label('نوع ذخیره'),
                                                TextEntry::make('provider')->label('ارائه‌دهنده'),
                                                TextEntry::make('created_at')->label('ایجاد'),
                                                TextEntry::make('summary_json')
                                                    ->label('متن خلاصه')
                                                    ->columnSpanFull()
                                                    ->markdown()
                                                    ->formatStateUsing(function ($state) {
                                                        $sections = (array) data_get($state, 'sections', []);
                                                        if ($sections === []) {
                                                            return (string) data_get($state, 'text', '');
                                                        }

                                                        $chunks = [];
                                                        foreach ($sections as $title => $items) {
                                                            $lines = array_map(fn ($item) => '- '.(string) $item, (array) $items);
                                                            $chunks[] = '### '.$title."\n".implode("\n", $lines);
                                                        }

                                                        return implode("\n\n", $chunks);
                                                    })
                                                    ->copyable(),
                                            ])
                                            ->columns(4)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                                Section::make('فیلدهای هوش مصنوعی')
                                    ->schema([
                                        RepeatableEntry::make('ai_fields')
                                            ->label('فیلدها')
                                            ->state(fn (WorkItem $record) => static::aiFieldSnapshot($record))
                                            ->schema([
                                                TextEntry::make('name')->label('نام فیلد'),
                                                TextEntry::make('key')->label('کلید'),
                                                TextEntry::make('status')->label('وضعیت'),
                                                TextEntry::make('last_run_at')->label('آخرین اجرا'),
                                                TextEntry::make('value')
                                                    ->label('خروجی')
                                                    ->columnSpanFull()
                                                    ->markdown()
                                                    ->copyable(),
                                            ])
                                            ->columns(4)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                                Section::make('راهنما')
                                    ->schema([
                                        TextEntry::make('ai_help')
                                            ->label('نکته')
                                            ->state('برای تولید خلاصه‌ها و فیلدهای هوش مصنوعی از دکمه‌های بالای صفحه استفاده کنید.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkItems::route('/'),
            'create' => CreateWorkItem::route('/create'),
            'edit' => EditWorkItem::route('/{record}/edit'),
            'view' => ViewWorkItem::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
            AttachmentsRelationManager::class,
            WatchersRelationManager::class,
            DecisionsRelationManager::class,
            TimeEntriesRelationManager::class,
            AuditEventsRelationManager::class,
            LinksRelationManager::class,
        ];
    }

    /**
     * @return array<int, \\Filament\\Forms\\Components\\Component>
     */
    protected static function customFieldComponents(): array
    {
        $fields = CustomField::query()
            ->where('scope', 'work_item')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($fields->isEmpty()) {
            return [];
        }

        return $fields->map(function (CustomField $field) {
            $name = 'custom_fields.'.$field->key;

            $component = match ($field->type) {
                'textarea' => Textarea::make($name)->rows(4),
                'number' => TextInput::make($name)->numeric(),
                'date' => DatePicker::make($name),
                'boolean' => Toggle::make($name),
                'select' => Select::make($name)->options((array) ($field->settings['options'] ?? [])),
                'multi_select' => Select::make($name)->multiple()->options((array) ($field->settings['options'] ?? [])),
                'ai_field' => Textarea::make($name)
                    ->rows(3)
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state) => is_array($state)
                        ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                        : (string) $state)
                    ->helperText('این مقدار توسط هوش مصنوعی تولید می‌شود.'),
                default => TextInput::make($name),
            };

            return $component
                ->label($field->name)
                ->required((bool) $field->is_required && $field->type !== 'ai_field');
        })->values()->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected static function aiFieldSnapshot(WorkItem $record): array
    {
        $fields = CustomField::query()
            ->where('scope', 'work_item')
            ->where('type', 'ai_field')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($fields->isEmpty()) {
            return [];
        }

        $values = $record->customFieldValues->keyBy('field_id');
        $latestRuns = $record->aiFieldRuns
            ->sortByDesc('created_at')
            ->groupBy('field_id');

        return $fields->map(function (CustomField $field) use ($values, $latestRuns) {
            $value = $values->get($field->getKey())?->value;
            $run = $latestRuns->get($field->getKey())?->first();

            return [
                'name' => $field->name,
                'key' => $field->key,
                'status' => $run ? 'تولید شده' : 'در انتظار',
                'last_run_at' => $run?->created_at?->toDateTimeString(),
                'value' => static::formatAiFieldValue($value),
            ];
        })->values()->all();
    }

    protected static function formatAiFieldValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_array($value)) {
            return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return (string) $value;
    }
}
