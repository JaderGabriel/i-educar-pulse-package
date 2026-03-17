<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    'web',
    'auth',
    'can:' . config('ieducar-pulse.gate', 'viewPulse'),
])->group(function (): void {
    Route::get('/monitoramento/pulse', static function () {
        return view('ieducar-pulse::dashboard', [
            'title' => 'Monitoramento (Pulse)',
        ]);
    })->name('ieducar.pulse.dashboard');
});

