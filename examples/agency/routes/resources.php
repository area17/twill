<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\DisciplineController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\OfficeController;
use App\Http\Controllers\Api\SectorController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\WorkController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::middleware(SetLocale::class)->group(function () {
    Route::get('/home', [HomeController::class, 'index']);

    Route::prefix('about')->group(function () {
        Route::get('/', [AboutController::class, 'index']);
        Route::get('/{person}', [AboutController::class, 'person']);
    });

    Route::prefix('work')->group(function () {
        Route::get('/sectors', [SectorController::class, 'index']);
        Route::get('/sectors/{slug}', [WorkController::class, 'sectorWorks']);
        Route::get('/disciplines', [DisciplineController::class, 'index']);
        Route::get('/disciplines/{slug}', [WorkController::class, 'disciplineWorks']);
        Route::get('/', [WorkController::class, 'index']);
        Route::get('/{slug}', [WorkController::class, 'show']);
    });

    Route::get('/offices', [OfficeController::class, 'index']);
    Route::get('/settings', [SettingController::class, 'index']);
});
