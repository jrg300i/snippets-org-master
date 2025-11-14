@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Lenguaje
                        </h4>
                        <a href="{{ route('languages.index', $language->id) }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('languages.update', $language->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-warning"></i>Nombre del Lenguaje
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $language->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="slug" class="form-label fw-semibold">
                                <i class="fas fa-code me-1 text-warning"></i>Slug
                            </label>
                            <input type="text" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug', $language->slug) }}" 
                                   required>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="color" class="form-label fw-semibold">
                                <i class="fas fa-palette me-1 text-warning"></i>Color
                            </label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" 
                                       name="color" 
                                       value="{{ old('color', $language->color) }}"
                                       required>
                                <input type="text" 
                                       class="form-control @error('color') is-invalid @enderror" 
                                       value="{{ old('color', $language->color) }}"
                                       onchange="document.getElementById('color').value = this.value">
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-warning"></i>Descripción
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $language->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- CORRECCIÓN: Campo is_active funcionando correctamente -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       role="switch"
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $language->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    <i class="fas fa-toggle-on me-1"></i>Lenguaje activo
                                </label>
                            </div>
                            <div class="form-text">
                                Los lenguajes inactivos no aparecerán en los formularios de creación/edición de snippets.
                                @if(!$language->is_active)
                                    <span class="text-warning fw-semibold">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Actualmente INACTIVO
                                    </span>
                                @else
                                    <span class="text-success fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Actualmente ACTIVO
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-warning">
                                    <i class="fas fa-info-circle me-2"></i>Información del Lenguaje
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Snippets asociados:</strong></p>
                                        <p class="mb-0">
                                            <span class="badge bg-primary">{{ $language->snippets_count ?? $language->snippets->count() }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Estado actual:</strong></p>
                                        <p class="mb-0">
                                            <span class="badge bg-{{ $language->is_active ? 'success' : 'secondary' }}">
                                                {{ $language->is_active ? 'ACTIVO' : 'INACTIVO' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('languages.show', $language->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Actualizar Lenguaje
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Script para mejorar la experiencia del usuario
    document.addEventListener('DOMContentLoaded', function() {
        const isActiveSwitch = document.getElementById('is_active');
        const statusBadge = document.querySelector('.form-text .fw-semibold');
        
        isActiveSwitch.addEventListener('change', function() {
            if (this.checked) {
                statusBadge.className = 'text-success fw-semibold';
                statusBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i>Se activará al guardar';
            } else {
                statusBadge.className = 'text-warning fw-semibold';
                statusBadge.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Se desactivará al guardar';
            }
        });
    });
</script>
@endpush
@endsection
