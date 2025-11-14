<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SnippetApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\StatsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Ruta de prueba
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'Snippet Organizer API is running',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Snippets API Routes
Route::get('/snippets', [SnippetApiController::class, 'index']);
Route::post('/snippets', [SnippetApiController::class, 'store']);
Route::get('/snippets/{id}', [SnippetApiController::class, 'show']);

// Categories API Routes
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::post('/categories', [CategoryApiController::class, 'store']);
Route::get('/categories/{id}', [CategoryApiController::class, 'show']);

// Stats API Route
Route::get('/stats', [StatsController::class, 'index']);

// Languages API Route (temporal con closure)
Route::get('/languages', function () {
    try {
        $languages = \App\Models\Language::withCount('snippets')->get();
        return response()->json([
            'success' => true,
            'data' => $languages,
            'count' => $languages->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving languages'
        ], 500);
    }
});
