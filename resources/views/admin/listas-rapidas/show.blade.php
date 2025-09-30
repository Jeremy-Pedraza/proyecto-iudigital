@extends('layouts/contentNavbarLayout')
@section('title', 'Listas rápidas - Detalle')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Detalle</h4>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('admin.listas-rapidas.edit', $item->id) }}">
                <i class="fa-regular fa-pen-to-square"></i> Editar
            </a>
            <a class="btn btn-outline-secondary" href="{{ route('admin.listas-rapidas.index') }}">Volver</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Grupo</dt>
                <dd class="col-sm-9">{{ $item->grupo }}</dd>
                <dt class="col-sm-3">Clave</dt>
                <dd class="col-sm-9">{{ $item->clave }}</dd>
                <dt class="col-sm-3">Valor</dt>
                <dd class="col-sm-9">{{ $item->valor }}</dd>
                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    @if ($item->estado)
                        <span class="badge bg-label-success">Activo</span>
                    @else
                        <span class="badge bg-label-secondary">Inactivo</span>
                    @endif
                </dd>
                <dt class="col-sm-3">Descripción</dt>
                <dd class="col-sm-9">{{ $item->descripcion ?? '—' }}</dd>
                <dt class="col-sm-3">Creación</dt>
                <dd class="col-sm-9">{{ $item->created_at }}</dd>
                <dt class="col-sm-3">Actualización</dt>
                <dd class="col-sm-9">{{ $item->updated_at }}</dd>
            </dl>
        </div>
    </div>
@endsection
