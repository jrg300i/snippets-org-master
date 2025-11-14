@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">

                            <i class="fas fa-language me-2"></i>Crear Nuevo Lenguaje

                        <a href="{{ route('home') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('languages.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-info"></i>Nombre del Lenguaje
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Ej: PHP, JavaScript, Python"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Nombre completo del lenguaje de programación.</div>
                        </div>

                        <div class="mb-4">
                            <label for="slug" class="form-label fw-semibold">
                                <i class="fas fa-code me-1 text-info"></i>Slug
                            </label>
                            <input type="text" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug') }}" 
                                   placeholder="Ej: php, javascript, python"
                                   required>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Identificador único en minúsculas y sin espacios.</div>
                        </div>

                        <div class="mb-4">
                            <label for="color" class="form-label fw-semibold">
                                <i class="fas fa-palette me-1 text-info"></i>Color
                            </label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" 
                                       name="color" 
                                       value="{{ old('color', '#6c757d') }}"
                                       required>
                                <input type="text" 
                                       class="form-control @error('color') is-invalid @enderror" 
                                       value="{{ old('color', '#6c757d') }}"
                                       onchange="document.getElementById('color').value = this.value">
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Color para identificar el lenguaje en la interfaz.</div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-info"></i>Descripción (Opcional)
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Breve descripción del lenguaje...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('languages.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-info text-white">
                                <i class="fas fa-save me-1"></i>Crear Lenguaje
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
    // Generar slug automáticamente desde el nombre
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const slug = name.toLowerCase()
            .replace(/[^\w\s]/gi, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        
        document.getElementById('slug').value = slug;
    });
</script>
@endpush
@endsection
