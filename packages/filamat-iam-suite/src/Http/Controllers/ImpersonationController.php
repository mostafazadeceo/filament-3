<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers;

use Filamat\IamSuite\Services\ImpersonationService;
use Illuminate\Http\RedirectResponse;

class ImpersonationController
{
    public function stop(ImpersonationService $service): RedirectResponse
    {
        $service->stop();

        return redirect()->to('/');
    }
}
