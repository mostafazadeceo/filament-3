<?php

namespace Haida\FilamentRelograde\Enums;

enum OrderStatus: string
{
    case Created = 'created';
    case Pending = 'pending';
    case Finished = 'finished';
    case Cancelled = 'cancelled';
    case Deleted = 'deleted';
}
