<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\EmployeeResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\EmployeeResource;

class ListEmployees extends ListRecordsWithCreate
{
    protected static string $resource = EmployeeResource::class;
}
