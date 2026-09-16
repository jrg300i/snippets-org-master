<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\AuditoriaService;
use App\Services\ThisCodeWorksService;

class CategoryController extends Controller
{
    public function __construct(
        private readonly AuditoriaService $auditoria,
        private readonly ThisCodeWorksService $thiscodeworks,
    ) {
    }

    public function index()
    {
        $categories = Category::withCount(['snippets', 'publishedSnippets'])
            ->orderBy('name')
            ->get();

        return view('categories.index', [
            'categories' => $categories,
            'thiscodeworksEnabled' => $this->thiscodeworks->enabled(),
        ]);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        $this->auditoria->registrar(
            'crear',
            'categoria',
            $category->id,
            "Categoría «{$category->name}» creada.",
            null,
            $category->toArray(),
        );

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function show(Category $category)
    {
        return view('categories.show', [
            'category' => $category->loadCount(['snippets', 'publishedSnippets']),
            'thiscodeworksEnabled' => $this->thiscodeworks->enabled(),
        ]);
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $antes = $category->toArray();

        $category->update($request->validated());

        $this->auditoria->registrar(
            'actualizar',
            'categoria',
            $category->id,
            "Categoría «{$category->name}» actualizada.",
            $antes,
            $category->toArray(),
        );

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Publicar toda la colección: sube a thiscodeworks.com los snippets que
     * aún no estén publicados, reutilizando los ya publicados.
     *
     * La API no soporta "colecciones" con página pública, por lo que la
     * publicación se hace snippet a snippet (sin crear colecciones fantasma).
     */
    public function publish(Category $category)
    {
        if (!$this->thiscodeworks->enabled()) {
            return redirect()->back()
                ->with('error', 'La integración con thiscodeworks está deshabilitada.');
        }

        $total = $category->snippets()->count();
        $pendientes = $category->snippets()->whereNull('thiscodeworks_id')->get();
        $yaPublicados = $total - $pendientes->count();

        if ($total === 0) {
            return redirect()->back()
                ->with('info', 'Esta colección no tiene snippets para publicar.');
        }

        if ($pendientes->isEmpty()) {
            return redirect()->back()
                ->with('info', 'Todos los snippets de esta colección ya están publicados en thiscodeworks.com.');
        }

        $publicados = 0;
        $fallidos = 0;

        foreach ($pendientes as $snippet) {
            if ($this->thiscodeworks->publish($snippet)) {
                $publicados++;
            } else {
                $fallidos++;
            }
        }

        $resumen = "Colección «{$category->name}»: {$publicados} publicados, {$yaPublicados} ya existían";
        $resumen .= $fallidos > 0 ? ", {$fallidos} fallidos." : '.';

        $this->auditoria->registrar(
            'publicar',
            'categoria',
            $category->id,
            "Publicación de la colección «{$category->name}» ({$publicados} de {$pendientes->count()} pendientes).",
            null,
            ['publicados' => $publicados, 'ya_publicados' => $yaPublicados, 'fallidos' => $fallidos],
        );

        if ($publicados === 0) {
            return redirect()->back()
                ->with('error', 'No se pudo publicar ningún snippet de la colección. Inténtalo de nuevo más tarde.');
        }

        $mensaje = "Colección publicada: {$publicados} snippet(s) nuevos";
        $mensaje .= $yaPublicados > 0 ? " ({$yaPublicados} ya estaban publicados)" : '';
        $mensaje .= $fallidos > 0 ? ". {$fallidos} no se pudieron publicar." : '.';

        return redirect()->back()->with('success', $mensaje);
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $antes = $category->toArray();

        $category->delete();

        $this->auditoria->registrar(
            'eliminar',
            'categoria',
            $category->id,
            "Categoría «{$name}» eliminada.",
            $antes,
            null,
        );

        return redirect()->route('categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}