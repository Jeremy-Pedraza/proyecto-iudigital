@extends('layouts/contentNavbarLayout')
@section('title', 'Zonas - Detalle')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge"
                style="background-color: {{ $zona->color }}; width: 30px; height: 30px; border-radius: 4px;"></span>
            <h4 class="mb-0">{{ $zona->nombre }}</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('planificacion.zonas.edit', $zona) }}" class="btn btn-primary">
                <i class="fa-solid fa-pen"></i> Editar
            </a>
            <a href="{{ route('planificacion.zonas.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Información general</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Código</dt>
                        <dd class="col-sm-9"><strong>{{ $zona->codigo }}</strong></dd>

                        <dt class="col-sm-3">Nombre</dt>
                        <dd class="col-sm-9">{{ $zona->nombre }}</dd>

                        <dt class="col-sm-3">Descripción</dt>
                        <dd class="col-sm-9">{{ $zona->descripcion ?? '—' }}</dd>

                        <dt class="col-sm-3">Estado</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-{{ $zona->estado === 'activa' ? 'success' : 'secondary' }}">
                                {{ ucfirst($zona->estado) }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Prioridad</dt>
                        <dd class="col-sm-9">
                            <span
                                class="badge bg-{{ $zona->prioridad <= 2 ? 'danger' : ($zona->prioridad <= 3 ? 'warning' : 'secondary') }}">
                                {{ $zona->prioridad }} -
                                {{ $zona->prioridad <= 2 ? 'Alta' : ($zona->prioridad <= 3 ? 'Media' : 'Baja') }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Ciudades</dt>
                        <dd class="col-sm-9">
                            @if ($zona->ciudades && count($zona->ciudades) > 0)
                                @foreach ($zona->ciudades as $c)
                                    <span class="badge bg-label-info">{{ $c }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Color</dt>
                        <dd class="col-sm-9">
                            <span class="badge" style="background-color: {{ $zona->color }}; padding: 8px 16px;">
                                {{ $zona->color }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Creada</dt>
                        <dd class="col-sm-9">{{ $zona->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-3">Última actualización</dt>
                        <dd class="col-sm-9">{{ $zona->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Delimitación geográfica</h5>
                </div>
                <div class="card-body">
                    @if ($zona->poligono && count($zona->poligono) > 0)
                        <div class="alert alert-info mb-3">
                            <i class="fa-solid fa-draw-polygon"></i>
                            <strong>Tipo:</strong> Polígono personalizado
                        </div>
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Puntos definidos</dt>
                            <dd class="col-sm-9">
                                <span class="badge bg-label-primary">{{ count($zona->poligono) }} vértices</span>
                            </dd>

                            <dt class="col-sm-3">Coordenadas</dt>
                            <dd class="col-sm-9">
                                <pre class="bg-light p-3 rounded font-monospace small mb-0" style="max-height: 300px; overflow-y: auto;">{{ json_encode($zona->poligono, JSON_PRETTY_PRINT) }}</pre>
                            </dd>
                        </dl>
                    @elseif($zona->centro_lat && $zona->radio_km)
                        <div class="alert alert-success mb-3">
                            <i class="fa-solid fa-circle-dot"></i>
                            <strong>Tipo:</strong> Radio desde punto central
                        </div>
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Punto central</dt>
                            <dd class="col-sm-9">
                                <div class="d-flex gap-3">
                                    <div>
                                        <small class="text-muted d-block">Latitud</small>
                                        <strong>{{ $zona->centro_lat }}</strong>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Longitud</small>
                                        <strong>{{ $zona->centro_lng }}</strong>
                                    </div>
                                </div>
                            </dd>

                            <dt class="col-sm-3">Radio de cobertura</dt>
                            <dd class="col-sm-9">
                                <span class="badge bg-label-success" style="font-size: 1rem; padding: 8px 16px;">
                                    {{ $zona->radio_km }} km
                                </span>
                            </dd>

                            <dt class="col-sm-3">Área aproximada</dt>
                            <dd class="col-sm-9">
                                <span class="text-muted">
                                    {{ number_format(pi() * pow($zona->radio_km, 2), 2) }} km²
                                </span>
                            </dd>

                            <dt class="col-sm-3">Mapa</dt>
                            <dd class="col-sm-9">
                                <a href="https://www.google.com/maps?q={{ $zona->centro_lat }},{{ $zona->centro_lng }}"
                                    target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-map-location-dot"></i> Ver en Google Maps
                                </a>
                            </dd>
                        </dl>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <strong>Sin delimitación geográfica definida.</strong>
                            <p class="mb-0 mt-2">
                                Esta zona no tiene configurada una delimitación geográfica específica.
                                Puedes editarla para agregar un radio o polígono.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            @if ($zona->notas)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-note-sticky"></i> Notas adicionales
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $zona->notas }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-sliders"></i> Acciones
                    </h5>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('planificacion.zonas.edit', $zona) }}" class="btn btn-primary">
                        <i class="fa-solid fa-pen"></i> Editar zona
                    </a>

                    @if ($zona->estado === 'activa')
                        <button type="button" class="btn btn-outline-warning js-estado-btn"
                            data-form-id="form-cambiar-estado" data-name="{{ $zona->nombre }}" data-estado="inactiva"
                            data-action="desactivar">
                            <i class="fa-solid fa-pause"></i> Desactivar zona
                        </button>
                    @else
                        <button type="button" class="btn btn-outline-success js-estado-btn"
                            data-form-id="form-cambiar-estado" data-name="{{ $zona->nombre }}" data-estado="activa"
                            data-action="activar">
                            <i class="fa-solid fa-play"></i> Activar zona
                        </button>
                    @endif

                    <hr>

                    <form action="{{ route('planificacion.zonas.destroy', $zona) }}" method="POST" id="form-delete">
                        @csrf @method('DELETE')
                    </form>

                    <button type="button" class="btn btn-outline-danger js-delete-btn w-100" data-form-id="form-delete"
                        data-name="{{ $zona->nombre }}">
                        <i class="fa-solid fa-trash"></i> Eliminar zona
                    </button>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-chart-simple"></i> Estadísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <span class="text-muted d-block small">Clientes en zona</span>
                            <h4 class="mb-0">0</h4>
                        </div>
                        <span class="badge bg-label-primary rounded-pill"
                            style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-users"></i>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <span class="text-muted d-block small">Comerciales asignados</span>
                            <h4 class="mb-0">0</h4>
                        </div>
                        <span class="badge bg-label-success rounded-pill"
                            style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-user-tie"></i>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <span class="text-muted d-block small">Rutas activas</span>
                            <h4 class="mb-0">0</h4>
                        </div>
                        <span class="badge bg-label-info rounded-pill"
                            style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-route"></i>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted d-block small">Paradas completadas</span>
                            <h4 class="mb-0">0</h4>
                        </div>
                        <span class="badge bg-label-warning rounded-pill"
                            style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>
                    </div>

                    <hr class="my-3">

                    <div class="alert alert-info mb-0">
                        <small>
                            <i class="fa-solid fa-circle-info"></i>
                            Las estadísticas se actualizarán automáticamente cuando se implementen los módulos de clientes,
                            comerciales y rutas.
                        </small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-circle-info"></i> Información adicional
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="small text-muted">ID de zona</dt>
                        <dd class="mb-2"><code>#{{ $zona->id }}</code></dd>

                        <dt class="small text-muted">Creado por</dt>
                        <dd class="mb-2">Sistema</dd>

                        <dt class="small text-muted">Última modificación</dt>
                        <dd class="mb-2">{{ $zona->updated_at->diffForHumans() }}</dd>

                        @if ($zona->deleted_at)
                            <dt class="small text-muted">Estado</dt>
                            <dd class="mb-0">
                                <span class="badge bg-danger">Eliminada</span>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Form oculto para cambiar estado -->
    <form id="form-cambiar-estado" method="POST" action="{{ route('planificacion.zonas.update', $zona) }}"
        style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="nombre" value="{{ $zona->nombre }}">
        <input type="hidden" name="estado" id="nuevo-estado">
        <input type="hidden" name="prioridad" value="{{ $zona->prioridad }}">
        @if ($zona->ciudades)
            <input type="hidden" name="ciudades" value="{{ implode(',', $zona->ciudades) }}">
        @endif
    </form>
@endsection

@section('modals')
    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                        Confirmar eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        ¿Está seguro de eliminar la zona
                        <strong id="delete-name"></strong>?
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">
                        <i class="fa-solid fa-trash"></i> Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para cambiar estado -->
    <div class="modal fade" id="estadoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-circle-exclamation text-warning"></i>
                        Confirmar cambio de estado
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        ¿Está seguro de <strong id="estado-action"></strong> la zona
                        <strong id="estado-name"></strong>?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="confirm-estado">
                        Sí, continuar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal de eliminación
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            let targetDeleteForm = null;

            document.querySelectorAll('.js-delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const formId = this.dataset.formId;
                    const name = this.dataset.name;

                    targetDeleteForm = document.getElementById(formId);
                    document.getElementById('delete-name').textContent = name;
                    deleteModal.show();
                });
            });

            document.getElementById('confirm-delete').addEventListener('click', function() {
                if (targetDeleteForm) {
                    targetDeleteForm.submit();
                }
            });

            // Modal de cambio de estado
            const estadoModal = new bootstrap.Modal(document.getElementById('estadoModal'));
            let targetEstadoForm = null;
            let nuevoEstado = null;

            document.querySelectorAll('.js-estado-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const formId = this.dataset.formId;
                    const name = this.dataset.name;
                    const action = this.dataset.action;
                    nuevoEstado = this.dataset.estado;

                    targetEstadoForm = document.getElementById(formId);
                    document.getElementById('estado-name').textContent = name;
                    document.getElementById('estado-action').textContent = action;
                    estadoModal.show();
                });
            });

            document.getElementById('confirm-estado').addEventListener('click', function() {
                if (targetEstadoForm && nuevoEstado) {
                    document.getElementById('nuevo-estado').value = nuevoEstado;
                    targetEstadoForm.submit();
                }
            });
        });
    </script>
@endpush
