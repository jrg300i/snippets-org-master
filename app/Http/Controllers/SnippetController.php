<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSnippetRequest;
use App\Http\Requests\UpdateSnippetRequest;
use App\Models\Category;
use App\Models\Language;
use App\Models\Snippet;
use App\Services\AuditoriaService;
use App\Services\ThisCodeWorksService;

class SnippetController extends Controller
{
    public function __construct(
        private readonly AuditoriaService $auditoria,
        private readonly ThisCodeWorksService $thiscodeworks,
    ) {
    }

    public function index()
    {
        $snippets = Snippet::with(['category', 'language', 'user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $categories = Category::orderBy('name')->get();
        $languages = Language::active()->orderBy('name')->get();
        $thiscodeworksEnabled = $this->thiscodeworks->enabled();

        return view('snippets.index', compact('snippets', 'categories', 'languages', 'thiscodeworksEnabled'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $languages = Language::active()->orderBy('name')->get();

        return view('snippets.create', compact('categories', 'languages'));
    }

    public function store(StoreSnippetRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $publishToApi = $request->boolean('publish_to_api', true);
        unset($validated['publish_to_api']);

        $snippet = Snippet::create($validated);

        $this->auditoria->registrar(
            'crear',
            'snippet',
            $snippet->id,
            "Snippet «{$snippet->title}» creado.",
            null,
            $snippet->toArray(),
        );

        $message = 'Snippet creado exitosamente.';

        if ($this->thiscodeworks->enabled() && $publishToApi) {
            $published = $this->thiscodeworks->publish($snippet);

            if ($published) {
                $this->auditoria->registrar(
                    'publicar',
                    'snippet',
                    $snippet->id,
                    'Snippet publicado en thiscodeworks.com.',
                    null,
                    [
                        'thiscodeworks_id' => $snippet->thiscodeworks_id,
                        'thiscodeworks_url' => $snippet->thiscodeworks_url,
                    ],
                );
                $message .= ' Publicado en thiscodeworks.com.';
            } else {
                $message .= ' No se pudo publicar en thiscodeworks.com.';
            }
        }

        return redirect()->route('snippets.index')->with('success', $message);
    }

    public function show(Snippet $snippet)
    {
        $this->authorizeSnippet($snippet);

        $snippet->load(['category', 'language', 'user']);

        return view('snippets.show', [
            'snippet' => $snippet,
            'thiscodeworksEnabled' => $this->thiscodeworks->enabled(),
        ]);
    }

    public function edit(Snippet $snippet)
    {
        $this->authorizeSnippet($snippet);

        $categories = Category::orderBy('name')->get();
        $languages = Language::active()->orderBy('name')->get();

        return view('snippets.edit', compact('snippet', 'categories', 'languages'));
    }

    public function update(UpdateSnippetRequest $request, Snippet $snippet)
    {
        $this->authorizeSnippet($snippet);

        $antes = $snippet->toArray();

        $snippet->update($request->validated());

        $this->auditoria->registrar(
            'actualizar',
            'snippet',
            $snippet->id,
            "Snippet «{$snippet->title}» actualizado.",
            $antes,
            $snippet->toArray(),
        );

        return redirect()->route('snippets.index')
            ->with('success', 'Snippet actualizado exitosamente.');
    }

    public function destroy(Snippet $snippet)
    {
        $this->authorizeSnippet($snippet);

        $title = $snippet->title;
        $antes = $snippet->toArray();

        $snippet->delete();

        $this->auditoria->registrar(
            'eliminar',
            'snippet',
            $snippet->id,
            "Snippet «{$title}» eliminado.",
            $antes,
            null,
        );

        return redirect()->route('snippets.index')
            ->with('success', 'Snippet eliminado exitosamente.');
    }

    public function publish(Snippet $snippet)
    {
        $this->authorizeSnippet($snippet);

        if (!$this->thiscodeworks->enabled()) {
            return redirect()->back()
                ->with('error', 'La integración con thiscodeworks está deshabilitada.');
        }

        if ($snippet->thiscodeworks_id) {
            return redirect()->back()->with('info', 'El snippet ya está publicado en thiscodeworks.');
        }

        $published = $this->thiscodeworks->publish($snippet);

        if ($published) {
            $this->auditoria->registrar(
                'publicar',
                'snippet',
                $snippet->id,
                'Snippet publicado en thiscodeworks.com.',
                null,
                [
                    'thiscodeworks_id' => $snippet->thiscodeworks_id,
                    'thiscodeworks_url' => $snippet->thiscodeworks_url,
                ],
            );

            return redirect()->back()->with('success', 'Snippet publicado en thiscodeworks.com.');
        }

        return redirect()->back()
            ->with('error', 'No se pudo publicar el snippet en thiscodeworks.com.');
    }

    private function authorizeSnippet(Snippet $snippet): void
    {
        if ($snippet->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para acceder a este snippet.');
        }
    }
}