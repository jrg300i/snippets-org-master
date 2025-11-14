@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-folder-plus me-2"></i>Crear Nueva Categoría
                        </h4>
                        <a href="{{ route('categories.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf

                        <!-- Nombre -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-success"></i>Nombre de la Categoría
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Ej: PHP Functions, SQL Queries, React Components"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Un nombre único y descriptivo para organizar tus snippets.</div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-success"></i>Descripción (Opcional)
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Describe el propósito de esta categoría...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Una breve descripción sobre el tipo de snippets que contendrá esta categoría.</div>
                        </div>

                        <!-- Propósito -->
                        <div class="mb-4">
                            <label for="proposito" class="form-label fw-semibold">
                                <i class="fas fa-bullseye me-1 text-success"></i>Propósito (Opcional)
                            </label>
                            <textarea class="form-control @error('proposito') is-invalid @enderror" 
                                      id="proposito" 
                                      name="proposito" 
                                      rows="3" 
                                      placeholder="¿Cuál es el objetivo principal de esta categoría?">{{ old('proposito') }}</textarea>
                            @error('proposito')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Explica el propósito o la finalidad específica de esta categoría.</div>
                        </div>

                        <!-- Ejemplos -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-lightbulb me-1 text-warning"></i>Ejemplos de Categorías
                            </label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <h6 class="card-title">Por lenguaje:</h6>
                                            <ul class="list-unstyled small mb-0">
                                                <li><i class="fas fa-check text-success me-1"></i>PHP Functions</li>
                                                <li><i class="fas fa-check text-success me-1"></i>JavaScript Utilities</li>
                                                <li><i class="fas fa-check text-success me-1"></i>Python Scripts</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <h6 class="card-title">Por propósito:</h6>
                                            <ul class="list-unstyled small mb-0">
                                                <li><i class="fas fa-check text-success me-1"></i>Database Queries</li>
                                                <li><i class="fas fa-check text-success me-1"></i>API Endpoints</li>
                                                <li><i class="fas fa-check text-success me-1"></i>Authentication</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i>Crear Categoría
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
