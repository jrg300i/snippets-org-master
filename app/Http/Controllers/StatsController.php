<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index()
    {
        try {
            // Obtener datos para la vista
            $totalSnippets = \App\Models\Snippet::count();
            $totalCategories = \App\Models\Category::count();
            $totalLanguages = \App\Models\Language::count();
            
            $popularLanguages = \App\Models\Language::withCount('snippets')
                ->orderBy('snippets_count', 'desc')
                ->take(10)
                ->get();

            $popularCategories = \App\Models\Category::withCount('snippets')
                ->orderBy('snippets_count', 'desc')
                ->take(10)
                ->get();

            $recentSnippets = \App\Models\Snippet::with(['category', 'language'])
                ->latest()
                ->take(10)
                ->get();

            $languageDistribution = \App\Models\Language::withCount('snippets')->get();
            $categoryDistribution = \App\Models\Category::withCount('snippets')->get();

            return view('stats.index', compact(
                'totalSnippets',
                'totalCategories',
                'totalLanguages',
                'popularLanguages',
                'popularCategories',
                'recentSnippets',
                'languageDistribution',
                'categoryDistribution'
            ));

        } catch (\Exception $e) {
            return view('stats.index')->with('error', 'Error al cargar las estadísticas: ' . $e->getMessage());
        }
    }
}
