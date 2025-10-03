@extends('layouts/contentNavbarLayout')

@section('title', 'Ver Comercial')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Planificación / Comerciales /</span> Ver Detalle
    </h4>

    <div class="row">
        <!-- Información del Comercial -->
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-user me-2"></i>Información del Comercial
                        </h5>
                        <div>
                            <a href="{{ route('planificacion.comerciales.edit', $comercial->id) }}"
                                class="btn btn-sm btn-primary me-2">
                                <i class="bx bx-edit me-1"></i>Editar
                            </a>
                            <a href="{{ route('planificacion.comerciales.index') }}" class="btn btn-sm btn-label-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Nombre Completo</h6>
                            <p class="mb-0 fw-semibold">{{ $comercial->nombre }} {{ $comercial->apellido }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Email</h6>
                            <p class="mb-0">
                                <a href="mailto:{{ $comercial->email }}">{{ $comercial->email }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Teléfono</h6>
                            <p class="mb-0">{{ $comercial->telefono ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Código Empleado</h6>
                            <p class="mb-0">
                                @if ($comercial->codigo_empleado)
                                    <span class="badge bg-label-info">{{ $comercial->codigo_empleado }}</span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Departamento</h6>
                            <p class="mb-0">
                                @if ($comercial->departamento)
                                    <span class="badge bg-label-primary">
                                        {{ ucfirst(str_replace('_', ' ', $comercial->departamento)) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Estado</h6>
                            <p class="mb-0">
                                <span class="badge bg-{{ $comercial->activo ? 'success' : 'danger' }}">
                                    {{ $comercial->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-1">Dirección</h6>
                            <p class="mb-0">{{ $comercial->direccion ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Fecha de Ingreso</h6>
                            <p class="mb-0">
                                @if ($comercial->fecha_ingreso)
                                    {{ $comercial->fecha_ingreso->format('d/m/Y') }}
                                    <small class="text-muted">
                                        ({{ $comercial->fecha_ingreso->diffForHumans() }})
                                    </small>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Salario Base</h6>
                            <p class="mb-0">
                                @if ($comercial->salario_base)
                                    <span class="fw-semibold">${{ number_format($comercial->salario_base, 2) }}</span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Fecha de Creación</h6>
                            <p class="mb-0">
                                <small>{{ $comercial->created_at->format('d/m/Y H:i') }}</small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Última Actualización</h6>
                            <p class="mb-0">
                                <small>{{ $comercial->updated_at->format('d/m/Y H:i') }}</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar con estadísticas o acciones rápidas -->
        <div class="col-lg-4 col-md-12">
            <!-- Estadísticas del Comercial -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-bar-chart me-2"></i>Estadísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">Clientes Asignados</h6>
                            <small class="text-muted">Total de clientes</small>
                        </div>
                        <div class="text-end">
                            <h4 class="mb-0 text-primary">0</h4>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">Visitas Programadas</h6>
                            <small class="text-muted">Este mes</small>
                        </div>
                        <div class="text-end">
                            <h4 class="mb-0 text-info">0</h4>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Visitas Realizadas</h6>
                            <small class="text-muted">Este mes</small>
                        </div>
                        <div class="text-end">
                            <h4 class="mb-0 text-success">0</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-cog me-2"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('planificacion.comerciales.edit', $comercial->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>Editar Información
                        </a>

                        <button type="button" class="btn btn-info" disabled>
                            <i class="bx bx-calendar me-1"></i>Ver Agenda
                        </button>

                        <button type="button" class="btn btn-success" disabled>
                            <i class="bx bx-user-plus me-1"></i>Asignar Cliente
                        </button>

                        <hr class="my-2">

                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $comercial->id }})">
                            <i class="bx bx-trash me-1"></i>Eliminar Comercial
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="bx bx-error me-2"></i>
                        ¿Estás seguro de que deseas eliminar a <strong>{{ $comercial->nombre }}
                            {{ $comercial->apellido }}</strong>?
                    </div>
                    <p class="mb-0">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" method="POST"
                        action="{{ route('planificacion.comerciales.destroy', $comercial->id) }}"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-trash me-1"></i>Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        function confirmDelete(id) {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
@endsection
