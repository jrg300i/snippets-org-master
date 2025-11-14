<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SnippetApiController extends Controller
{
    //
}<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Snippet;
use Illuminate\Http\Request;

class SnippetApiController extends Controller
{
    public function index()
    {
        try {
            $snippets = Snippet::with(['category', 'language'])->latest()->get();
            
            return response()->json([
                'success' => true,
                'data' => $snippets,
                'message' => 'Snippets retrieved successfully',
                'count' => $snippets->count()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving snippets'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $snippet = Snippet::with(['category', 'language'])->find($id);

            if (!$snippet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Snippet not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $snippet,
                'message' => 'Snippet retrieved successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving snippet'
            ], 500);
        }
    }
}
