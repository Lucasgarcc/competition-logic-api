<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampeonatoController;

Route::get('/', function () {
    return view('app');
});

// Rota para Campeonatos (dentro de resources/views/layouts/campeonatos.blade.php)
Route::get('/campeonatos', function () {
    return view('layouts.campeonatos');
});

// Rota para Estatísticas (dentro de resources/views/componentes/estatisticas.blade.php)
Route::get('/estatisticas', function () {
    return view('components.estatisticas');
});

/** Rest Full */
Route::post('/simular-partida',[CampeonatoController::class, 'simularPartida']);
Route::get('/simular-partida-teste',[CampeonatoController::class, 'simularPartida']);
Route::get('/campeonato/status-atual',[CampeonatoController::class, 'statusAtual']);

Route::get('/campeonatos', [CampeonatoController::class, 'index']);
Route::get('/estatisticas-globais', [CampeonatoController::class, 'estatisticasGlobais']);