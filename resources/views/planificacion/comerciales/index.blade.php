@extends('layouts/contentNavbarLayout')

@section('title', 'Comerciales - Listado')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Comerciales</h4>
        <a href="{{ route('planificacion.comerciales.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Nuevo
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-sm-4 col-md-3">
                    <label class="form-label small">Buscar</label>
                    <input name="search" class="form-control form-control-sm" style="max-width: 220px"
                        placeholder="Nombre / Email" value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Departamento</label>
                    <select name="departamento" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach (['ventas' => 'Ventas', 'marketing' => 'Marketing', 'atencion_cliente' => 'Atención al Cliente', 'desarrollo_negocio' => 'Desarrollo de Negocio'] as $k => $v)
                            <option value="{{ $k }}" @selected(($filters['departamento'] ?? '') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Estado</label>
                    <select name="activo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="1" @selected(($filters['activo'] ?? '') === '1')>Activo</option>
                        <option value="0" @selected(($filters['activo'] ?? '') === '0')>Inactivo</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Límite</label>
                    <select name="limit" class="form-select form-select-sm">
                        @foreach ([10, 15, 25, 50] as $n)
                            <option @selected(request('limit', 15) == $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button class="btn btn-sm btn-primary w-100">
                        <i class="bx bx-search me-1"></i> Buscar
                    </button>
                    <a href="{{ route('planificacion.comerciales.index') }}" class="btn btn-sm btn-secondary w-100">
                        Reset
                    </a>
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
                                    'sort_by' => 'nombre',
                                    'sort_dir' => request('sort_by') === 'nombre' && request('sort_dir') === 'asc' ? 'desc' : 'asc',
                                ]) }}">
                                Comercial
                                @if (request('sort_by') === 'nombre')
                                    <i class="bx bx-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }}-arrow-alt"></i>
                                @endif
                            </a>
                        </th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Departamento</th>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'fecha_ingreso',
                                    'sort_dir' => request('sort_by') === 'fecha_ingreso' && request('sort_dir') === 'asc' ? 'desc' : 'asc',
                                ]) }}">
                                F. Ingreso
                                @if (request('sort_by') === 'fecha_ingreso')
                                    <i class="bx bx-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }}-arrow-alt"></i>
                                @endif
                            </a>
                        </th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comerciales as $c)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $c->nombre }} {{ $c->apellido }}</strong>
                                    @if ($c->codigo_empleado)
                                        <br><small class="text-muted">{{ $c->codigo_empleado }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $c->email }}</td>
                            <td>{{ $c->telefono ?? '-' }}</td>
                            <td>
                                @if ($c->departamento)
                                    <span class="badge bg-label-info">
                                        {{ ucwords(str_replace('_', ' ', $c->departamento)) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $c->fecha_ingreso ? $c->fecha_ingreso->format('d/m/Y') : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $c->activo ? 'success' : 'danger' }}">
                                    {{ $c->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('planificacion.comerciales.show', $c) }}"
                                    class="btn btn-sm btn-outline-secondary">Ver</a>
                                <a href="{{ route('planificacion.comerciales.edit', $c) }}"
                                    class="btn btn-sm btn-outline-primary">Editar</a>
                                <form action="{{ route('planificacion.comerciales.destroy', $c) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('¿Eliminar comercial {{ $c->nombre }} {{ $c->apellido }}?');">
                                    @csrf
                                    @method('DELETE')
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
            {{ $comerciales->links() }}
        </div>
    </div>
@endsection
