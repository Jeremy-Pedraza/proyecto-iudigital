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
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Nuevo usuario
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
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control"
                        placeholder="Nombre, email o usuario…">
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
                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-secondary w-100" type="submit"><i class="ti ti-search"></i></button>
                </div>
            </form>
        </div>

        <div class="card-datatable table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
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
                            <td>{{ $u->username ?? '—' }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @if (method_exists($u, 'getRoleNames'))
                                    @php $rn = $u->getRoleNames(); @endphp
                                    @forelse($rn as $r)
                                    <span class="badge bg-label-primary me-1">{{ $r }}</span> @empty <span
                                            class="text-muted">Sin rol</span>
                                    @endforelse
                                @elseif(!empty($u->role))
                                    <span class="badge bg-label-primary">{{ $u->role }}</span>
                                @else
                                    <span class="text-muted">Sin rol</span>
                                @endif
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
                                <a href="{{ route('admin.users.show', $u) }}"
                                    class="btn btn-sm btn-icon btn-outline-secondary" title="Ver">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $u) }}"
                                    class="btn btn-sm btn-icon btn-outline-primary" title="Editar">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('¿Eliminar este usuario?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon btn-outline-danger" type="submit" title="Eliminar">
                                        <i class="ti ti-trash"></i>
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
