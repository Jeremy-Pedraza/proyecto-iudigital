@extends('layouts/contentNavbarLayout')
@section('title', 'Productos - Editar')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Editar producto</h4>
        <div>
            <a href="{{ route('admin.productos.show', $producto) }}" class="btn btn-outline-secondary me-2"><i
                    class="ti ti-eye"></i> Ver</a>
            <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left"></i>
                Volver</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.productos.update', $producto) }}" method="POST" class="row g-3">
                @csrf @method('PUT')

                <div class="col-md-6">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $producto->name) }}"
                        class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku', $producto->sku) }}"
                        class="form-control @error('sku') is-invalid @enderror">
                    @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $producto->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Precio <span class="text-danger">*</span></label>
                    <input type="number" name="price" step="0.01" min="0"
                        value="{{ old('price', $producto->price) }}"
                        class="form-control @error('price') is-invalid @enderror">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Costo</label>
                    <input type="number" name="cost" step="0.01" min="0"
                        value="{{ old('cost', $producto->cost) }}" class="form-control @error('cost') is-invalid @enderror">
                    @error('cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Stock <span class="text-danger">*</span></label>
                    <input type="number" name="stock" min="0" value="{{ old('stock', $producto->stock) }}"
                        class="form-control @error('stock') is-invalid @enderror">
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $producto->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Activo</label>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary"><i class="ti ti-device-floppy"></i> Guardar cambios</button>
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <form action="{{ route('admin.productos.destroy', $producto) }}" method="POST"
                        class="d-inline float-end" onsubmit="return confirm('¿Eliminar este producto?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="ti ti-trash"></i> Eliminar</button>
                    </form>
                </div>
            </form>
        </div>
    </div>
@endsection
