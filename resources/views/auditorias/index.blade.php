@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-clipboard-list me-2 text-primary"></i>Auditoría de acciones
        </h4>
        <span class="badge bg-secondary">{{ $auditorias->total() }} registros</span>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('auditorias.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small mb-1">Acción</label>
                    <select name="accion" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach (['crear', 'actualizar', 'eliminar', 'login', 'logout', 'registro', 'backup', 'publicar'] as $opcion)
                            <option value="{{ $opcion }}" @selected(request('accion') === $opcion)>{{ ucfirst($opcion) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Entidad</label>
                    <select name="entidad" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach (['snippet', 'categoria', 'lenguaje', 'perfil', 'usuario'] as $opcion)
                            <option value="{{ $opcion }}" @selected(request('entidad') === $opcion)>{{ ucfirst($opcion) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Usuario</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" @selected((string) request('user_id') === (string) $usuario->id)>{{ $usuario->name }} ({{ $usuario->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">IP</label>
                    <input type="text" name="ip" value="{{ request('ip') }}" class="form-control form-control-sm" placeholder="127.0.0.1">
                </div>
                <div class="col-md-1">
                    <label class="form-label small mb-1">Desde</label>
                    <input type="date" name="desde" value="{{ request('desde') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-1">
                    <label class="form-label small mb-1">Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Entidad</th>
                        <th>Descripción</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditorias as $auditoria)
                        <tr>
                            <td class="text-nowrap small">{{ $auditoria->created_at->format('d/m/Y H:i') }}</td>
                            <td class="small">
                                {{ $auditoria->user ? $auditoria->user->name : 'Sistema/Invitado' }}
                                @if ($auditoria->user?->isAdmin())
                                    <span class="badge bg-dark ms-1">admin</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badge = [
                                        'crear' => 'success',
                                        'actualizar' => 'info',
                                        'eliminar' => 'danger',
                                        'login' => 'secondary',
                                        'logout' => 'secondary',
                                        'registro' => 'warning',
                                        'backup' => 'primary',
                                        'publicar' => 'dark',
                                    ][$auditoria->accion] ?? 'light';
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ $auditoria->accion }}</span>
                            </td>
                            <td class="small">{{ $auditoria->entidad }}
                                @if ($auditoria->entidad_id)
                                    <span class="text-muted">#{{ $auditoria->entidad_id }}</span>
                                @endif
                            </td>
                            <td class="small">{{ $auditoria->descripcion }}</td>
                            <td class="small text-muted">{{ $auditoria->ip }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Sin registros de auditoría para los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $auditorias->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection