@extends('layouts/contentNavbarLayout')
@section('title', 'Zonas - Listado')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Zonas</h4>
        <a href="{{ route('planificacion.zonas.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nueva zona
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <form method="GET" action="{{ route('planificacion.zonas.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control"
                        placeholder="Nombre, código o descripción...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" value="{{ $filters['ciudad'] ?? '' }}" class="form-control"
                        placeholder="Filtrar por ciudad...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">-- Todos --</option>
                        <option value="activa" @selected(($filters['estado'] ?? '') === 'activa')>Activa</option>
                        <option value="inactiva" @selected(($filters['estado'] ?? '') === 'inactiva')>Inactiva</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ordenar por</label>
                    <select name="sort_by" class="form-select">
                        <option value="nombre" @selected(($filters['sort_by'] ?? 'nombre') === 'nombre')>Nombre</option>
                        <option value="codigo" @selected(($filters['sort_by'] ?? '') === 'codigo')>Código</option>
                        <option value="prioridad" @selected(($filters['sort_by'] ?? '') === 'prioridad')>Prioridad</option>
                        <option value="created_at" @selected(($filters['sort_by'] ?? '') === 'created_at')>Fecha creación</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex justify-content-center">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="card-datatable table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 30px;"></th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Ciudades</th>
                        <th>Tipo</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zonas as $z)
                        <tr>
                            <td>
                                <span class="badge"
                                    style="background-color: {{ $z->color }}; width: 20px; height: 20px;"></span>
                            </td>
                            <td><strong>{{ $z->codigo }}</strong></td>
                            <td>{{ $z->nombre }}</td>
                            <td>
                                @if ($z->ciudades && count($z->ciudades) > 0)
                                    @foreach (array_slice($z->ciudades, 0, 3) as $c)
                                        <span class="badge bg-label-info">{{ $c }}</span>
                                    @endforeach
                                    @if (count($z->ciudades) > 3)
                                        <span class="badge bg-label-secondary">+{{ count($z->ciudades) - 3 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($z->poligono)
                                    <span class="badge bg-label-primary">Polígono</span>
                                @elseif($z->centro_lat && $z->radio_km)
                                    <span class="badge bg-label-success">Radio ({{ $z->radio_km }} km)</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span
                                    class="badge bg-label-{{ $z->prioridad <= 2 ? 'danger' : ($z->prioridad <= 3 ? 'warning' : 'secondary') }}">
                                    {{ $z->prioridad }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $z->estado === 'activa' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($z->estado) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('planificacion.zonas.show', $z) }}"
                                    class="btn btn-sm btn-icon btn-outline-secondary" title="Ver">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                <a href="{{ route('planificacion.zonas.edit', $z) }}"
                                    class="btn btn-sm btn-icon btn-outline-primary" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('planificacion.zonas.destroy', $z) }}" method="POST"
                                    id="delete-form-{{ $z->id }}" class="d-inline js-delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger js-open-delete"
                                        data-form="delete-form-{{ $z->id }}" data-name="{{ $z->nombre }}"
                                        title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No hay zonas para los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($zonas instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $zonas->firstItem() }}–{{ $zonas->lastItem() }} de {{ $zonas->total() }}
                </small>
                {{ $zonas->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        ¿Seguro que deseas eliminar la zona <strong data-zona-name></strong>?
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btn-confirm-delete">Sí, eliminar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let targetFormId = null;
            const modalEl = document.getElementById('modalDelete');
            const nameEl = modalEl.querySelector('[data-zona-name]');
            const confirmEl = document.getElementById('btn-confirm-delete');
            const bsModal = new bootstrap.Modal(modalEl);

            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.js-open-delete');
                if (!btn) return;
                targetFormId = btn.dataset.form || null;
                nameEl.textContent = btn.dataset.name || '';
                bsModal.show();
            });

            confirmEl.addEventListener('click', function() {
                if (!targetFormId) return;
                const form = document.getElementById(targetFormId);
                if (form) form.submit();
            });
        });
    </script>
@endpush
