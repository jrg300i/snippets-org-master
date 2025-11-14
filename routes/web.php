<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SnippetController;
use App\Http\Controllers\StatsController;

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Rutas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'passwordUpdate'])
        ->name('profile.password.update');

    // Ruta para backup (AGREGAR ESTA LÍNEA)


    
    Route::post('/profile/backup', [ProfileController::class, 'backup'])->name('profile.backup.create');
    Route::get('/profile/stats', [ProfileController::class, 'stats'])->name('profile.stats');


    // Rutas para Categories
    Route::resource('categories', CategoryController::class);

    // Rutas para Languages

    Route::get('/languages', [LanguageController::class, 'index'])->name('languages.index');
    Route::get('/languages/create', [LanguageController::class, 'create'])->name('languages.create');
    Route::post('/languages', [LanguageController::class, 'store'])->name('languages.store');
    Route::get('/languages/{language}', [LanguageController::class, 'show'])->name('languages.show');
    Route::get('/languages/{language}/edit', [LanguageController::class, 'edit'])->name('languages.edit');
    Route::put('/languages/{language}', [LanguageController::class, 'update'])->name('languages.update');
    Route::delete('/languages/{language}', [LanguageController::class, 'destroy'])->name('languages.destroy');

    // Rutas para Snippets
    Route::resource('snippets', SnippetController::class);

    // Ruta para Estadísticas WEB (vista HTML)
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
});
