<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampeonatoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/simular-partida',[CampeonatoController::class, 'simularPartida']);