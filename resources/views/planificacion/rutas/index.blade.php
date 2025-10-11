@extends('layouts/contentNavbarLayout')

@section('title', 'Rutas - Listado')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Gestión de Rutas</h4>
        <a href="{{ route('planificacion.rutas.planificar.form') }}" class="btn btn-primary">
            <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Planificar nueva ruta
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <form method="GET" action="{{ route('planificacion.rutas.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           class="form-control" placeholder="Nombre o ID...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">-- Todos --</option>
                        @foreach (['borrador' => 'Borrador', 'calculada' => 'Calculada', 'publicada' => 'Publicada', 'en_ejecucion' => 'En ejecución', 'completada' => 'Completada', 'cancelada' => 'Cancelada'] as $k => $v)
                            <option value="{{ $k }}" @selected(($filters['estado'] ?? '') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ $filters['fecha_inicio'] ?? '' }}"
                           class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ $filters['fecha_fin'] ?? '' }}"
                           class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ordenar por</label>
                    <select name="sort_by" class="form-select">
                        <option value="created_at" @selected(($filters['sort_by'] ?? 'created_at') === 'created_at')>Fecha creación</option>
                        <option value="fecha_inicio" @selected(($filters['sort_by'] ?? '') === 'fecha_inicio')>Fecha inicio</option>
                        <option value="nombre" @selected(($filters['sort_by'] ?? '') === 'nombre')>Nombre</option>
                        <option value="estado" @selected(($filters['sort_by'] ?? '') === 'estado')>Estado</option>
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
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Período</th>
                        <th>Comercial</th>
                        <th>Paradas</th>
                        <th>Distancia</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rutas as $r)
                        <tr>
                            <td><strong>#{{ $r->id }}</strong></td>
                            <td>{{ $r->nombre }}</td>
                            <td>
                                <small>{{ $r->fecha_inicio->format('d/m/Y') }} - {{ $r->fecha_fin->format('d/m/Y') }}</small>
                            </td>
                            <td>{{ $r->comercial->name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-label-info">{{ $r->paradas_count ?? 0 }}</span>
                            </td>
                            <td>{{ number_format($r->distancia_total_km ?? 0, 1) }} km</td>
                            <td>
                                @php
                                    $badgeClass = match($r->estado) {
                                        'borrador' => 'secondary',
                                        'calculada' => 'info',
                                        'publicada' => 'primary',
                                        'en_ejecucion' => 'warning',
                                        'completada' => 'success',
                                        'cancelada' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $r->estado)) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('planificacion.rutas.show', $r) }}"
                                   class="btn btn-sm btn-icon btn-outline-secondary" title="Ver">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                @if(in_array($r->estado, ['borrador', 'calculada']))
                                    <a href="{{ route('planificacion.rutas.editor', $r) }}"
                                       class="btn btn-sm btn-icon btn-outline-primary" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                @endif
                                @if($r->estado === 'calculada')
                                    <form action="{{ route('planificacion.rutas.publicar', $r) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-success"
                                                title="Publicar">
                                            <i class="fa-solid fa-paper-plane"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(!in_array($r->estado, ['en_ejecucion', 'completada']))
                                    <form action="{{ route('planificacion.rutas.destroy', $r) }}"
                                          method="POST" id="delete-form-{{ $r->id }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                class="btn btn-sm btn-icon btn-outline-danger js-open-delete"
                                                data-form="delete-form-{{ $r->id }}"
                                                data-name="{{ $r->nombre }}"
                                                title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No hay rutas para los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rutas instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $rutas->firstItem() }}–{{ $rutas->lastItem() }} de {{ $rutas->total() }}
                </small>
                {{ $rutas->appends(request()->query())->links() }}
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        ¿Seguro que deseas eliminar la ruta
                        <strong data-name></strong>?
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="btn-confirm-delete">
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
            let targetFormId = null;
            const modalEl = document.getElementById('modalDelete');
            const nameEl = modalEl.querySelector('[data-name]');
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
