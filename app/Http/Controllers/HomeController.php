<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\Snippet;

class HomeController extends Controller
{
    public function index()
    {
        $snippetsCount = Snippet::forCurrentUser()->count();
        $categoriesCount = Category::count();
        $languagesCount = Language::count();

        $recentSnippets = Snippet::with(['category', 'language'])
            ->forCurrentUser()
            ->latest()
            ->take(6)
            ->get();

        $userId = auth()->id();
        $countByUser = fn ($query) => $query->where('user_id', $userId);

        $categories = Category::withCount(['snippets' => $countByUser])->latest()->take(6)->get();
        $languages = Language::withCount(['snippets' => $countByUser])->latest()->take(6)->get();

        return view('home', compact(
            'snippetsCount',
            'categoriesCount',
            'languagesCount',
            'recentSnippets',
            'categories',
            'languages'
        ));
    }
}