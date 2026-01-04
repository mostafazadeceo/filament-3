<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserInvitationResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\UserInvitationResource;

class ListUserInvitations extends ListRecordsWithCreate
{
    protected static string $resource = UserInvitationResource::class;
}
