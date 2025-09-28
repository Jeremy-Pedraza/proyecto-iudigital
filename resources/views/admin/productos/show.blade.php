@extends('layouts/contentNavbarLayout')
@section('title', 'Productos - Detalle')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Detalle de producto</h4>
        <div>
            <a href="{{ route('admin.productos.edit', $producto) }}" class="btn btn-primary me-2"><i class="ti ti-edit"></i>
                Editar</a>
            <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left"></i>
                Volver</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Nombre</dt>
                        <dd class="col-sm-9">{{ $producto->name }}</dd>

                        <dt class="col-sm-3">SKU</dt>
                        <dd class="col-sm-9"><code>{{ $producto->sku }}</code></dd>

                        <dt class="col-sm-3">Precio</dt>
                        <dd class="col-sm-9">${{ number_format($producto->price, 2) }}</dd>

                        <dt class="col-sm-3">Costo</dt>
                        <dd class="col-sm-9">{{ $producto->cost !== null ? '$' . number_format($producto->cost, 2) : '—' }}
                        </dd>

                        <dt class="col-sm-3">Stock</dt>
                        <dd class="col-sm-9">{{ $producto->stock }}</dd>

                        <dt class="col-sm-3">Estado</dt>
                        <dd class="col-sm-9">
                            @if ($producto->is_active)
                                <span class="badge bg-label-success">Activo</span>
                            @else
                                <span class="badge bg-label-secondary">Inactivo</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Descripción</dt>
                        <dd class="col-sm-9">{{ $producto->description ?? '—' }}</dd>

                        <dt class="col-sm-3">Creado</dt>
                        <dd class="col-sm-9">{{ optional($producto->created_at)->format('Y-m-d H:i') }}</dd>

                        <dt class="col-sm-3">Actualizado</dt>
                        <dd class="col-sm-9">{{ optional($producto->updated_at)->format('Y-m-d H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.productos.edit', $producto) }}" class="btn btn-primary"><i
                            class="ti ti-edit"></i> Editar</a>
                    <form action="{{ route('admin.productos.destroy', $producto) }}" method="POST"
                        onsubmit="return confirm('¿Eliminar este producto?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="ti ti-trash"></i> Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
