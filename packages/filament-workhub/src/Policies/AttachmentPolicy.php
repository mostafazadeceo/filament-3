<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\Attachment;

class AttachmentPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.attachment.view',
            'workhub.attachment.manage',
        ], null, $user);
    }

    public function view(User $user, Attachment $attachment): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.attachment.view',
            'workhub.attachment.manage',
        ], IamAuthorization::resolveTenantFromRecord($attachment), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.attachment.manage', null, $user);
    }

    public function delete(User $user, Attachment $attachment): bool
    {
        return IamAuthorization::allows('workhub.attachment.manage', IamAuthorization::resolveTenantFromRecord($attachment), $user);
    }
}
