<?php

namespace Haida\FilamentNotify\Core\Models\Concerns;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

trait HasPanelId
{
    protected static function bootHasPanelId(): void
    {
        static::creating(function ($model): void {
            if (filled($model->panel_id)) {
                return;
            }

            $panelId = Filament::getCurrentPanel()?->getId();
            if ($panelId) {
                $model->panel_id = $panelId;
            }
        });
    }

    public function scopeForPanel(Builder $query, ?string $panelId = null): Builder
    {
        $panelId ??= Filament::getCurrentPanel()?->getId();
        if (! $panelId) {
            return $query;
        }

        return $query->where('panel_id', $panelId);
    }
}
