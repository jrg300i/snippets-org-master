@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-code me-2 text-primary"></i>Mis Snippets
            </h4>
            <small class="text-muted">
                {{ $snippets->count() }} total · {{ $snippets->where('language_id')->count() }} con lenguaje ·
                {{ $snippets->whereNull('language_id')->count() }} sin lenguaje
            </small>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm me-1">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
        <a href="{{ route('snippets.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Nuevo Snippet
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($snippets->count() > 0)
        <div class="row g-3 mb-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                    <input type="text" id="snippetSearch" class="form-control" placeholder="Buscar por título, código o etiqueta...">
                </div>
            </div>
            <div class="col-md-3">
                <select id="categoryFilter" class="form-select form-select-sm">
                    <option value="">Todas las colecciones</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="languageFilter" class="form-select form-select-sm">
                    <option value="">Todos los lenguajes</option>
                    @foreach ($languages as $language)
                        <option value="{{ $language->name }}">{{ $language->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 text-end">
                <a href="{{ route('snippets.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
        </div>

        <div class="row" id="snippetsGrid">
            @foreach ($snippets as $snippet)
                @php
                    $languageColor = $snippet->language->color ?? '#94a3b8';
                @endphp
                <div class="col-md-6 col-lg-4 mb-4 d-flex align-items-stretch snippet-card-wrap"
                     data-search="{{ strtolower($snippet->title . ' ' . $snippet->description . ' ' . implode(' ', $snippet->tags ?? []) . ' ' . $snippet->code) }}"
                     data-category="{{ $snippet->category->name ?? '' }}"
                     data-language="{{ $snippet->language->name ?? '' }}">
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
                                <button type="button" class="btn btn-sm btn-dark text-white btn-copy position-absolute top-0 end-0 m-1"
                                        data-code="{{ htmlspecialchars($snippet->code) }}" title="Copiar código">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <pre class="m-0 overflow-auto" style="max-height: 130px; font-size: 0.75rem; line-height: 1.35;">
<code class="language-{{ $snippet->language->slug ?? 'plaintext' }}">{{ $snippet->getFirstLines(8) }}</code>
                                </pre>
                            </div>

                            @if ($snippet->tags)
                                <div class="mb-2">
                                    @foreach ($snippet->tags as $tag)
                                        <span class="chip chip-tag">#{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($snippet->thiscodeworks_url)
                                <div class="mb-2 small">
                                    <a href="{{ $snippet->thiscodeworks_url }}" target="_blank" rel="noopener" class="text-decoration-none text-success">
                                        <i class="fab fa-connectdevelop me-1"></i>Publicado en thiscodeworks
                                    </a>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $snippet->updated_at->diffForHumans() }}
                                </small>
                                <div class="btn-group btn-group-sm">
                                    @if ($thiscodeworksEnabled && !$snippet->thiscodeworks_id)
                                        <form action="{{ route('snippets.publish', $snippet->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Publicar en thiscodeworks">
                                                <i class="fab fa-connectdevelop"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('snippets.show', $snippet->id) }}" class="btn btn-outline-info" title="Ver snippet">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('snippets.edit', $snippet->id) }}" class="btn btn-outline-warning" title="Editar snippet">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('snippets.destroy', $snippet->id) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar snippet"
                                                data-confirm="¿Seguro que deseas eliminar el snippet «{{ $snippet->title }}»?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="noResults" class="text-center py-5 d-none">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">No hay snippets que coincidan con tu búsqueda.</p>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-code fa-4x text-muted mb-4"></i>
            <h5 class="text-muted">No hay snippets creados</h5>
            <p class="text-muted mb-4">Comienza organizando tu código creando tu primer snippet.</p>
            <a href="{{ route('snippets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Crear Primer Snippet
            </a>
        </div>
    @endif
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
            setTimeout(() => { icon.className = 'fas fa-copy'; }, 1500);
        });
    });

    const applyFilters = function () {
        const q = document.getElementById('snippetSearch').value.toLowerCase();
        const category = document.getElementById('categoryFilter').value;
        const language = document.getElementById('languageFilter').value;
        let visible = 0;

        document.querySelectorAll('.snippet-card-wrap').forEach(function (card) {
            const search = card.getAttribute('data-search') || '';
            const cat = card.getAttribute('data-category');
            const lang = card.getAttribute('data-language');
            const ok = (!q || search.includes(q)) &&
                       (!category || cat === category) &&
                       (!language || lang === language);
            card.style.display = ok ? '' : 'none';
            if (ok) visible++;
        });

        document.getElementById('noResults').classList.toggle('d-none', visible > 0);
    };

    const searchEl = document.getElementById('snippetSearch');
    const categoryEl = document.getElementById('categoryFilter');
    const languageEl = document.getElementById('languageFilter');
    if (searchEl) searchEl.addEventListener('input', applyFilters);
    if (categoryEl) categoryEl.addEventListener('change', applyFilters);
    if (languageEl) languageEl.addEventListener('change', applyFilters);

    // Eliminación con confirmación vía SweetAlert2
    document.querySelectorAll('.form-delete').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const message = form.querySelector('button[type=submit]').getAttribute('data-confirm');
            Swal.fire({
                title: '¿Eliminar snippet?',
                text: message || 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush