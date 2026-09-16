<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\Snippet;

class HomeController extends Controller
{
    public function index()
    {
        $snippetsCount = Snippet::count();
        $categoriesCount = Category::count();
        $languagesCount = Language::count();

        $recentSnippets = Snippet::with(['category', 'language'])->latest()->take(6)->get();
        $categories = Category::withCount('snippets')->latest()->take(6)->get();
        $languages = Language::withCount('snippets')->latest()->take(6)->get();

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