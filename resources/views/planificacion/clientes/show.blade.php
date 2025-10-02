@extends('layouts/contentNavbarLayout')
@section('title', 'Clientes - Detalle')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">{{ $cliente->razon_social }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('planificacion.clientes.edit', $cliente) }}" class="btn btn-primary">Editar</a>
            <a href="{{ route('planificacion.clientes.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Documento</dt>
                <dd class="col-sm-9">{{ $cliente->documento }}</dd>
                <dt class="col-sm-3">Nombre fantasía</dt>
                <dd class="col-sm-9">{{ $cliente->nombre_fantasia ?? '-' }}</dd>
                <dt class="col-sm-3">Ciudad</dt>
                <dd class="col-sm-9">{{ $cliente->ciudad ?? '-' }}</dd>
                <dt class="col-sm-3">Dirección</dt>
                <dd class="col-sm-9">{{ $cliente->direccion ?? '-' }}</dd>
                <dt class="col-sm-3">Frecuencia</dt>
                <dd class="col-sm-9">{{ ucfirst($cliente->frecuencia_visita) }}</dd>
                <dt class="col-sm-3">Ventana</dt>
                <dd class="col-sm-9">{{ $cliente->ventana_horaria ?? '-' }}</dd>
                <dt class="col-sm-3">Coordenadas</dt>
                <dd class="col-sm-9">{{ $cliente->lat }}, {{ $cliente->lng }}</dd>
                <dt class="col-sm-3">Prioridad</dt>
                <dd class="col-sm-9">{{ $cliente->prioridad }}</dd>
                <dt class="col-sm-3">Comercial</dt>
                <dd class="col-sm-9">{{ $cliente->comercial->name ?? '-' }}</dd>
                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    <span class="badge bg-{{ $cliente->estado === 'activo' ? 'success' : 'secondary' }}">
                        {{ ucfirst($cliente->estado) }}
                    </span>
                </dd>
                <dt class="col-sm-3">Notas</dt>
                <dd class="col-sm-9">{{ $cliente->notas ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    <form action="{{ route('planificacion.clientes.destroy', $cliente) }}" method="POST"
        onsubmit="return confirm('¿Eliminar cliente?');">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">Eliminar</button>
    </form>
@endsection
