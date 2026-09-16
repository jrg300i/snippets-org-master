<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SnippetController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\ThisCodeWorksStatusController;

Route::get('/', [LoginController::class, 'showLoginForm']);

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Rutas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'passwordUpdate'])
        ->name('profile.password.update');

    Route::post('/profile/thiscodeworks-api-key', [ProfileController::class, 'apiKeyUpdate'])
        ->name('profile.api-key.update');

    Route::get('/thiscodeworks/status', ThisCodeWorksStatusController::class)
        ->name('thiscodeworks.status');

    // Rutas para backup (CORREGIDAS)
    Route::get('/profile/backup', [ProfileController::class, 'backup'])->name('profile.backup');
    Route::post('/profile/backup', [ProfileController::class, 'backupStore'])->name('profile.backup.store');
    Route::get('/profile/stats', [ProfileController::class, 'stats'])->name('profile.stats');

    // Rutas para Categories
    Route::resource('categories', CategoryController::class);
    Route::post('/categories/{category}/publish', [CategoryController::class, 'publish'])
        ->name('categories.publish');

    // Rutas para Languages (CONSISTENTES)
    Route::resource('languages', LanguageController::class);

    // Rutas para Snippets
    Route::resource('snippets', SnippetController::class);
    Route::post('/snippets/{snippet}/publish', [SnippetController::class, 'publish'])
        ->name('snippets.publish');

    // Ruta para Estadísticas WEB (vista HTML)
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');

    // Auditoría (solo administradores)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/auditorias', [AuditoriaController::class, 'index'])->name('auditorias.index');
    });
});