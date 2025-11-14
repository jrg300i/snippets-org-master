@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-language me-2"></i>Gestionar Lenguajes de Programación
                        </h4>
                        <a href="{{ route('languages.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus me-1"></i>Nuevo Lenguaje
                        </a>
                         <a href="{{ route('home') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
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

                    @if($languages->count() > 0)
                        <div class="row">
                            @foreach ($languages as $language)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: {{ $language->color }}20; border-left: 4px solid {{ $language->color }};">
                                            <h6 class="mb-0 fw-bold">{{ $language->name }}</h6>
                                            <span class="badge" style="background-color: {{ $language->color }};">
                                                {{ $language->snippets_count ?? $language->snippets->count() }} snippets
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text text-muted small">
                                                <strong>Slug:</strong> <code>{{ $language->slug }}</code>
                                            </p>
                                            @if($language->description)
                                                <p class="card-text small">{{ $language->description }}</p>
                                            @else
                                                <p class="card-text small text-muted"><em>Sin descripción</em></p>
                                            @endif
                                            
                                            <div class="mt-3">
                                                <small class="text-muted d-block">Color:</small>
                                                <div class="d-flex align-items-center mt-1">
                                                    <div class="color-preview me-2" style="width: 20px; height: 20px; background-color: {{ $language->color }}; border-radius: 3px;"></div>
                                                    <small class="text-muted">{{ $language->color }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="btn-group w-100">
                                                <a href="{{ route('languages.show', $language->id) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Ver lenguaje">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('languages.edit', $language->id) }}" 
                                                   class="btn btn-sm btn-outline-warning"
                                                   title="Editar lenguaje">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('languages.destroy', $language->id) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este lenguaje?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Eliminar lenguaje"
                                                            {{ $language->snippets_count > 0 ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            @if($language->snippets_count > 0)
                                                <small class="text-muted d-block mt-2 text-center">
                                                    No se puede eliminar - tiene snippets asociados
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-language fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay lenguajes creados</h5>
                            <p class="text-muted">Comienza creando tu primer lenguaje de programación</p>
                            <a href="{{ route('languages.create') }}" class="btn btn-info text-white">
                                <i class="fas fa-plus me-1"></i>Crear Primer Lenguaje
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
