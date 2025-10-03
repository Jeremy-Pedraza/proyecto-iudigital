@extends('layouts/contentNavbarLayout')

@section('title', 'Crear Comercial')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Planificación / Comerciales /</span> Crear
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Crear Nuevo Comercial</h5>
                        <a href="{{ route('planificacion.comerciales.index') }}" class="btn btn-label-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="createComercialForm" action="{{ route('planificacion.comerciales.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="nombre">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                    id="nombre" name="nombre" placeholder="Ingrese el nombre"
                                    value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="apellido">Apellido <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('apellido') is-invalid @enderror"
                                    id="apellido" name="apellido" placeholder="Ingrese el apellido"
                                    value="{{ old('apellido') }}" required>
                                @error('apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" placeholder="comercial@empresa.com"
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="telefono">Teléfono</label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                    id="telefono" name="telefono" placeholder="+1 234 567 890"
                                    value="{{ old('telefono') }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="codigo_empleado">Código Empleado</label>
                                <input type="text" class="form-control @error('codigo_empleado') is-invalid @enderror"
                                    id="codigo_empleado" name="codigo_empleado" placeholder="EMP001"
                                    value="{{ old('codigo_empleado') }}">
                                @error('codigo_empleado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="departamento">Departamento</label>
                                <select class="form-select select2 @error('departamento') is-invalid @enderror"
                                    id="departamento" name="departamento">
                                    <option value="">Seleccionar departamento</option>
                                    <option value="ventas" {{ old('departamento') == 'ventas' ? 'selected' : '' }}>Ventas
                                    </option>
                                    <option value="marketing" {{ old('departamento') == 'marketing' ? 'selected' : '' }}>
                                        Marketing</option>
                                    <option value="atencion_cliente"
                                        {{ old('departamento') == 'atencion_cliente' ? 'selected' : '' }}>Atención al
                                        Cliente</option>
                                    <option value="desarrollo_negocio"
                                        {{ old('departamento') == 'desarrollo_negocio' ? 'selected' : '' }}>Desarrollo de
                                        Negocio</option>
                                </select>
                                @error('departamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label" for="direccion">Dirección</label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" rows="3"
                                    placeholder="Ingrese la dirección completa">{{ old('direccion') }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="fecha_ingreso">Fecha de Ingreso</label>
                                <input type="date" class="form-control @error('fecha_ingreso') is-invalid @enderror"
                                    id="fecha_ingreso" name="fecha_ingreso"
                                    value="{{ old('fecha_ingreso', date('Y-m-d')) }}">
                                @error('fecha_ingreso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="salario_base">Salario Base</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('salario_base') is-invalid @enderror"
                                        id="salario_base" name="salario_base" placeholder="0.00" step="0.01"
                                        min="0" value="{{ old('salario_base') }}">
                                </div>
                                @error('salario_base')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('activo') is-invalid @enderror" type="checkbox"
                                        id="activo" name="activo" value="1"
                                        {{ old('activo', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">
                                        Comercial Activo
                                    </label>
                                    @error('activo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <a href="{{ route('planificacion.comerciales.index') }}"
                                        class="btn btn-label-secondary">
                                        <i class="bx bx-x me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>Guardar Comercial
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        // Función para cargar scripts dinámicamente después de jQuery
        function loadScript(src) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        // Esperar a que jQuery esté disponible
        const initPage = () => {
            if (typeof $ === 'undefined' || typeof jQuery === 'undefined') {
                setTimeout(initPage, 50);
                return;
            }

            // Cargar Select2 después de que jQuery esté disponible
            loadScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js')
                .then(() => {
                    // Inicializar Select2
                    $('.select2').select2({
                        placeholder: "Seleccionar...",
                        allowClear: true,
                        width: '100%',
                        language: {
                            noResults: function() {
                                return "No se encontraron resultados";
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error cargando Select2:', error);
                });

            // Validación del formulario
            const form = document.getElementById('createComercialForm');

            form.addEventListener('submit', function(e) {
                let isValid = true;
                const errors = [];

                // Limpiar validaciones previas
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });

                // Validar nombre
                const nombre = document.getElementById('nombre');
                if (!nombre.value.trim() || nombre.value.length < 2) {
                    isValid = false;
                    nombre.classList.add('is-invalid');
                    errors.push('El nombre es requerido y debe tener al menos 2 caracteres');
                }

                // Validar apellido
                const apellido = document.getElementById('apellido');
                if (!apellido.value.trim() || apellido.value.length < 2) {
                    isValid = false;
                    apellido.classList.add('is-invalid');
                    errors.push('El apellido es requerido y debe tener al menos 2 caracteres');
                }

                // Validar email
                const email = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email.value.trim() || !emailRegex.test(email.value)) {
                    isValid = false;
                    email.classList.add('is-invalid');
                    errors.push('Ingrese un email válido');
                }

                // Validar teléfono (opcional pero si existe debe ser válido)
                const telefono = document.getElementById('telefono');
                if (telefono.value.trim()) {
                    const telefonoClean = telefono.value.replace(/\D/g, '');
                    if (telefonoClean.length < 10 || telefonoClean.length > 15) {
                        isValid = false;
                        telefono.classList.add('is-invalid');
                        errors.push('El teléfono debe tener entre 10 y 15 dígitos');
                    }
                }

                // Validar salario base (debe ser positivo si existe)
                const salario = document.getElementById('salario_base');
                if (salario.value && parseFloat(salario.value) < 0) {
                    isValid = false;
                    salario.classList.add('is-invalid');
                    errors.push('El salario base debe ser un valor positivo');
                }

                if (!isValid) {
                    e.preventDefault();

                    // Mostrar errores
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de validación',
                            html: errors.join('<br>'),
                            confirmButtonText: 'Entendido',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    } else {
                        alert('Errores de validación:\n\n' + errors.join('\n'));
                    }

                    // Scroll al primer error
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstError.focus();
                    }
                }
            });

            // Limpiar errores al escribir
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });

            // Formatear teléfono mientras se escribe
            const telefonoInput = document.getElementById('telefono');
            if (telefonoInput) {
                telefonoInput.addEventListener('input', function(e) {
                    // Permitir solo números, espacios, + y -
                    this.value = this.value.replace(/[^\d\s\+\-]/g, '');
                });
            }

            // Formatear código empleado a mayúsculas
            const codigoInput = document.getElementById('codigo_empleado');
            if (codigoInput) {
                codigoInput.addEventListener('input', function(e) {
                    this.value = this.value.toUpperCase();
                });
            }
        };

        // Iniciar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPage);
        } else {
            initPage();
        }
    </script>
@endsection
