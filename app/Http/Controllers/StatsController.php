<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Language;
use App\Models\Snippet;

class StatsController extends Controller
{
    public function index()
    {
        try {
            $userId = auth()->id();
            $countByUser = fn ($query) => $query->where('user_id', $userId);

            // Obtener datos para la vista (solo del usuario autenticado)
            $totalSnippets = Snippet::forCurrentUser()->count();
            $totalCategories = Category::count();
            $totalLanguages = Language::count();

            $popularLanguages = Language::withCount(['snippets' => $countByUser])
                ->orderBy('snippets_count', 'desc')
                ->take(10)
                ->get();

            $popularCategories = Category::withCount(['snippets' => $countByUser])
                ->orderBy('snippets_count', 'desc')
                ->take(10)
                ->get();

            $recentSnippets = Snippet::with(['category', 'language'])
                ->forCurrentUser()
                ->latest()
                ->take(10)
                ->get();

            $languageDistribution = Language::withCount(['snippets' => $countByUser])->get();
            $categoryDistribution = Category::withCount(['snippets' => $countByUser])->get();

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
