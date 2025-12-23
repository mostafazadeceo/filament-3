<?php

namespace Haida\FilamentNotify\Core\Support\Rendering;

class RenderedMessage
{
    public function __construct(
        public ?string $subject,
        public string $body,
        public array $meta = [],
    ) {}
}
