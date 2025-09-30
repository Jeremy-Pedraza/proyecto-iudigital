@extends('layouts/contentNavbarLayout')
@section('title', 'Listas rápidas - Nuevo')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Nuevo registro</h4>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.listas-rapidas.store') }}">
        @csrf
        @php($editing = isset($item))
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Grupo</label>
                <input type="text" name="grupo" class="form-control" value="{{ old('grupo', $item->grupo ?? '') }}"
                    required>
                @error('grupo')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Clave</label>
                <input type="text" name="clave" class="form-control" value="{{ old('clave', $item->clave ?? '') }}"
                    required>
                @error('clave')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Valor</label>
                <input type="text" name="valor" class="form-control" value="{{ old('valor', $item->valor ?? '') }}"
                    required>
                @error('valor')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Descripción</label>
                <input type="text" name="descripcion" class="form-control"
                    value="{{ old('descripcion', $item->descripcion ?? '') }}">
                @error('descripcion')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select" required>
                    <option value="1" @selected(old('estado', $item->estado ?? 1) == 1)>Activo</option>
                    <option value="0" @selected(old('estado', $item->estado ?? 1) == 0)>Inactivo</option>
                </select>
                @error('estado')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">
                <i class="fa-regular fa-floppy-disk me-1"></i> {{ $editing ? 'Actualizar' : 'Guardar' }}
            </button>
            <a href="{{ route('admin.listas-rapidas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
