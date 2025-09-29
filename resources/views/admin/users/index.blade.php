@extends('layouts/contentNavbarLayout')

@section('title', 'Usuarios - Listado')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
    @vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Usuarios</h4>
        <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i> Nuevo usuario
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any() && !$errors->has('general'))
        <div class="alert alert-danger" role="alert">
            Corrige los errores y vuelve a intentar.
        </div>
    @endif
    @if ($errors->has('general'))
        <div class="alert alert-danger" role="alert">
            {{ $errors->first('general') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.usuarios.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control"
                        placeholder="Nombre, apellido, email o usuario…">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rol</label>
                    <select name="role" class="form-select">
                        <option value="">-- Todos --</option>
                        @foreach ($roles as $label => $value)
                            <option value="{{ $value }}" @selected($role === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">-- Todos --</option>
                        <option value="active" @selected($status === 'active')>Activo</option>
                        <option value="inactive" @selected($status === 'inactive')>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Por página</label>
                    <select name="per_page" class="form-select">
                        @foreach ([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected($perPage == $n)>{{ $n }}</option>
                        @endforeach
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
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol(es)</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->lastname }}</td>
                            <td>{{ $u->username ?? '—' }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @php $rn = $u->role_names ?? []; @endphp
                                @forelse($rn as $r)
                                    <span class="badge bg-label-primary me-1">{{ $r }}</span>
                                @empty
                                    <span class="text-muted">Sin rol</span>
                                @endforelse
                            </td>
                            <td>
                                @if ($u->is_active ?? true)
                                    <span class="badge bg-label-success">Activo</span>
                                @else
                                    <span class="badge bg-label-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>{{ optional($u->created_at)->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.usuarios.show', $u) }}"
                                    class="btn btn-sm btn-icon btn-outline-secondary" title="Ver">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.usuarios.edit', $u) }}"
                                    class="btn btn-sm btn-icon btn-outline-primary" title="Editar">
                                    <i class="fa-solid fa-user-pen"></i>
                                </a>
                                <form action="{{ route('admin.usuarios.destroy', $u) }}" method="POST"
                                    id="delete-form-{{ $u->id }}" class="d-inline js-delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger js-open-delete"
                                        data-form="delete-form-{{ $u->id }}" data-name="{{ $u->name }}"
                                        title="Eliminar">
                                        <i class="fa-solid fa-user-minus"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay usuarios para los filtros
                                aplicados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }}
                </small>
                {{ $users->appends(request()->query())->links() }}
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
                        ¿Seguro que deseas eliminar el Usuario
                        <strong data-user-name></strong>?
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
            const nameEl = modalEl.querySelector('[data-user-name]');
            const confirmEl = document.getElementById('btn-confirm-delete');
            const bsModal = new bootstrap.Modal(modalEl);

            // Delegación: cualquier botón .js-open-delete abre el modal
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.js-open-delete');
                if (!btn) return;

                targetFormId = btn.dataset.form || null;
                nameEl.textContent = btn.dataset.name || '';
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
