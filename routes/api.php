<?php

use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\SnippetApiController;
use App\Http\Controllers\Api\StatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API pública de lectura (estilo explore de thiscodeworks.com).
| Los endpoints de escritura se delegan a los controladores web
| (SnippetController) que integran ThisCodeWorksService.
|
*/

Route::get('/health', HealthController::class);

// Snippets API Routes
Route::get('/snippets', [SnippetApiController::class, 'index']);
Route::get('/snippets/{id}', [SnippetApiController::class, 'show']);

// Categories API Routes
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::get('/categories/{id}', [CategoryApiController::class, 'show']);

// Languages API Route
Route::get('/languages', [SnippetApiController::class, 'languages']);

// Stats API Route
Route::get('/stats', [StatsController::class, 'index']);