<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Filament\Resources\QuickActionResource;
use Filamat\IamSuite\Models\QuickAction;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    use AuthorizesIam;

    protected static ?string $permission = 'iam.view';

    protected string $view = 'filamat-iam::widgets.quick-actions-widget';

    protected int|string|array $columnSpan = ['default' => 'full'];

    /**
     * @return array{actions: array<int, array{label: string, description: string, icon: string, url: string, rank: int, editUrl: string|null}>, addUrl: string, manageUrl: string, canManage: bool, canCreate: bool}
     */
    protected function getViewData(): array
    {
        $actions = $this->resolveUserActions();

        return [
            'actions' => $actions,
            'addUrl' => QuickActionResource::getUrl('create'),
            'manageUrl' => QuickActionResource::getUrl('index'),
            'canManage' => QuickActionResource::canViewAny(),
            'canCreate' => QuickActionResource::canCreate(),
        ];
    }

    /**
     * @return array<int, array{label: string, description: string, icon: string, url: string, rank: int, editUrl: string|null}>
     */
    private function resolveUserActions(): array
    {
        $userId = auth()->id();
        if (! $userId) {
            return [];
        }

        $panelId = Filament::getCurrentPanel()?->getId();
        $tenantId = TenantContext::getTenantId();

        $query = QuickAction::query()
            ->where('user_id', $userId)
            ->where('is_active', true);

        if ($panelId) {
            $query->where('panel_id', $panelId);
        }

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        } else {
            $query->whereNull('tenant_id');
        }

        $items = $query->orderBy('sort')->get();

        return $items->map(function (QuickAction $action, int $index): array {
            $rank = (int) ($action->rank ?: ($action->sort ?: ($index + 1)));
            if ($rank < 1) {
                $rank = $index + 1;
            }

            return [
                'label' => $action->label,
                'description' => $action->description ?: 'بدون توضیح',
                'icon' => $action->icon ?: 'heroicon-o-bolt',
                'url' => $action->url,
                'rank' => $rank,
                'editUrl' => QuickActionResource::canEdit($action)
                    ? QuickActionResource::getUrl('edit', ['record' => $action])
                    : null,
            ];
        })->all();
    }
}
