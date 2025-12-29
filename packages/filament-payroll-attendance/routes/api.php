<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')
    ->prefix(config('filament-payroll-attendance.api.prefix'))
    ->group(function (): void {
        // API routes will be registered in later milestones.
    });
