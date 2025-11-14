@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header text-white py-3" style="background-color: {{ $language->color }}; border-left: 4px solid {{ $language->color }}20;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-language me-2"></i>{{ $language->name }}
                        </h4>
                        <a href="{{ route('languages.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Slug:</strong> 
                                <code>{{ $language->slug }}</code>
                            </p>
                            <p class="mb-2">
                                <strong>Color:</strong>
                                <span class="badge" style="background-color: {{ $language->color }};">
                                    {{ $language->color }}
                                </span>
                            </p>
                            <p class="mb-0">
                                <strong>Estado:</strong>
                                <span class="badge bg-{{ $language->is_active ? 'success' : 'secondary' }}">
                                    {{ $language->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Creado:</strong>
                                {{ $language->created_at->format('d/m/Y H:i') }}
                            </p>
                            <p class="mb-0">
                                <strong>Actualizado:</strong>
                                {{ $language->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    @if($language->description)
                        <div class="mb-4">
                            <h5 class="text-info">Descripción</h5>
                            <p class="lead">{{ $language->description }}</p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5 class="text-info">
                            <i class="fas fa-code me-2"></i>Snippets en este lenguaje
                            <span class="badge bg-info">{{ $language->snippets->count() }}</span>
                        </h5>

                        @if($language->snippets->count() > 0)
                            <div class="row">
                                @foreach($language->snippets as $snippet)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $snippet->title }}</h6>
                                                <p class="card-text">
                                                    <span class="badge bg-secondary">{{ $snippet->category->name }}</span>
                                                    <small class="text-muted ms-2">
                                                        {{ $snippet->created_at->format('d/m/Y') }}
                                                    </small>
                                                </p>
                                                <pre class="snippet-code p-2 small"><code>{{ Str::limit($snippet->code, 100) }}</code></pre>
                                                <div class="mt-2">
                                                    <a href="{{ route('snippets.show', $snippet->id) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                                    <a href="{{ route('snippets.edit', $snippet->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-code fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No hay snippets en este lenguaje</p>
                                <a href="{{ route('snippets.create') }}" class="btn btn-info text-white">
                                    <i class="fas fa-plus me-1"></i>Crear Primer Snippet
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('languages.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-1"></i>Ver Todos los Lenguajes
                        </a>
                        <div>
                            <a href="{{ route('languages.edit', $language->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <a href="{{ route('snippets.create') }}" class="btn btn-info text-white">
                                <i class="fas fa-plus me-1"></i>Crear Snippet
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
