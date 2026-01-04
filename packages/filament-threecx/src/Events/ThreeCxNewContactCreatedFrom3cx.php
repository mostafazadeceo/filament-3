<?php

namespace Haida\FilamentThreeCx\Events;

use Haida\FilamentThreeCx\Models\ThreeCxContact;

class ThreeCxNewContactCreatedFrom3cx
{
    public const NAME = 'threecx.new_contact_created';

    public function __construct(public ThreeCxContact $contact) {}
}
