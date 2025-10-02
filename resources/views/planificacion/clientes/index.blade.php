@extends('layouts/contentNavbarLayout')

@section('title', 'Clientes - Listado')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Clientes</h4>
        <a href="{{ route('planificacion.clientes.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Nuevo
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-sm-4 col-md-3">
                    <label class="form-label small">Buscar</label>
                    <input name="search" class="form-control form-control-sm" style="max-width: 220px"
                        placeholder="Razón / Doc." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Ciudad</label>
                    <input name="ciudad" class="form-control form-control-sm" value="{{ $filters['ciudad'] ?? '' }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach (['activo' => 'Activo', 'inactivo' => 'Inactivo'] as $k => $v)
                            <option value="{{ $k }}" @selected(($filters['estado'] ?? '') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Frecuencia</label>
                    <select name="frecuencia" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach (['diaria', 'semanal', 'quincenal', 'mensual'] as $f)
                            <option value="{{ $f }}" @selected(($filters['frecuencia'] ?? '') === $f)>{{ ucfirst($f) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-1">
                    <label class="form-label small">Límite</label>
                    <select name="limit" class="form-select form-select-sm">
                        @foreach ([10, 15, 25, 50] as $n)
                            <option @selected(request('limit', 15) == $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button class="btn btn-sm btn-primary w-100"><i class="fa fa-search me-1"></i> Buscar</button>
                    <a href="{{ route('planificacion.clientes.index') }}" class="btn btn-sm btn-secondary w-100">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'razon_social',
                                    'sort_dir' => request('sort_by') === 'razon_social' && request('sort_dir') === 'asc' ? 'desc' : 'asc',
                                ]) }}">Cliente</a>
                        </th>
                        <th>Documento</th>
                        <th>Ciudad</th>
                        <th>Frecuencia</th>
                        <th>Prioridad</th>
                        <th>Comercial</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $c)
                        <tr>
                            <td>{{ $c->razon_social }}</td>
                            <td>{{ $c->documento }}</td>
                            <td>{{ $c->ciudad }}</td>
                            <td>{{ ucfirst($c->frecuencia_visita) }}</td>
                            <td>{{ $c->prioridad }}</td>
                            <td>{{ $c->comercial->name ?? '-' }}</td>
                            <td class="text-end">
                                <a href="{{ route('planificacion.clientes.show', $c) }}"
                                    class="btn btn-sm btn-outline-secondary">Ver</a>
                                <a href="{{ route('planificacion.clientes.edit', $c) }}"
                                    class="btn btn-sm btn-outline-primary">Editar</a>
                                <form action="{{ route('planificacion.clientes.destroy', $c) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('¿Eliminar cliente?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Sin resultados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $clientes->links() }}
        </div>
    </div>
@endsection
