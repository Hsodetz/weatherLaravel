<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Http\Middleware\HandleCors as Cors;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(Cors::class)->group(function () {
    Route::get('/users-weather', [UserController::class, 'getUsersWeather']);
    Route::get('/users/{id}/weather', [UserController::class, 'showWeather']);
});


