@extends('layouts/contentNavbarLayout')
@section('title', 'Comerciales - Listado')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">
            <i class="fa-solid fa-users"></i> Comerciales
        </h4>
        <a href="{{ route('planificacion.comerciales.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i> Nuevo comercial
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fa-solid fa-filter"></i> Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('planificacion.comerciales.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Nombre, email o documento..."
                        value="{{ $filters['search'] ?? '' }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Zona</label>
                    <select name="zona_id" class="form-select">
                        <option value="">— Todas las zonas —</option>
                        @foreach ($zonas as $zona)
                            <option value="{{ $zona->id }}" @selected(($filters['zona_id'] ?? '') == $zona->id)>
                                {{ $zona->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">— Todos —</option>
                        <option value="activo" @selected(($filters['estado'] ?? '') === 'activo')>
                            Activo
                        </option>
                        <option value="inactivo" @selected(($filters['estado'] ?? '') === 'inactivo')>
                            Inactivo
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Por página</label>
                    <select name="limit" class="form-select">
                        @foreach ([15, 25, 50, 100] as $num)
                            <option value="{{ $num }}" @selected(request('limit', 15) == $num)>
                                {{ $num }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Documento</th>
                        <th>Zona</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <th>Registrado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comerciales as $comercial)
                        <tr>
                            <td>
                                <strong>{{ $comercial->name }}</strong>
                            </td>
                            <td>
                                <a href="mailto:{{ $comercial->email }}">{{ $comercial->email }}</a>
                            </td>
                            <td>{{ $comercial->documento }}</td>
                            <td>
                                @if ($comercial->zona)
                                    <span class="badge bg-label-primary">
                                        {{ $comercial->zona->nombre }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-info">
                                    {{ $comercial->capacidad_paradas_dia }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $comercial->estado === 'activo' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($comercial->estado) }}
                                </span>
                            </td>
                            <td>{{ $comercial->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('planificacion.comerciales.show', $comercial) }}"
                                        class="btn btn-sm btn-icon btn-outline-secondary" title="Ver detalles">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('planificacion.comerciales.edit', $comercial) }}"
                                        class="btn btn-sm btn-icon btn-outline-primary" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('planificacion.comerciales.destroy', $comercial) }}"
                                        method="POST" id="delete-form-{{ $comercial->id }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger js-delete-btn"
                                            data-form-id="delete-form-{{ $comercial->id }}"
                                            data-name="{{ $comercial->name }}" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                                No hay comerciales registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($comerciales instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $comerciales->firstItem() ?? 0 }}–{{ $comerciales->lastItem() ?? 0 }}
                    de {{ $comerciales->total() }} comerciales
                </small>
                {{ $comerciales->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

{{-- Modal de confirmación --}}
@section('modals')
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                        Confirmar eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        ¿Está seguro de eliminar al comercial
                        <strong id="delete-name"></strong>?
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            let targetForm = null;

            // Abrir modal
            document.querySelectorAll('.js-delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const formId = this.dataset.formId;
                    const name = this.dataset.name;

                    targetForm = document.getElementById(formId);
                    document.getElementById('delete-name').textContent = name;
                    modal.show();
                });
            });

            // Confirmar eliminación
            document.getElementById('confirm-delete').addEventListener('click', function() {
                if (targetForm) {
                    targetForm.submit();
                }
            });
        });
    </script>
@endpush
