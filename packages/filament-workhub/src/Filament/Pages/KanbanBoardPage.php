<?php

namespace Haida\FilamentWorkhub\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\WorkItemTransitionService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class KanbanBoardPage extends Page
{
    use AuthorizesIam;

    protected static ?string $navigationLabel = 'کانبان';

    protected static ?string $title = 'برد کانبان';

    protected string $view = 'filament-workhub::pages.kanban-board';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static string|\UnitEnum|null $navigationGroup = 'رهگیری کارها';

    protected static ?string $permission = 'workhub.work_item.view';

    public ?int $projectId = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $columns = [];

    public function mount(): void
    {
        $this->projectId = $this->projectId ?? Project::query()->value('id');
        $this->loadBoard();
    }

    public function updatedProjectId(): void
    {
        $this->loadBoard();
    }

    public function loadBoard(): void
    {
        $this->columns = [];

        $project = $this->projectId ? Project::query()->find($this->projectId) : null;
        if (! $project) {
            return;
        }

        $statuses = Status::query()
            ->where('workflow_id', $project->workflow_id)
            ->orderBy('sort_order')
            ->get();

        $items = WorkItem::query()
            ->where('project_id', $project->getKey())
            ->with(['assignee'])
            ->orderBy('sort_order')
            ->get()
            ->groupBy('status_id');

        $this->columns = $statuses->map(function (Status $status) use ($items) {
            $cards = $items->get($status->getKey(), collect())
                ->map(fn (WorkItem $item) => [
                    'id' => $item->getKey(),
                    'key' => $item->key,
                    'title' => $item->title,
                    'assignee' => $item->assignee?->name,
                    'priority' => $item->priority,
                ]);

            return [
                'status' => [
                    'id' => $status->getKey(),
                    'name' => $status->name,
                    'category' => $status->category,
                    'color' => $status->color,
                ],
                'items' => $cards,
            ];
        })->values()->toArray();
    }

    public function moveWorkItem(int $workItemId, int $statusId): void
    {
        $workItem = WorkItem::query()->find($workItemId);
        if (! $workItem) {
            return;
        }

        try {
            app(WorkItemTransitionService::class)->transition($workItem, $statusId);
            $this->loadBoard();
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'انتقال مجاز نیست.';

            Notification::make()
                ->title('انتقال مجاز نیست')
                ->body($message)
                ->danger()
                ->send();
        } catch (\Throwable $exception) {
            Notification::make()
                ->title('انتقال مجاز نیست')
                ->body('امکان انتقال آیتم وجود ندارد.')
                ->danger()
                ->send();
        }
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getProjectOptions(): Collection
    {
        return Project::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Project $project) => [
                'id' => $project->getKey(),
                'label' => $project->key.' - '.$project->name,
            ]);
    }
}
