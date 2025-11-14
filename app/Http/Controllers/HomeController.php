<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $snippetsCount = 0;
        $categoriesCount = 0;
        $languagesCount = 0;

        try {
            if (class_exists('App\\Models\\Snippet')) {
                $snippetsCount = \App\Models\Snippet::count();
            }
        } catch (\Exception $e) {
            $snippetsCount = 0;
        }

        try {
            if (class_exists('App\\Models\\Category')) {
                $categoriesCount = \App\Models\Category::count();
            }
        } catch (\Exception $e) {
            $categoriesCount = 0;
        }

        try {
            if (class_exists('App\\Models\\Language')) {
                $languagesCount = \App\Models\Language::count();
            }
        } catch (\Exception $e) {
            $languagesCount = 0;
        }

        return view('home', compact('snippetsCount', 'categoriesCount', 'languagesCount'));
    }
}