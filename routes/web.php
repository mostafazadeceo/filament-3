<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KycDefenseController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/ttt', [KycDefenseController::class, 'index'])->name('ttt.dashboard');
    Route::post('/ttt/analyze', [KycDefenseController::class, 'analyze'])->name('ttt.analyze');
});
