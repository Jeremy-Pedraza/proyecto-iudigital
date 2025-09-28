@extends('layouts/contentNavbarLayout')
@section('title', 'Productos - Crear')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Nuevo producto</h4>
        <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left"></i>
            Volver</a>
    </div>

    @if ($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.productos.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku') }}"
                        class="form-control @error('sku') is-invalid @enderror">
                    @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Precio <span class="text-danger">*</span></label>
                    <input type="number" name="price" step="0.01" min="0" value="{{ old('price', 0) }}"
                        class="form-control @error('price') is-invalid @enderror">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Costo</label>
                    <input type="number" name="cost" step="0.01" min="0" value="{{ old('cost') }}"
                        class="form-control @error('cost') is-invalid @enderror">
                    @error('cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Stock <span class="text-danger">*</span></label>
                    <input type="number" name="stock" min="0" value="{{ old('stock', 0) }}"
                        class="form-control @error('stock') is-invalid @enderror">
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Activo</label>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary"><i class="ti ti-device-floppy"></i> Guardar</button>
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
