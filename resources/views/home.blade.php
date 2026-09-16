@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Estadísticas rápidas --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('snippets.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div style="height: 4px; background: var(--c-primary);"></div>
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Snippets</h6>
                            <h2 class="mb-0 fw-bold">{{ $snippetsCount }}</h2>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 52px; height: 52px; background: var(--c-primary-light); color: var(--c-primary);">
                            <i class="fas fa-code fa-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('categories.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div style="height: 4px; background: var(--c-success);"></div>
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Colecciones</h6>
                            <h2 class="mb-0 fw-bold">{{ $categoriesCount }}</h2>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 52px; height: 52px; background: #D1FAE5; color: var(--c-success);">
                            <i class="fas fa-layer-group fa-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('languages.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div style="height: 4px; background: var(--c-accent);"></div>
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Lenguajes</h6>
                            <h2 class="mb-0 fw-bold">{{ $languagesCount }}</h2>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 52px; height: 52px; background: #E0F2FE; color: #0284C7;">
                            <i class="fas fa-language fa-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Snippets recientes --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Snippets recientes</h5>
        <a href="{{ route('snippets.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Nuevo Snippet
        </a>
    </div>

    @if ($recentSnippets->count() > 0)
        <div class="row g-3 mb-4">
            @foreach ($recentSnippets as $snippet)
                @php $languageColor = $snippet->language->color ?? '#94a3b8'; @endphp
                <div class="col-md-6 col-lg-4 d-flex align-items-stretch">
                    <div class="card w-100 border-0 shadow-sm overflow-hidden">
                        <div style="height: 4px; background: {{ $languageColor }};"></div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0 fw-bold text-truncate" title="{{ $snippet->title }}">
                                    {{ $snippet->title }}
                                </h6>
                                @if ($snippet->language)
                                    <span class="badge ms-2" style="background: {{ $languageColor }}22; color: {{ $languageColor }};">
                                        {{ $snippet->language->name }}
                                    </span>
                                @endif
                            </div>
                            <div class="mb-2">
                                <span class="chip"><i class="fas fa-layer-group me-1"></i>{{ $snippet->category->name ?? 'Sin categoría' }}</span>
                            </div>
                            <div class="position-relative bg-dark rounded mb-2 p-2">
                                <button type="button"
                                        class="btn btn-sm btn-dark text-white btn-copy position-absolute top-0 end-0 m-1"
                                        data-code="{{ htmlspecialchars($snippet->code) }}" title="Copiar código">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <pre class="m-0 overflow-auto" style="max-height: 110px; font-size: 0.75rem; line-height: 1.35;">
<code class="language-{{ $snippet->language->slug ?? 'plaintext' }}">{{ $snippet->getFirstLines(6) }}</code>
                                </pre>
                            </div>
                            @if ($snippet->tags)
                                <div class="mb-2">
                                    @foreach ($snippet->tags as $tag)
                                        <span class="chip chip-tag">#{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $snippet->updated_at->diffForHumans() }}</small>
                                <a href="{{ route('snippets.show', $snippet->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Ver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5 bg-white rounded shadow-sm mb-4">
            <i class="fas fa-code fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">Aún no hay snippets. Crea el primero.</p>
        </div>
    @endif

    {{-- Explora colecciones y lenguajes --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="fas fa-layer-group me-2 text-success"></i>Colecciones
                        <span class="badge text-bg-light float-end">{{ $categoriesCount }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if ($categories->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($categories as $category)
                                <a href="{{ route('categories.show', $category->id) }}"
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-semibold">{{ $category->name }}</span>
                                    <span class="badge rounded-pill" style="background: var(--c-primary-light); color: var(--c-primary-dark);">
                                        {{ $category->snippets_count }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No hay colecciones.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="fas fa-language me-2 text-info"></i>Lenguajes
                        <span class="badge text-bg-light float-end">{{ $languagesCount }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if ($languages->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($languages as $language)
                                <a href="{{ route('languages.show', $language->id) }}"
                                   class="text-decoration-none chip"
                                   style="background: {{ $language->color }}1a; color: {{ $language->color }}; border: 1px solid {{ $language->color }}44;">
                                    {{ $language->name }}
                                    <span class="badge ms-1" style="background: {{ $language->color }}; color: #fff;">
                                        {{ $language->snippets_count }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No hay lenguajes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-copy').forEach(function (button) {
        button.addEventListener('click', async function () {
            const code = this.getAttribute('data-code');
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(code);
                } else {
                    throw new Error('clipboard no disponible');
                }
            } catch (e) {
                const ta = document.createElement('textarea');
                ta.value = code;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
            }
            const icon = this.querySelector('i');
            icon.className = 'fas fa-check';
            const that = this;
            setTimeout(() => { that.querySelector('i').className = 'fas fa-copy'; }, 1500);
        });
    });
});
</script>
@endpush