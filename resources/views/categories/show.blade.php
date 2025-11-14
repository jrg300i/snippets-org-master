{{-- resources/views/categories/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-folder me-2"></i>Detalles de la Categoría
                        </h4>
                        <a href="{{ route('categories.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-muted">Nombre</label>
                                <p class="fs-5">
                                    <i class="fas fa-folder text-warning me-2"></i>
                                    {{ $category->name }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-muted">Descripción</label>
                                <p class="fs-6">
                                    @if($category->description)
                                        {{ $category->description }}
                                    @else
                                        <span class="text-muted">No hay descripción disponible</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-muted">Propósito</label>
                                <p class="fs-6">
                                    @if($category->proposito)
                                        {{ $category->proposito }}
                                    @else
                                        <span class="text-muted">No hay propósito definido</span>
                                    @endif
                                </p>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted">Fecha de Creación</label>
                                    <p class="mb-0">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted">Última Actualización</label>
                                    <p class="mb-0">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Volver al Listado
                                </a>
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning text-dark">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
