@extends('layouts/contentNavbarLayout')
@section('title', 'Reglas de Negocio')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Reglas de Negocio</h4>
            <p class="text-muted mb-0">
                Configuración de parámetros para la generación y optimización de rutas
            </p>
        </div>
        <a href="{{ route('planificacion.clientes.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <strong>Error:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @foreach ($categoriasOrden as $categoriaKey => $categoriaNombre)
        @if (isset($reglasAgrupadas[$categoriaKey]) && $reglasAgrupadas[$categoriaKey]->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-{{ getCategoriaIcon($categoriaKey) }} me-2"></i>
                        {{ $categoriaNombre }}
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        @foreach ($reglasAgrupadas[$categoriaKey] as $regla)
                            <div class="col-md-6">
                                <div
                                    class="border rounded p-3 h-100 
                                    {{ !$regla->activa ? 'bg-light' : '' }}
                                    {{ !$regla->editable ? 'border-warning' : '' }}">

                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <label class="form-label fw-bold mb-1">
                                                {{ $regla->nombre }}
                                                @if (!$regla->editable)
                                                    <span class="badge bg-label-warning ms-2"
                                                        title="Esta regla no puede ser modificada">
                                                        <i class="fa-solid fa-lock"></i> Bloqueada
                                                    </span>
                                                @endif
                                            </label>
                                            <p class="text-muted small mb-2">{{ $regla->descripcion }}</p>
                                        </div>

                                        <div class="form-check form-switch ms-3">
                                            <input class="form-check-input" type="checkbox" id="activa-{{ $regla->id }}"
                                                {{ $regla->activa ? 'checked' : '' }}
                                                {{ !$regla->editable ? 'disabled' : '' }}
                                                onchange="toggleRegla({{ $regla->id }}, this.checked)"
                                                title="{{ $regla->activa ? 'Desactivar' : 'Activar' }} regla">
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('planificacion.reglas.update', $regla) }}"
                                        class="regla-form" data-regla-id="{{ $regla->id }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="input-group">
                                            @if ($regla->tipo === 'booleano')
                                                <select name="valor" class="form-select"
                                                    {{ !$regla->editable ? 'disabled' : '' }}
                                                    onchange="this.form.submit()">
                                                    <option value="true"
                                                        {{ $regla->valor === 'true' ? 'selected' : '' }}>
                                                        Sí / Habilitado
                                                    </option>
                                                    <option value="false"
                                                        {{ $regla->valor === 'false' ? 'selected' : '' }}>
                                                        No / Deshabilitado
                                                    </option>
                                                </select>
                                            @elseif ($regla->tipo === 'numero' || $regla->tipo === 'tiempo')
                                                <input type="number" name="valor" class="form-control"
                                                    value="{{ $regla->valor }}"
                                                    @if ($regla->valor_minimo !== null) min="{{ $regla->valor_minimo }}" @endif
                                                    @if ($regla->valor_maximo !== null) max="{{ $regla->valor_maximo }}" @endif
                                                    step="{{ $regla->tipo === 'numero' ? '0.01' : '1' }}"
                                                    {{ !$regla->editable ? 'disabled' : '' }} required>

                                                @if ($regla->unidad)
                                                    <span class="input-group-text">{{ $regla->unidad }}</span>
                                                @endif

                                                <button type="submit" class="btn btn-primary"
                                                    {{ !$regla->editable ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-floppy-disk"></i>
                                                </button>
                                            @else
                                                <input type="text" name="valor" class="form-control"
                                                    value="{{ $regla->valor }}" {{ !$regla->editable ? 'disabled' : '' }}
                                                    required>

                                                @if ($regla->unidad)
                                                    <span class="input-group-text">{{ $regla->unidad }}</span>
                                                @endif

                                                <button type="submit" class="btn btn-primary"
                                                    {{ !$regla->editable ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-floppy-disk"></i>
                                                </button>
                                            @endif
                                        </div>

                                        @if ($regla->valor_minimo !== null || $regla->valor_maximo !== null)
                                            <small class="text-muted d-block mt-1">
                                                <i class="fa-solid fa-info-circle"></i>
                                                Rango permitido:
                                                @if ($regla->valor_minimo !== null)
                                                    {{ $regla->valor_minimo }}
                                                @endif
                                                -
                                                @if ($regla->valor_maximo !== null)
                                                    {{ $regla->valor_maximo }}
                                                @endif
                                                @if ($regla->unidad)
                                                    {{ $regla->unidad }}
                                                @endif
                                            </small>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if ($reglasAgrupadas->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No hay reglas configuradas en el sistema.</p>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function toggleRegla(reglaId, activa) {
            const form = document.querySelector(`form[data-regla-id="${reglaId}"]`);
            if (!form) return;

            // Crear un input hidden temporal para el estado
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'activa';
            input.value = activa ? '1' : '0';
            form.appendChild(input);

            // Enviar el formulario
            form.submit();
        }

        // Confirmación antes de cambiar valores críticos
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.regla-form');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const reglaName = form.querySelector('label')?.textContent.trim() ||
                        'esta regla';
                    const valor = form.querySelector('[name="valor"]')?.value;

                    // Solo confirmar en reglas críticas (opcional)
                    // if (!confirm(`¿Confirmas actualizar "${reglaName}" al valor "${valor}"?`)) {
                    //     e.preventDefault();
                    // }
                });
            });
        });
    </script>
@endpush

@php
    // Helper function para iconos por categoría
    function getCategoriaIcon($categoria)
    {
        return match ($categoria) {
            'capacidad' => 'boxes-stacked',
            'tiempo' => 'clock',
            'distancia' => 'road',
            'optimizacion' => 'diagram-project',
            'restricciones' => 'shield-halved',
            'penalizaciones' => 'exclamation-triangle',
            'general' => 'gear',
            default => 'circle-dot',
        };
    }
@endphp
