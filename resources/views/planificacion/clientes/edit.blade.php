@extends('layouts/contentNavbarLayout')
@section('title', 'Clientes - Editar')

@section('content')
    <h4 class="mb-4">Editar cliente</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('clientes.update', $cliente) }}">
        @method('PUT')
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Razón social *</label>
                <input name="razon_social" class="form-control" required
                    value="{{ old('razon_social', $cliente->razon_social ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Nombre de fantasía</label>
                <input name="nombre_fantasia" class="form-control"
                    value="{{ old('nombre_fantasia', $cliente->nombre_fantasia ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Documento *</label>
                <input name="documento" class="form-control" required
                    value="{{ old('documento', $cliente->documento ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $cliente->email ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Teléfono</label>
                <input name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Dirección</label>
                <input name="direccion" class="form-control" value="{{ old('direccion', $cliente->direccion ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Ciudad</label>
                <input name="ciudad" class="form-control" value="{{ old('ciudad', $cliente->ciudad ?? '') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <div class="flex-fill">
                    <label class="form-label">Lat</label>
                    <input name="lat" class="form-control" value="{{ old('lat', $cliente->lat ?? '') }}">
                </div>
                <div class="flex-fill">
                    <label class="form-label">Lng</label>
                    <input name="lng" class="form-control" value="{{ old('lng', $cliente->lng ?? '') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Frecuencia *</label>
                <select name="frecuencia_visita" class="form-select" required>
                    @foreach (['diaria', 'semanal', 'quincenal', 'mensual'] as $f)
                        <option value="{{ $f }}" @selected(old('frecuencia_visita', $cliente->frecuencia_visita ?? 'semanal') === $f)>
                            {{ ucfirst($f) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Ventana horaria</label>
                <input name="ventana_horaria" class="form-control" placeholder="09:00-12:00"
                    value="{{ old('ventana_horaria', $cliente->ventana_horaria ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Prioridad *</label>
                <input type="number" name="prioridad" min="1" max="5" class="form-control"
                    value="{{ old('prioridad', $cliente->prioridad ?? 3) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado *</label>
                <select name="estado" class="form-select">
                    @foreach (['activo' => 'Activo', 'inactivo' => 'Inactivo'] as $k => $v)
                        <option value="{{ $k }}" @selected(old('estado', $cliente->estado ?? 'activo') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Notas</label>
                <textarea name="notas" rows="3" class="form-control">{{ old('notas', $cliente->notas ?? '') }}</textarea>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">Actualizar</button>
            <a href="{{ route('planificacion.clientes.show', $cliente) }}" class="btn btn-secondary">Volver</a>
        </div>
    </form>
@endsection
