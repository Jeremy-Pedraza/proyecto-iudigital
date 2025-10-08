@extends('layouts/contentNavbarLayout')

@section('title', 'Comerciales - Editar')

@section('content')
    <h4 class="mb-4">Editar comercial</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('planificacion.comerciales.update', $comercial) }}">
        @method('PUT')
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre completo *</label>
                <input name="name" class="form-control" required value="{{ old('name', $comercial->name) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required
                    value="{{ old('email', $comercial->email) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Documento *</label>
                <input name="documento" class="form-control" required value="{{ old('documento', $comercial->documento) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Teléfono</label>
                <input name="telefono" class="form-control" value="{{ old('telefono', $comercial->telefono) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Capacidad paradas/día *</label>
                <input type="number" name="capacidad_paradas_dia" min="1" max="50" class="form-control"
                    required value="{{ old('capacidad_paradas_dia', $comercial->capacidad_paradas_dia) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Estado *</label>
                <select name="estado" class="form-select" required>
                    @foreach (['activo' => 'Activo', 'inactivo' => 'Inactivo'] as $k => $v)
                        <option value="{{ $k }}" @selected(old('estado', $comercial->estado) === $k)>
                            {{ $v }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Zona (opcional)</label>
                <select name="zona_id" class="form-select">
                    <option value="">Sin zona asignada</option>
                    @foreach ($zonas as $z)
                        <option value="{{ $z->id }}" @selected(old('zona_id', $comercial->zona_id) == $z->id)>
                            {{ $z->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Notas</label>
                <textarea name="notas" rows="3" class="form-control">{{ old('notas', $comercial->notas) }}</textarea>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <a href="{{ route('planificacion.comerciales.index', $comercial) }}" class="btn btn-secondary">Volver</a>
        </div>
    </form>
@endsection
