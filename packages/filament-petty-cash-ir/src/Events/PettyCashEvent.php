<?php

namespace Haida\FilamentPettyCashIr\Events;

use Illuminate\Database\Eloquent\Model;

class PettyCashEvent
{
    public function __construct(
        public string $type,
        public Model $subject
    ) {}
}
