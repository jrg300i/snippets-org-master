<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::withCount('snippets')->get();
        return view('languages.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('languages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:languages',
            'slug' => 'required|string|max:50|unique:languages',
            'color' => 'required|string|max:7',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ]);

        // Procesar el color de forma segura
        $validated['color'] = $this->processColor($validated['color']);
        
        // Establecer is_active (por defecto true si no se envía)
        $validated['is_active'] = $request->has('is_active') ? true : true;

        Language::create($validated);

        return redirect()->route('languages.index')
            ->with('success', 'Lenguaje creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language)
    {
        $language->load('snippets.category');
        return view('languages.show', compact('language'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language)
    {
        return view('languages.edit', compact('language'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:languages,name,' . $language->id,
            'slug' => 'required|string|max:50|unique:languages,slug,' . $language->id,
            'color' => 'required|string|max:7',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ]);

        // Procesar el color de forma segura
        $validated['color'] = $this->processColor($validated['color']);
        
        // Manejar el campo is_active correctamente
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $language->update($validated);

        return redirect()->route('languages.index')
            ->with('success', 'Lenguaje actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        if ($language->snippets()->count() > 0) {
            return redirect()->route('languages.index')
                ->with('error', 'No se puede eliminar el lenguaje porque tiene snippets asociados.');
        }

        $language->delete();

        return redirect()->route('languages.index')
            ->with('success', 'Lenguaje eliminado exitosamente.');
    }

    /**
     * Procesar y validar color hexadecimal de forma segura
     */
    private function processColor($color)
    {
        // Asegurar que el color sea un string válido
        if (!is_string($color)) {
            return '#6c757d'; // Color por defecto
        }

        // Limpiar el color y asegurar formato hexadecimal
        $color = trim($color);
        
        // Si no empieza con #, agregarlo
        if (!Str::startsWith($color, '#')) {
            $color = '#' . $color;
        }

        // Validar formato hexadecimal (3 o 6 caracteres después del #)
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
            return Str::lower($color);
        }

        // Si el color no es válido, retornar color por defecto
        return '#6c757d';
    }
}