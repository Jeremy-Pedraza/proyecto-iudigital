@extends('layouts/contentNavbarLayout')
@section('title', 'Zonas - Editar')

@section('content')
    <h4 class="mb-4">Editar zona</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('planificacion.zonas.update', $zona) }}">
        @method('PUT')
        @csrf

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Información básica</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre *</label>
                        <input name="nombre" class="form-control" required value="{{ old('nombre', $zona->nombre) }}"
                            placeholder="Ej: Zona Centro">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Código</label>
                        <input name="codigo" class="form-control" value="{{ old('codigo', $zona->codigo) }}"
                            placeholder="Ej: ZC01" maxlength="20">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Color *</label>
                        <input type="color" name="color" class="form-control form-control-color"
                            value="{{ old('color', $zona->color) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" rows="2" class="form-control" placeholder="Descripción de la zona...">{{ old('descripcion', $zona->descripcion) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ciudades</label>
                        <input name="ciudades" class="form-control"
                            value="{{ old('ciudades', is_array($zona->ciudades) ? implode(', ', $zona->ciudades) : '') }}"
                            placeholder="Montevideo, Canelones, Maldonado">
                        <small class="text-muted">Separar con comas</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado *</label>
                        <select name="estado" class="form-select">
                            <option value="activa" @selected(old('estado', $zona->estado) === 'activa')>Activa</option>
                            <option value="inactiva" @selected(old('estado', $zona->estado) === 'inactiva')>Inactiva</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Prioridad *</label>
                        <input type="number" name="prioridad" min="1" max="5" class="form-control"
                            value="{{ old('prioridad', $zona->prioridad) }}">
                        <small class="text-muted">1 = Alta, 5 = Baja</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Delimitación geográfica</h5>
            </div>
            <div class="card-body">
                @php
                    $tipoActual = '';
                    if ($zona->poligono && count($zona->poligono) > 0) {
                        $tipoActual = 'poligono';
                    } elseif ($zona->centro_lat && $zona->radio_km) {
                        $tipoActual = 'radio';
                    }
                @endphp

                <div class="mb-3">
                    <label class="form-label">Tipo de delimitación</label>
                    <select id="tipo-delimitacion" class="form-select">
                        <option value="">-- Seleccionar --</option>
                        <option value="radio" @selected($tipoActual === 'radio')>Radio desde centro</option>
                        <option value="poligono" @selected($tipoActual === 'poligono')>Polígono personalizado</option>
                    </select>
                </div>

                <div id="campos-radio" class="{{ $tipoActual === 'radio' ? '' : 'd-none' }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Latitud centro</label>
                            <input type="number" name="centro_lat" step="0.0000001" class="form-control"
                                value="{{ old('centro_lat', $zona->centro_lat) }}" placeholder="-34.9011">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Longitud centro</label>
                            <input type="number" name="centro_lng" step="0.0000001" class="form-control"
                                value="{{ old('centro_lng', $zona->centro_lng) }}" placeholder="-56.1645">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Radio (km)</label>
                            <input type="number" name="radio_km" min="1" class="form-control"
                                value="{{ old('radio_km', $zona->radio_km) }}" placeholder="50">
                        </div>
                    </div>
                </div>

                <div id="campos-poligono" class="{{ $tipoActual === 'poligono' ? '' : 'd-none' }}">
                    <label class="form-label">Coordenadas del polígono (JSON)</label>
                    <textarea name="poligono" rows="4" class="form-control font-monospace"
                        placeholder='[{"lat": -34.9, "lng": -56.1}, {"lat": -34.8, "lng": -56.2}, {"lat": -34.9, "lng": -56.3}]'>{{ old('poligono', $zona->poligono ? json_encode($zona->poligono) : '') }}</textarea>
                    <small class="text-muted">Array JSON de puntos con lat/lng (mínimo 3 puntos)</small>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <label class="form-label">Notas</label>
                <textarea name="notas" rows="3" class="form-control">{{ old('notas', $zona->notas) }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('planificacion.zonas.show', $zona) }}" class="btn btn-secondary">Volver</a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo-delimitacion');
            const camposRadio = document.getElementById('campos-radio');
            const camposPoligono = document.getElementById('campos-poligono');

            tipoSelect.addEventListener('change', function() {
                camposRadio.classList.add('d-none');
                camposPoligono.classList.add('d-none');

                if (this.value === 'radio') {
                    camposRadio.classList.remove('d-none');
                } else if (this.value === 'poligono') {
                    camposPoligono.classList.remove('d-none');
                }
            });
        });
    </script>
@endpush
