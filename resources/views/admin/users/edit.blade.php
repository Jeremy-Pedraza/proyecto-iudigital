@extends('layouts/contentNavbarLayout')

@section('title', 'Usuarios - Editar')

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
        <h4 class="mb-0">Editar usuario</h4>
        <div>
            <a href="{{ route('admin.usuarios.show', $usuario) }}" class="btn btn-outline-secondary me-2"><i
                    class="ti ti-eye"></i> Ver</a>
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
    @if ($errors->has('general'))
        <div class="alert alert-danger" role="alert">{{ $errors->first('general') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST" class="row g-3">
                @csrf @method('PUT')

                <div class="col-md-6">
                    <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $usuario->name) }}"
                        class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="username" value="{{ old('username', $usuario->username) }}"
                        class="form-control @error('username') is-invalid @enderror">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}"
                        class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Nueva contraseña</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Dejar en blanco para mantener">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Rol</label>
                    @php
                        $selectedRole = null;
                        if (method_exists($usuario, 'getRoleNames')) {
                            $roleNames = $usuario->getRoleNames();
                            $selectedRole = $roleNames[0] ?? null;
                        } else {
                            $selectedRole = $usuario->role ?? null;
                        }
                    @endphp
                    <select name="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="">— Sin rol —</option>
                        @foreach ($roles as $label => $value)
                            <option value="{{ $value }}" @selected(old('role', $selectedRole) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $usuario->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Activo</label>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary"><i class="ti ti-device-floppy"></i> Guardar cambios</button>
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST"
                        class="d-inline float-end" onsubmit="return confirm('¿Eliminar este usuario?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="ti ti-trash"></i> Eliminar</button>
                    </form>
                </div>
            </form>
        </div>
    </div>
@endsection
