<?php

namespace Haida\FilamentLoyaltyClub\Policies;

use Haida\FilamentLoyaltyClub\Models\LoyaltySegment;
use Haida\FilamentLoyaltyClub\Policies\Concerns\HandlesLoyaltyPermissions;

class LoyaltySegmentPolicy
{
    use HandlesLoyaltyPermissions;

    public function viewAny(): bool
    {
        return $this->allow('loyalty.segment.view');
    }

    public function view(LoyaltySegment $record): bool
    {
        return $this->allow('loyalty.segment.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('loyalty.segment.manage');
    }

    public function update(LoyaltySegment $record): bool
    {
        return $this->allow('loyalty.segment.manage', $record);
    }

    public function delete(LoyaltySegment $record): bool
    {
        return $this->allow('loyalty.segment.manage', $record);
    }

    public function restore(LoyaltySegment $record): bool
    {
        return $this->allow('loyalty.segment.manage', $record);
    }

    public function forceDelete(LoyaltySegment $record): bool
    {
        return $this->allow('loyalty.segment.manage', $record);
    }
}
