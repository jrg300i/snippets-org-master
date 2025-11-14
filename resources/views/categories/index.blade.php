{{-- resources/views/categories/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-folder me-2"></i>Gestión de Categorías
                        </h4>
                        <a href="{{ route('categories.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus me-1"></i>Nueva Categoría
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                     </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($categories->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay categorías registradas</h5>
                            <p class="text-muted">Comienza creando tu primera categoría</p>
                            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Crear Primera Categoría
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Propósito</th>
                                        <th>Fecha Creación</th>
                                        <th width="120">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>
                                                <i class="fas fa-folder text-warning me-2"></i>
                                                <strong>{{ $category->name }}</strong>
                                            </td>
                                            <td>
                                                @if($category->description)
                                                    {{ Str::limit($category->description, 50) }}
                                                @else
                                                    <span class="text-muted">Sin descripción</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($category->proposito)
                                                    {{ Str::limit($category->proposito, 50) }}
                                                @else
                                                    <span class="text-muted">Sin propósito</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $category->created_at->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('categories.show', $category) }}" 
                                                       class="btn btn-outline-info" 
                                                       title="Ver">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('categories.edit', $category) }}" 
                                                       class="btn btn-outline-primary" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $category->id }}"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Modal de Confirmación -->
                                                <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>¿Estás seguro de que deseas eliminar la categoría <strong>"{{ $category->name }}"</strong>?</p>
                                                                <p class="text-danger small">Esta acción no se puede deshacer.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <form action="{{ route('categories.destroy', $category) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
