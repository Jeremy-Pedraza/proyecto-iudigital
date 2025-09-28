@extends('layouts/contentNavbarLayout')

@section('title', 'Usuarios - Detalle')

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
        <h4 class="mb-0">Detalle de usuario</h4>
        <div>
            <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-primary me-2"><i class="ti ti-edit"></i>
                Editar</a>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left"></i>
                Volver</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Nombre</dt>
                        <dd class="col-sm-9">{{ $usuario->name }}</dd>

                        <dt class="col-sm-3">Usuario</dt>
                        <dd class="col-sm-9">{{ $usuario->username ?? '—' }}</dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $usuario->email }}</dd>

                        <dt class="col-sm-3">Estado</dt>
                        <dd class="col-sm-9">
                            @if ($usuario->is_active ?? true)
                                <span class="badge bg-label-success">Activo</span>
                            @else
                                <span class="badge bg-label-secondary">Inactivo</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Rol(es)</dt>
                        <dd class="col-sm-9">
                            @if (method_exists($usuario, 'getRoleNames'))
                                @forelse($usuario->getRoleNames() as $r)
                                    <span class="badge bg-label-primary me-1">{{ $r }}</span>
                                @empty
                                    <span class="text-muted">Sin rol asignado</span>
                                @endforelse
                            @elseif(!empty($usuario->role))
                                <span class="badge bg-label-primary">{{ $usuario->role }}</span>
                            @else
                                <span class="text-muted">Sin rol asignado</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Creado</dt>
                        <dd class="col-sm-9">{{ optional($usuario->created_at)->format('Y-m-d H:i') }}</dd>

                        <dt class="col-sm-3">Actualizado</dt>
                        <dd class="col-sm-9">{{ optional($usuario->updated_at)->format('Y-m-d H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-primary"><i
                            class="ti ti-edit"></i> Editar</a>
                    <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST"
                        onsubmit="return confirm('¿Eliminar este usuario?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="ti ti-trash"></i> Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
