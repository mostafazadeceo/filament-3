<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentWorkhub\Support\EntityReferenceRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema as SchemaFacade;

class LinksRelationManager extends RelationManager
{
    protected static string $relationship = 'links';

    protected static ?string $title = 'لینک‌ها';

    public function form(Schema $schema): Schema
    {
        $registry = app(EntityReferenceRegistry::class);
        $workItem = $this->getOwnerRecord();
        $workItem->loadMissing('project');
        $allowedTypes = $workItem->project?->allowed_link_types ?? [];

        $options = collect($registry->all())
            ->filter(function (array $item) use ($allowedTypes) {
                if ($allowedTypes === [] || $allowedTypes === null) {
                    return true;
                }

                return in_array($item['type'], $allowedTypes, true);
            })
            ->mapWithKeys(fn (array $item) => [$item['type'] => $item['label']])
            ->toArray();

        return $schema
            ->schema([
                Select::make('target_type')
                    ->label('نوع لینک')
                    ->options($options)
                    ->searchable()
                    ->required()
                    ->reactive(),
                Select::make('target_id')
                    ->label('مقصد')
                    ->searchable()
                    ->required()
                    ->getSearchResultsUsing(fn (string $search, Get $get) => $this->searchTargets($registry, $get('target_type'), $search))
                    ->getOptionLabelUsing(fn ($value, Get $get) => $this->resolveTargetLabel($registry, $get('target_type'), $value)),
                TextInput::make('relation_type')
                    ->label('نوع ارتباط')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('target_type')->label('نوع'),
                TextColumn::make('target_id')->label('شناسه'),
                TextColumn::make('relation_type')->label('ارتباط'),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $this->getOwnerRecord()->tenant_id;
        $data['source_type'] = $this->getOwnerRecord()::class;
        $data['source_id'] = $this->getOwnerRecord()->getKey();

        return $data;
    }

    protected function searchTargets(EntityReferenceRegistry $registry, ?string $type, string $search): array
    {
        if (! $type) {
            return [];
        }

        $definition = $registry->get($type);
        if (! $definition) {
            return [];
        }

        $modelClass = $definition['model'];
        /** @var Builder $query */
        $query = $modelClass::query();
        $this->applySearchFilters($query, $search);

        return $query
            ->limit(20)
            ->get()
            ->mapWithKeys(fn (Model $model) => [
                $model->getKey() => $registry->resolveLabel($type, $model) ?? (string) $model->getKey(),
            ])
            ->toArray();
    }

    protected function resolveTargetLabel(EntityReferenceRegistry $registry, ?string $type, $value): ?string
    {
        if (! $type || ! $value) {
            return null;
        }

        $definition = $registry->get($type);
        if (! $definition) {
            return null;
        }

        $modelClass = $definition['model'];
        /** @var Model|null $model */
        $model = $modelClass::query()->find($value);

        return $model ? $registry->resolveLabel($type, $model) : null;
    }

    protected function applySearchFilters(Builder $query, string $search): void
    {
        if ($search === '') {
            return;
        }

        $table = $query->getModel()->getTable();
        $columns = ['name', 'title', 'key'];

        $query->where(function (Builder $builder) use ($table, $columns, $search) {
            foreach ($columns as $column) {
                if (SchemaFacade::hasColumn($table, $column)) {
                    $builder->orWhere($table.'.'.$column, 'like', '%'.$search.'%');
                }
            }
        });
    }
}
