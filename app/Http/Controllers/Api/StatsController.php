<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Snippet;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $stats = [
                'total_snippets' => Snippet::count(),
                'total_categories' => Category::count(),
                'total_languages' => Language::count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Stats retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving stats'
            ], 500);
        }
    }
}
