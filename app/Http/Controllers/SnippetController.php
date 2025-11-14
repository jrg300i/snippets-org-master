<?php

namespace App\Http\Controllers;

use App\Models\Snippet;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;

class SnippetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Solo snippets del usuario autenticado
        $snippets = Snippet::with(['category', 'language', 'user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        // Filtrar snippets con y sin lenguaje
        $snippetsWithLanguage = $snippets->filter(function($snippet) {
            return $snippet->language !== null;
        });
        
        $snippetsWithoutLanguage = $snippets->filter(function($snippet) {
            return $snippet->language === null;
        });

        return view('snippets.index', compact(
            'snippets', 
            'snippetsWithLanguage', 
            'snippetsWithoutLanguage'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $languages = Language::active()->get();
        
        return view('snippets.create', compact('categories', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'language_id' => 'required|exists:languages,id',
            'description' => 'nullable|string'
        ]);

        // Agregar el user_id automáticamente
        $validated['user_id'] = auth()->id();

        Snippet::create($validated);

        return redirect()->route('snippets.index')
            ->with('success', 'Snippet creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Snippet $snippet)
    {
        // Verificar que el snippet pertenezca al usuario
        $this->authorizeSnippet($snippet);

        $snippet->load(['category', 'language', 'user']);
        return view('snippets.show', compact('snippet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Snippet $snippet)
    {
        // Verificar que el snippet pertenezca al usuario
        $this->authorizeSnippet($snippet);

        $categories = Category::all();
        $languages = Language::active()->get();

        return view('snippets.edit', compact('snippet', 'categories', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Snippet $snippet)
    {
        // Verificar que el snippet pertenezca al usuario
        $this->authorizeSnippet($snippet);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'language_id' => 'required|exists:languages,id',
            'description' => 'nullable|string'
        ]);

        $snippet->update($validated);

        return redirect()->route('snippets.index')
            ->with('success', 'Snippet actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Snippet $snippet)
    {
        // Verificar que el snippet pertenezca al usuario
        $this->authorizeSnippet($snippet);

        $snippet->delete();

        return redirect()->route('snippets.index')
            ->with('success', 'Snippet eliminado exitosamente.');
    }

    /**
     * Autorizar el acceso al snippet
     */
    private function authorizeSnippet(Snippet $snippet): void
    {
        if ($snippet->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para acceder a este snippet.');
        }
    }
}