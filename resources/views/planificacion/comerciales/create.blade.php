@extends('layouts/contentNavbarLayout')
@section('title', 'Comerciales - Crear')

@section('content')
    <h4 class="mb-4">Nuevo comercial</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('planificacion.comerciales.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre completo *</label>
                <input name="name" class="form-control" required value="{{ old('name') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Documento *</label>
                <input name="documento" class="form-control" required value="{{ old('documento') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Teléfono</label>
                <input name="telefono" class="form-control" value="{{ old('telefono') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Capacidad paradas/día *</label>
                <input type="number" name="capacidad_paradas_dia" min="1" max="50" class="form-control"
                    required value="{{ old('capacidad_paradas_dia', 10) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Estado *</label>
                <select name="estado" class="form-select" required>
                    @foreach (['activo' => 'Activo', 'inactivo' => 'Inactivo'] as $k => $v)
                        <option value="{{ $k }}" @selected(old('estado', 'activo') === $k)>
                            {{ $v }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Zona (opcional)</label>
                <select name="zona_id" class="form-select">
                    <option value="">Sin zona asignada</option>
                    @foreach ($zonas as $zona)
                        <option value="{{ $zona->id }}" @selected(old('zona_id') == $zona->id)>
                            {{ $zona->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Notas</label>
                <textarea name="notas" rows="3" class="form-control">{{ old('notas') }}</textarea>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('planificacion.comerciales.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
@endsection
