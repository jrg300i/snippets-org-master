@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Estadísticas del Sistema
                        </h4>
                        <div>
                            <a href="/api/stats" class="btn btn-light btn-sm me-2" target="_blank">
                                <i class="fas fa-code me-1"></i>Ver JSON
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(isset($error))
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    @endif

                    <!-- Resumen General -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Snippets</h5>
                                    <h2 class="mb-0">{{ $totalSnippets ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Categorías</h5>
                                    <h2 class="mb-0">{{ $totalCategories ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Lenguajes</h5>
                                    <h2 class="mb-0">{{ $totalLanguages ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Lenguajes Activos</h5>
                                    <h2 class="mb-0">
                                        @php
                                            try {
                                                echo \App\Models\Language::where('is_active', true)->count();
                                            } catch (Exception $e) {
                                                echo '0';
                                            }
                                        @endphp
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <!-- Snippets Recientes -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-code me-2"></i>Snippets Más Recientes
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(isset($recentSnippets) && $recentSnippets->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Lenguaje</th>
                                                <th>Categoría</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentSnippets as $snippet)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('snippets.show', $snippet->id) }}" class="text-decoration-none">
                                                            {{ Str::limit($snippet->title, 40) }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="badge" style="background-color: {{ $snippet->language->color ?? '#6c757d' }};">
                                                            {{ $snippet->language->name ?? 'Sin lenguaje' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $snippet->category->name ?? 'Sin categoría' }}</td>
                                                    <td>{{ $snippet->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center">No hay snippets recientes</p>
                            @endif
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Información de la API
                        </h6>
                        <p class="mb-0">
                            Estas estadísticas también están disponibles via API REST en 
                            <a href="/api/stats" class="alert-link" target="_blank">/api/stats</a> 
                            en formato JSON para integraciones.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
