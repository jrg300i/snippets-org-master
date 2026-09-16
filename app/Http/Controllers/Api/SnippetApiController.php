<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Snippet;

class SnippetApiController extends Controller
{
    /**
     * Listado público de snippets (estilo explore de thiscodeworks).
     */
    public function index()
    {
        $snippets = Snippet::with(['category', 'language', 'user'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $snippets,
            'message' => 'Snippets obtenidos correctamente',
            'count' => $snippets->count(),
        ], 200);
    }

    /**
     * Detalle de un snippet por su ID.
     */
    public function show($id)
    {
        $snippet = Snippet::with(['category', 'language', 'user'])->find($id);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $snippet,
            'message' => 'Snippet obtenido correctamente',
        ], 200);
    }

    /**
     * Listado de lenguajes disponibles (reemplaza el closure inline de routes/api.php).
     */
    public function languages()
    {
        $languages = Language::active()->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $languages,
            'message' => 'Lenguajes obtenidos correctamente',
            'count' => $languages->count(),
        ], 200);
    }
}