@extends('layouts/contentNavbarLayout')

@section('title', 'Planificar Ruta')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Planificador de Rutas</h4>
            <p class="text-muted mb-0">Configure los parámetros para generar una nueva ruta optimizada</p>
        </div>
        <a href="{{ route('planificacion.rutas.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver a rutas
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <h5 class="alert-heading mb-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Errores de validación</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('planificacion.rutas.planificar') }}" id="formPlanificar">
        @csrf

        <!-- SECCIÓN 1: Datos Básicos -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa-solid fa-circle-info me-2"></i>Datos Básicos</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre de la ruta <small class="text-muted">(opcional)</small></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre') }}" placeholder="Se generará automáticamente si se deja vacío">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Si no ingresa un nombre, se generará automáticamente
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Comercial <span class="text-danger">*</span></label>
                        <select name="comercial_id" class="form-select @error('comercial_id') is-invalid @enderror"
                            required>
                            <option value="">-- Seleccione un comercial --</option>
                            @foreach ($comerciales as $comercial)
                                <option value="{{ $comercial->id }}" @selected(old('comercial_id') == $comercial->id)>
                                    {{ $comercial->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('comercial_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Fecha inicio <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_inicio"
                            class="form-control @error('fecha_inicio') is-invalid @enderror"
                            value="{{ old('fecha_inicio', now()->addDay()->format('Y-m-d')) }}"
                            min="{{ now()->format('Y-m-d') }}" required>
                        @error('fecha_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Fecha fin <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror"
                            value="{{ old('fecha_fin', now()->addDays(7)->format('Y-m-d')) }}"
                            min="{{ now()->addDay()->format('Y-m-d') }}" required>
                        @error('fecha_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipo de período <span class="text-danger">*</span></label>
                        <select name="tipo_periodo" class="form-select @error('tipo_periodo') is-invalid @enderror"
                            required>
                            <option value="diario" @selected(old('tipo_periodo') === 'diario')>Diario</option>
                            <option value="semanal" @selected(old('tipo_periodo', 'semanal') === 'semanal')>Semanal</option>
                            <option value="mensual" @selected(old('tipo_periodo') === 'mensual')>Mensual</option>
                        </select>
                        @error('tipo_periodo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: Restricciones de Jornada -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa-solid fa-clock me-2"></i>Restricciones de Jornada</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Máximo paradas/día <span class="text-danger">*</span></label>
                        <input type="number" name="max_paradas_dia" min="1" max="50"
                            class="form-control @error('max_paradas_dia') is-invalid @enderror"
                            value="{{ old('max_paradas_dia', $defaults['max_paradas']) }}" required>
                        @error('max_paradas_dia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Hora inicio <span class="text-danger">*</span></label>
                        <input type="time" name="hora_inicio_jornada"
                            class="form-control @error('hora_inicio_jornada') is-invalid @enderror"
                            value="{{ old('hora_inicio_jornada', $defaults['hora_inicio']) }}" required>
                        @error('hora_inicio_jornada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Hora fin <span class="text-danger">*</span></label>
                        <input type="time" name="hora_fin_jornada"
                            class="form-control @error('hora_fin_jornada') is-invalid @enderror"
                            value="{{ old('hora_fin_jornada', $defaults['hora_fin']) }}" required>
                        @error('hora_fin_jornada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Pausa (minutos) <span class="text-danger">*</span></label>
                        <input type="number" name="duracion_pausa_minutos" min="0" max="180"
                            step="15" class="form-control @error('duracion_pausa_minutos') is-invalid @enderror"
                            value="{{ old('duracion_pausa_minutos', $defaults['duracion_pausa']) }}" required>
                        @error('duracion_pausa_minutos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Tiempo de almuerzo/descanso</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 3: Filtros de Clientes -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-filter me-2"></i>Filtros de Clientes</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnPreview">
                    <i class="fa-solid fa-eye me-1"></i> Vista previa
                </button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Ciudad <small class="text-muted">(opcional)</small></label>
                        <select name="ciudad" class="form-select">
                            <option value="">-- Todas las ciudades --</option>
                            @foreach ($ciudades as $ciudad)
                                <option value="{{ $ciudad }}" @selected(old('ciudad') === $ciudad)>
                                    {{ $ciudad }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Frecuencia <small class="text-muted">(opcional)</small></label>
                        <select name="frecuencia_filtro" class="form-select">
                            <option value="">-- Todas las frecuencias --</option>
                            @foreach ($frecuencias as $value => $label)
                                <option value="{{ $value }}" @selected(old('frecuencia_filtro') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Prioridad mínima <small class="text-muted">(opcional)</small></label>
                        <select name="prioridad_min" class="form-select">
                            <option value="">-- Cualquier prioridad --</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" @selected(old('prioridad_min') == $i)>
                                    {{ $i }} {{ $i === 5 ? '(máxima)' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Solo se incluirán clientes activos con coordenadas válidas que coincidan con los filtros
                            seleccionados.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 4: Opciones de Optimización -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa-solid fa-route me-2"></i>Opciones de Optimización</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Criterio de priorización <span class="text-danger">*</span></label>
                        <select name="prioridad_criterio"
                            class="form-select @error('prioridad_criterio') is-invalid @enderror" required>
                            @foreach ($criterios as $value => $label)
                                <option value="{{ $value }}" @selected(old('prioridad_criterio', 'balanceado') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('prioridad_criterio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-block mb-3">Opciones adicionales</label>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="respetar_ventanas_horarias"
                                id="checkVentanas" value="1" @checked(old('respetar_ventanas_horarias', true))>
                            <label class="form-check-label" for="checkVentanas">
                                Respetar ventanas horarias de clientes
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="balancear_carga" id="checkBalanceo"
                                value="1" @checked(old('balancear_carga', true))>
                            <label class="form-check-label" for="checkBalanceo">
                                Balancear carga entre días
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Notas adicionales <small class="text-muted">(opcional)</small></label>
                        <textarea name="notas" rows="3" class="form-control"
                            placeholder="Observaciones, consideraciones especiales, etc.">{{ old('notas') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('planificacion.rutas.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-xmark me-1"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary" id="btnGenerar">
                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Generar Ruta
            </button>
        </div>
    </form>

    <!-- Modal Vista Previa -->
    <div class="modal fade" id="modalPreview" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vista Previa - Clientes Elegibles</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="previewContent">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando clientes...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formPlanificar');
            const btnGenerar = document.getElementById('btnGenerar');
            const btnPreview = document.getElementById('btnPreview');
            const modalPreview = new bootstrap.Modal(document.getElementById('modalPreview'));

            // Prevenir doble submit
            form.addEventListener('submit', function() {
                btnGenerar.disabled = true;
                btnGenerar.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span> Generando...';
            });

            // Sincronizar fecha_fin cuando cambia fecha_inicio
            const fechaInicio = document.querySelector('[name="fecha_inicio"]');
            const fechaFin = document.querySelector('[name="fecha_fin"]');

            fechaInicio?.addEventListener('change', function() {
                const inicio = new Date(this.value);
                const finActual = new Date(fechaFin.value);

                if (finActual <= inicio) {
                    const nuevaFin = new Date(inicio);
                    nuevaFin.setDate(nuevaFin.getDate() + 7);
                    fechaFin.value = nuevaFin.toISOString().split('T')[0];
                }

                fechaFin.min = this.value;
            });

            // Vista previa de clientes
            btnPreview.addEventListener('click', async function() {
                const formData = new FormData(form);
                const params = new URLSearchParams();

                ['comercial_id', 'ciudad', 'frecuencia_filtro', 'prioridad_min'].forEach(key => {
                    const value = formData.get(key);
                    if (value) params.append(key, value);
                });

                modalPreview.show();

                try {
                    const response = await fetch(
                        `{{ route('planificacion.rutas.planificar.preview') }}?${params}`);
                    const data = await response.json();

                    let html = '';

                    if (data.success && data.total_clientes > 0) {
                        html = `
                    <div class="alert alert-success">
                        <i class="fa-solid fa-check-circle me-1"></i>
                        Se encontraron <strong>${data.total_clientes}</strong> clientes elegibles
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Ciudad</th>
                                    <th>Frecuencia</th>
                                    <th>Prioridad</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                        data.clientes.forEach(c => {
                            html += `
                        <tr>
                            <td>${c.razon_social}</td>
                            <td>${c.ciudad || '-'}</td>
                            <td><span class="badge bg-label-info">${c.frecuencia}</span></td>
                            <td>
                                ${'★'.repeat(c.prioridad)}${'☆'.repeat(5 - c.prioridad)}
                            </td>
                        </tr>
                    `;
                        });

                        html += `
                            </tbody>
                        </table>
                    </div>
                `;
                    } else {
                        html = `
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        No se encontraron clientes con los filtros seleccionados
                    </div>
                    <p class="text-muted mb-0">
                        Revise los filtros o seleccione un comercial diferente
                    </p>
                `;
                    }

                    document.getElementById('previewContent').innerHTML = html;

                } catch (error) {
                    document.getElementById('previewContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa-solid fa-exclamation-circle me-1"></i>
                    Error al cargar la vista previa
                </div>
            `;
                }
            });
        });
    </script>
@endpush
