@extends('layouts/contentNavbarLayout')

@section('title', 'Editar Comercial')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Planificación / Comerciales /</span> Editar
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Editar Comercial</h5>
                        <div>
                            <a href="{{ route('planificacion.comerciales.show', $comercial->id) }}"
                                class="btn btn-label-info me-2">
                                <i class="bx bx-show me-1"></i>Ver
                            </a>
                            <a href="{{ route('planificacion.comerciales.index') }}" class="btn btn-label-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="editComercialForm" action="{{ route('planificacion.comerciales.update', $comercial->id) }}"
                        method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="nombre">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                    id="nombre" name="nombre" placeholder="Ingrese el nombre"
                                    value="{{ old('nombre', $comercial->nombre) }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="apellido">Apellido <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('apellido') is-invalid @enderror"
                                    id="apellido" name="apellido" placeholder="Ingrese el apellido"
                                    value="{{ old('apellido', $comercial->apellido) }}" required>
                                @error('apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" placeholder="comercial@empresa.com"
                                    value="{{ old('email', $comercial->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="telefono">Teléfono</label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                    id="telefono" name="telefono" placeholder="+1 234 567 890"
                                    value="{{ old('telefono', $comercial->telefono) }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="codigo_empleado">Código Empleado</label>
                                <input type="text" class="form-control @error('codigo_empleado') is-invalid @enderror"
                                    id="codigo_empleado" name="codigo_empleado" placeholder="EMP001"
                                    value="{{ old('codigo_empleado', $comercial->codigo_empleado) }}">
                                @error('codigo_empleado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="departamento">Departamento</label>
                                <select class="form-select select2 @error('departamento') is-invalid @enderror"
                                    id="departamento" name="departamento">
                                    <option value="">Seleccionar departamento</option>
                                    <option value="ventas"
                                        {{ old('departamento', $comercial->departamento) == 'ventas' ? 'selected' : '' }}>
                                        Ventas</option>
                                    <option value="marketing"
                                        {{ old('departamento', $comercial->departamento) == 'marketing' ? 'selected' : '' }}>
                                        Marketing</option>
                                    <option value="atencion_cliente"
                                        {{ old('departamento', $comercial->departamento) == 'atencion_cliente' ? 'selected' : '' }}>
                                        Atención al Cliente</option>
                                    <option value="desarrollo_negocio"
                                        {{ old('departamento', $comercial->departamento) == 'desarrollo_negocio' ? 'selected' : '' }}>
                                        Desarrollo de Negocio</option>
                                </select>
                                @error('departamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label" for="direccion">Dirección</label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" rows="3"
                                    placeholder="Ingrese la dirección completa">{{ old('direccion', $comercial->direccion) }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="fecha_ingreso">Fecha de Ingreso</label>
                                <input type="date" class="form-control @error('fecha_ingreso') is-invalid @enderror"
                                    id="fecha_ingreso" name="fecha_ingreso"
                                    value="{{ old('fecha_ingreso', $comercial->fecha_ingreso ? $comercial->fecha_ingreso->format('Y-m-d') : '') }}">
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
                                        min="0" value="{{ old('salario_base', $comercial->salario_base) }}">
                                </div>
                                @error('salario_base')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('activo') is-invalid @enderror" type="checkbox"
                                        id="activo" name="activo"
                                        {{ old('activo', $comercial->activo) ? 'checked' : '' }}>
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
                                        <i class="bx bx-save me-1"></i>Actualizar Comercial
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

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: "Seleccionar...",
                allowClear: true
            });

            // Form Validation
            const editComercialForm = FormValidation.formValidation(
                document.getElementById('editComercialForm'), {
                    fields: {
                        nombre: {
                            validators: {
                                notEmpty: {
                                    message: 'El nombre es requerido'
                                },
                                stringLength: {
                                    min: 2,
                                    message: 'El nombre debe tener al menos 2 caracteres'
                                }
                            }
                        },
                        apellido: {
                            validators: {
                                notEmpty: {
                                    message: 'El apellido es requerido'
                                },
                                stringLength: {
                                    min: 2,
                                    message: 'El apellido debe tener al menos 2 caracteres'
                                }
                            }
                        },
                        email: {
                            validators: {
                                notEmpty: {
                                    message: 'El email es requerido'
                                },
                                emailAddress: {
                                    message: 'Ingrese un email válido'
                                }
                            }
                        },
                        telefono: {
                            validators: {
                                stringLength: {
                                    min: 10,
                                    max: 15,
                                    message: 'El teléfono debe tener entre 10 y 15 caracteres'
                                }
                            }
                        }
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({
                            eleValidClass: '',
                            rowSelector: '.mb-3'
                        }),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        autoFocus: new FormValidation.plugins.AutoFocus()
                    }
                }
            );
        });
    </script>
@endsection
