<?php

namespace Haida\FilamentWorkhub\Http\Resources;

use Haida\FilamentWorkhub\Support\EntityReferenceRegistry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntityReferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $registry = app(EntityReferenceRegistry::class);
        $definition = $registry->get($this->target_type);
        $targetLabel = null;
        $targetUrl = null;

        if ($definition && class_exists($definition['model'])) {
            $modelClass = $definition['model'];
            $target = $modelClass::query()->find($this->target_id);
            if ($target) {
                $targetLabel = $registry->resolveLabel($this->target_type, $target);
                $resolver = $definition['url'] ?? null;
                $targetUrl = $resolver ? $resolver($target) : null;
            }
        }

        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'source_type' => $this->source_type,
            'source_id' => $this->source_id,
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
            'target_label' => $targetLabel,
            'target_url' => $targetUrl,
            'relation_type' => $this->relation_type,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
