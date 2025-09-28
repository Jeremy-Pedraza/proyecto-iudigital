@extends('layouts/contentNavbarLayout')

@section('title', 'Usuarios - Crear')

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
        <h4 class="mb-0">Nuevo usuario</h4>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Volver
        </a>
    </div>

    @if ($errors->has('general'))
        <div class="alert alert-danger" role="alert">{{ $errors->first('general') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.usuarios.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" placeholder="Ej: Ana Pérez">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="username" value="{{ old('username') }}"
                        class="form-control @error('username') is-invalid @enderror" placeholder="Opcional">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" placeholder="correo@dominio.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Rol</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="">— Sin rol —</option>
                        @foreach ($roles as $label => $value)
                            <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Activo</label>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary"><i class="ti ti-device-floppy"></i> Guardar</button>
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
