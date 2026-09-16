<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Models\Language;
use App\Services\AuditoriaService;

class LanguageController extends Controller
{
    public function __construct(private readonly AuditoriaService $auditoria)
    {
    }

    public function index()
    {
        $languages = Language::withCount('snippets')->orderBy('name')->get();

        return view('languages.index', compact('languages'));
    }

    public function create()
    {
        return view('languages.create');
    }

    public function store(StoreLanguageRequest $request)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $language = Language::create($validated);

        $this->auditoria->registrar(
            'crear',
            'lenguaje',
            $language->id,
            "Lenguaje «{$language->name}» creado.",
            null,
            $language->toArray(),
        );

        return redirect()->route('languages.index')
            ->with('success', 'Lenguaje creado exitosamente.');
    }

    public function show(Language $language)
    {
        $language->load('snippets.category');

        return view('languages.show', compact('language'));
    }

    public function edit(Language $language)
    {
        return view('languages.edit', compact('language'));
    }

    public function update(UpdateLanguageRequest $request, Language $language)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active');

        $antes = $language->toArray();

        $language->update($validated);

        $this->auditoria->registrar(
            'actualizar',
            'lenguaje',
            $language->id,
            "Lenguaje «{$language->name}» actualizado.",
            $antes,
            $language->toArray(),
        );

        return redirect()->route('languages.index')
            ->with('success', 'Lenguaje actualizado exitosamente.');
    }

    public function destroy(Language $language)
    {
        if ($language->snippets()->count() > 0) {
            return redirect()->route('languages.index')
                ->with('error', 'No se puede eliminar el lenguaje porque tiene snippets asociados.');
        }

        $name = $language->name;
        $antes = $language->toArray();

        $language->delete();

        $this->auditoria->registrar(
            'eliminar',
            'lenguaje',
            $language->id,
            "Lenguaje «{$name}» eliminado.",
            $antes,
            null,
        );

        return redirect()->route('languages.index')
            ->with('success', 'Lenguaje eliminado exitosamente.');
    }
}