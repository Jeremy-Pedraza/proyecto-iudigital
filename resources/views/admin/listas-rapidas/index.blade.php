@extends('layouts/contentNavbarLayout')

@section('title', 'Listas rápidas - Listado')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Listas rápidas</h4>
        <a href="{{ route('admin.listas-rapidas.create') }}" class="btn btn-primary">
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
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Buscar (grupo/clave/valor)"
                        value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="grupo" placeholder="Grupo"
                        value="{{ $filters['grupo'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <select name="estado" class="form-select">
                        <option value="">Estado (todos)</option>
                        <option value="1" @selected(($filters['estado'] ?? '') === '1')>Activo</option>
                        <option value="0" @selected(($filters['estado'] ?? '') === '0')>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="perPage" class="form-select">
                        @foreach ([10, 15, 25, 50] as $pp)
                            <option value="{{ $pp }}" @selected(request('perPage', 10) == $pp)>{{ $pp }} por página
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <a href="{{ route('admin.listas-rapidas.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    @php
                        $sortBy = $filters['sortBy'] ?? 'grupo';
                        $sortDir = $filters['sortDir'] ?? 'asc';
                        $toggle = $sortDir === 'asc' ? 'desc' : 'asc';

                        $sortLink = function (string $col) use ($sortBy, $toggle) {
                            $q = request()->query();
                            $q['sortBy'] = $col;
                            $q['sortDir'] = $sortBy === $col ? $toggle : 'asc';
                            return request()->url() . '?' . http_build_query($q);
                        };
                    @endphp
                    <tr>
                        <th><a href="{{ $sortLink('grupo') }}">Grupo</a></th>
                        <th><a href="{{ $sortLink('clave') }}">Clave</a></th>
                        <th><a href="{{ $sortLink('valor') }}">Valor</a></th>
                        <th>Estado</th>
                        <th style="width:140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $row)
                        <tr>
                            <td>{{ $row->grupo }}</td>
                            <td>{{ $row->clave }}</td>
                            <td>{{ $row->valor }}</td>
                            <td>
                                @if ($row->estado)
                                    <span class="badge bg-label-success">Activo</span>
                                @else
                                    <span class="badge bg-label-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-sm btn-outline-secondary"
                                    href="{{ route('admin.listas-rapidas.show', $row->id) }}">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('admin.listas-rapidas.edit', $row->id) }}">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('admin.listas-rapidas.destroy', $row->id) }}" method="POST"
                                    id="delete-form-{{ $row->id }}" class="d-inline js-delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger js-open-delete"
                                        data-form="delete-form-{{ $row->id }}"
                                        data-fastlist-name="{{ $row->grupo }}" title="Eliminar">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Sin resultados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
            <div class="card-footer">
                {{ $items->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection


@section('modals')
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Confirmar eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0">
                        ¿Seguro que deseas eliminar la Lista Rápida
                        <strong data-fastlist-name></strong>?
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
            const nameEl = modalEl.querySelector('[data-fastlist-name]');
            console.warn(nameEl);
            const confirmEl = document.getElementById('btn-confirm-delete');
            const bsModal = new bootstrap.Modal(modalEl);

            // Delegación: cualquier botón .js-open-delete abre el modal
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.js-open-delete');
                if (!btn) return;

                targetFormId = btn.dataset.form || null;
                nameEl.textContent = btn.dataset.fastlistName || '';
                bsModal.show();
            });

            // Al confirmar, enviamos el formulario objetivo
            confirmEl.addEventListener('click', function() {
                if (!targetFormId) return;
                const form = document.getElementById(targetFormId);
                if (form) form.submit();
            });
        });
    </script>
@endpush
