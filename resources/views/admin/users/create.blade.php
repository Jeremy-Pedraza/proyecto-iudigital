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
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Volver
        </a>
    </div>

    {{-- Mostrar errores generales --}}
    @if ($errors->has('general'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ $errors->first('general') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Mostrar todos los errores de validación --}}
    @if ($errors->any() && !$errors->has('general'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-3">
                    <label class="form-label">Nombres <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" placeholder="Ej: Ana" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                    <input type="text" name="lastname" value="{{ old('lastname') }}"
                        class="form-control @error('lastname') is-invalid @enderror" placeholder="Ej: Pérez Gómez" required>
                    @error('lastname')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="username" value="{{ old('username') }}"
                        class="form-control @error('username') is-invalid @enderror"
                        placeholder="Opcional (se puede usar para login)">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Opcional - Si no se proporciona, se usará el email para iniciar sesión</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" placeholder="correo@dominio.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Mínimo 8 caracteres" required minlength="8">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation"
                        class="form-control @error('password_confirmation') is-invalid @enderror"
                        placeholder="Repite la contraseña" required minlength="8">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Debe coincidir con la contraseña</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Rol</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="">— Sin rol —</option>
                        @foreach ($roles as $label => $value)
                            <option value="{{ $value }}" @selected(old('role') === $value)>
                                {{ $label }}
                            </option>
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
                        <label class="form-check-label" for="is_active">
                            Activo
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <hr class="my-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy"></i> Crear usuario
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Validación en tiempo real de contraseñas
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.querySelector('input[name="password"]');
            const passwordConfirm = document.querySelector('input[name="password_confirmation"]');

            if (password && passwordConfirm) {
                passwordConfirm.addEventListener('input', function() {
                    if (password.value !== passwordConfirm.value) {
                        passwordConfirm.setCustomValidity('Las contraseñas no coinciden');
                    } else {
                        passwordConfirm.setCustomValidity('');
                    }
                });

                password.addEventListener('input', function() {
                    if (passwordConfirm.value && password.value !== passwordConfirm.value) {
                        passwordConfirm.setCustomValidity('Las contraseñas no coinciden');
                    } else {
                        passwordConfirm.setCustomValidity('');
                    }
                });
            }
        });
    </script>
@endpush
