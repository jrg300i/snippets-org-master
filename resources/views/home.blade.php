@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="row">
                            <!-- Tarjeta de Snippets -->
                            <div class="col-md-12 mb-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">Snippets</h5>
                                                <h2 class="mb-0">{{ $snippetsCount ?? 0 }}</h2>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-code fa-2x"></i>
                                            </div>
                                        </div>
                                        <a href="{{ route('snippets.index') }}" class="btn btn-light btn-sm mt-3">
                                            Gestionar Snippets
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección adicional para Lenguajes y Categorías -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-language me-2"></i>Lenguajes Disponibles
                                            <span class="badge bg-light text-dark float-end">{{ $languagesCount ?? 0 }}</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            try {
                                                $languages = App\Models\Language::withCount('snippets')->latest()->take(6)->get();
                                            } catch (Exception $e) {
                                                $languages = collect();
                                            }
                                        @endphp

                                        @if($languages->count() > 0)
                                            <div class="row">
                                                @foreach($languages as $language)
                                                    <div class="col-6 mb-2">
                                                        <a href="{{ route('languages.show', $language->id) }}"
                                                            class="text-decoration-none">
                                                            <div class="d-flex align-items-center p-2 rounded"
                                                                style="background-color: {{ $language->color }}20; border-left: 3px solid {{ $language->color }};">
                                                                <span class="badge me-2"
                                                                    style="background-color: {{ $language->color }};">
                                                                    {{ $language->snippets_count }}
                                                                </span>
                                                                <small class="fw-semibold">{{ $language->name }}</small>
                                                                @if(!$language->is_active)
                                                                    <small class="ms-1 text-muted" title="Lenguaje inactivo">
                                                                        <i class="fas fa-eye-slash"></i>
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="text-center mt-3">
                                                <a href="{{ route('languages.index') }}"
                                                    class="btn btn-sm btn-info text-white me-2">
                                                    <i class="fas fa-list me-1"></i>Ver Todos
                                                </a>
                                                <a href="{{ route('languages.create') }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-plus me-1"></i>Nuevo Lenguaje
                                                </a>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="fas fa-language fa-2x text-muted mb-3"></i>
                                                <p class="text-muted">No hay lenguajes creados</p>
                                                <a href="{{ route('languages.create') }}" class="btn btn-info text-white">
                                                    <i class="fas fa-plus me-1"></i>Crear Primer Lenguaje
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-folder me-2"></i>Categorías Disponibles
                                            <span class="badge bg-light text-dark float-end">{{ $categoriesCount ?? 0 }}</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            try {
                                                $categories = App\Models\Category::withCount('snippets')->latest()->take(6)->get();
                                            } catch (Exception $e) {
                                                $categories = collect();
                                            }
                                        @endphp

                                        @if($categories->count() > 0)
                                            <div class="list-group list-group-flush">
                                                @foreach($categories as $category)
                                                    <a href="{{ route('categories.show', $category->id) }}"
                                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                        <span class="fw-semibold">{{ $category->name }}</span>
                                                        <span class="badge bg-primary rounded-pill">{{ $category->snippets_count }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                            <div class="text-center mt-3">
                                                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-success me-2">
                                                    <i class="fas fa-list me-1"></i>Ver Todas
                                                </a>
                                                <a href="{{ route('categories.create') }}"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-plus me-1"></i>Nueva Categoría
                                                </a>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="fas fa-folder fa-2x text-muted mb-3"></i>
                                                <p class="text-muted">No hay categorías creadas</p>
                                                <a href="{{ route('categories.create') }}" class="btn btn-success">
                                                    <i class="fas fa-plus me-1"></i>Crear Primera Categoría
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Acciones Rápidas</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('snippets.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i>Nuevo Snippet
                                            </a>
                                            <a href="{{ route('stats.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-chart-bar me-1"></i>Ver Estadísticas
                                            </a>
                                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-user me-1"></i>Mi Perfil
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Snippets Recientes</h5>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            try {
                                                $recentSnippets = App\Models\Snippet::with(['category', 'language'])->latest()->take(5)->get();
                                            } catch (Exception $e) {
                                                $recentSnippets = collect();
                                            }
                                        @endphp

                                        @if($recentSnippets->count() > 0)
                                            <div class="list-group list-group-flush">
                                                @foreach($recentSnippets as $snippet)
                                                    <a href="{{ route('snippets.show', $snippet->id) }}"
                                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>{{ Str::limit($snippet->title, 25) }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-folder"></i>
                                                                {{ $snippet->category->name ?? 'Sin categoría' }}
                                                                • <i class="fas fa-code"></i>
                                                                {{ $snippet->language->name ?? 'Sin lenguaje' }}
                                                            </small>
                                                        </div>
                                                        <span class="badge bg-primary rounded-pill">
                                                            {{ $snippet->created_at->diffForHumans() }}
                                                        </span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted text-center">No hay snippets recientes</p>
                                            <div class="text-center">
                                                <a href="{{ route('snippets.create') }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-plus"></i> Crear Primer Snippet
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection