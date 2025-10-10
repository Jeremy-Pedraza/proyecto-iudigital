@extends('layouts/contentNavbarLayout')

@section('title', 'Rutas - Editor')

@section('vendor-style')
<style>
.parada-item {
    cursor: move;
    transition: all 0.2s;
}
.parada-item:hover {
    background-color: #f8f9fa;
}
.parada-item.dragging {
    opacity: 0.5;
}
.drop-zone {
    min-height: 60px;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    transition: all 0.2s;
}
.drop-zone.drag-over {
    border-color: #696cff;
    background-color: #f8f9ff;
}
</style>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-0">Editar Ruta: {{ $ruta->nombre }}</h4>
        <small class="text-muted">{{ $ruta->fecha->format('d/m/Y') }} - {{ $ruta->comercial->name ?? 'Sin comercial' }}</small>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" id="btnGuardarOrden">
            <i class="fa-solid fa-save"></i> Guardar cambios
        </button>
        <a href="{{ route('planificacion.rutas.show', $ruta) }}" class="btn btn-secondary">
            <i class="fa-solid fa-times"></i> Cancelar
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Panel de edición de paradas -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Paradas ({{ $ruta->paradas->count() }})</h5>
                <div class="text-muted">
                    <small>Arrastra las paradas para reordenarlas</small>
                </div>
            </div>
            <div class="card-body">
                <div id="paradasContainer" class="drop-zone p-3">
                    @if($ruta->paradas->count() > 0)
                        @foreach($ruta->paradas as $parada)
                            <div class="parada-item card mb-2"
                                 draggable="true"
                                 data-parada-id="{{ $parada->id }}">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fa-solid fa-grip-vertical text-muted"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <span class="badge bg-label-primary me-2">
                                                            #<span class="parada-orden">{{ $parada->orden }}</span>
                                                        </span>
                                                        {{ $parada->cliente->razon_social ?? 'Sin cliente' }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="fa-solid fa-location-dot"></i>
                                                        {{ $parada->cliente->direccion ?? 'Sin dirección' }},
                                                        {{ $parada->cliente->ciudad ?? '' }}
                                                    </small>
                                                </div>
                                                <div class="text-end">
                                                    <small class="d-block text-muted">
                                                        <i class="fa-solid fa-route"></i>
                                                        {{ number_format($parada->distancia_desde_anterior_km ?? 0, 1) }} km
                                                    </small>
                                                    <small class="d-block text-muted">
                                                        <i class="fa-solid fa-clock"></i>
                                                        {{ $parada->tiempo_desde_anterior_min ?? 0 }} min
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-map-location-dot fa-3x mb-3"></i>
                            <p class="mb-0">Esta ruta no tiene paradas asignadas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de estadísticas -->
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Información General</h5>
            </div>
            <div class="card-body">
                <form id="formActualizarRuta" method="POST" action="{{ route('planificacion.rutas.update', $ruta) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nombre de la ruta</label>
                        <input type="text" name="nombre" class="form-control"
                               value="{{ old('nombre', $ruta->nombre) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control"
                               value="{{ old('fecha', $ruta->fecha->format('Y-m-d')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            @foreach(['borrador' => 'Borrador', 'planificada' => 'Planificada'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('estado', $ruta->estado) === $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Hora inicio</label>
                            <input type="time" name="hora_inicio" class="form-control"
                                   value="{{ old('hora_inicio', $ruta->hora_inicio) }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Hora fin</label>
                            <input type="time" name="hora_fin" class="form-control"
                                   value="{{ old('hora_fin', $ruta->hora_fin) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea name="notas" class="form-control" rows="3">{{ old('notas', $ruta->notas) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-save"></i> Actualizar información
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Totales calculados</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Total de paradas</small>
                    <h3 class="mb-0" id="totalParadas">{{ $ruta->paradas->count() }}</h3>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Distancia total</small>
                    <h3 class="mb-0">
                        <span id="distanciaTotal">{{ number_format($ruta->distancia_total_km ?? 0, 1) }}</span> km
                    </h3>
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block">Tiempo estimado</small>
                    <h3 class="mb-0">
                        <span id="tiempoTotal">{{ $ruta->tiempo_estimado_min ?? 0 }}</span> min
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let draggedElement = null;
    const container = document.getElementById('paradasContainer');
    const btnGuardar = document.getElementById('btnGuardarOrden');

    // Eventos de drag & drop
    document.querySelectorAll('.parada-item').forEach(item => {
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragover', handleDragOver);
        item.addEventListener('drop', handleDrop);
        item.addEventListener('dragend', handleDragEnd);
    });

    function handleDragStart(e) {
        draggedElement = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';

        const afterElement = getDragAfterElement(container, e.clientY);
        if (afterElement == null) {
            container.appendChild(draggedElement);
        } else {
            container.insertBefore(draggedElement, afterElement);
        }

        return false;
    }

    function handleDrop(e) {
        e.stopPropagation();
        actualizarNumeracion();
        return false;
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
        draggedElement = null;
    }

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.parada-item:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    function actualizarNumeracion() {
        document.querySelectorAll('.parada-item').forEach((item, index) => {
            item.querySelector('.parada-orden').textContent = index + 1;
        });
    }

    // Guardar el nuevo orden
    btnGuardar.addEventListener('click', function() {
        const orden = [];
        document.querySelectorAll('.parada-item').forEach(item => {
            orden.push(item.dataset.paradaId);
        });

        // Enviar al servidor
        fetch('{{ route("planificacion.rutas.paradas.reorden", $ruta) }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ orden: orden })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar totales
                if (data.data) {
                    document.getElementById('distanciaTotal').textContent =
                        parseFloat(data.data.distancia_total_km).toFixed(1);
                    document.getElementById('tiempoTotal').textContent =
                        data.data.tiempo_estimado_min;
                }

                // Mostrar mensaje de éxito
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible';
                alert.innerHTML = `
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.content-wrapper').insertBefore(
                    alert,
                    document.querySelector('.content-wrapper').firstChild
                );

                // Auto-cerrar después de 3 segundos
                setTimeout(() => alert.remove(), 3000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar el orden de las paradas');
        });
    });
});
</script>
@endpush
