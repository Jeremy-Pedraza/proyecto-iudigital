@extends('layouts/contentNavbarLayout')

@section('title', 'Rutas - Detalle')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">{{ $ruta->nombre }}</h4>
        <div class="d-flex gap-2">
            @if($ruta->esEditable())
                <a href="{{ route('planificacion.rutas.editor', $ruta) }}" class="btn btn-primary">
                    <i class="fa-solid fa-pen-to-square"></i> Editar
                </a>
            @endif
            @if($ruta->estado === 'planificada' && !$ruta->estaPublicada())
                <form action="{{ route('planificacion.rutas.publicar', $ruta) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-paper-plane"></i> Publicar
                    </button>
                </form>
            @endif
            <a href="{{ route('planificacion.rutas.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Información General -->
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">ID</dt>
                        <dd class="col-sm-9">#{{ $ruta->id }}</dd>

                        <dt class="col-sm-3">Fecha</dt>
                        <dd class="col-sm-9">{{ $ruta->fecha->format('d/m/Y') }}</dd>

                        <dt class="col-sm-3">Comercial</dt>
                        <dd class="col-sm-9">{{ $ruta->comercial->name ?? '—' }}</dd>

                        <dt class="col-sm-3">Estado</dt>
                        <dd class="col-sm-9">
                            @php
                                $badgeClass = match($ruta->estado) {
                                    'borrador' => 'secondary',
                                    'planificada' => 'info',
                                    'publicada' => 'primary',
                                    'en_curso' => 'warning',
                                    'completada' => 'success',
                                    'cancelada' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">
                                {{ ucfirst($ruta->estado) }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Horario</dt>
                        <dd class="col-sm-9">
                            {{ $ruta->hora_inicio ?? '—' }} - {{ $ruta->hora_fin ?? '—' }}
                        </dd>

                        <dt class="col-sm-3">Publicada</dt>
                        <dd class="col-sm-9">
                            @if($ruta->estaPublicada())
                                <i class="fa-solid fa-check text-success"></i>
                                {{ $ruta->publicada_at->format('d/m/Y H:i') }}
                            @else
                                <i class="fa-solid fa-times text-danger"></i> No publicada
                            @endif
                        </dd>

                        <dt class="col-sm-3">Notas</dt>
                        <dd class="col-sm-9">{{ $ruta->notas ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Paradas -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Paradas ({{ $ruta->paradas->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    @if($ruta->paradas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Dirección</th>
                                        <th>Hora estimada</th>
                                        <th>Distancia</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ruta->paradas as $parada)
                                        <tr>
                                            <td>{{ $parada->orden }}</td>
                                            <td>
                                                <strong>{{ $parada->cliente->razon_social ?? 'Sin cliente' }}</strong>
                                            </td>
                                            <td>{{ $parada->cliente->direccion ?? '—' }}</td>
                                            <td>{{ $parada->hora_estimada_llegada ?? '—' }}</td>
                                            <td>{{ number_format($parada->distancia_desde_anterior_km ?? 0, 1) }} km</td>
                                            <td>
                                                @php
                                                    $estadoBadge = match($parada->estado ?? 'pendiente') {
                                                        'pendiente' => 'secondary',
                                                        'en_curso' => 'warning',
                                                        'completada' => 'success',
                                                        'no_atendida' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-label-{{ $estadoBadge }}">
                                                    {{ ucfirst($parada->estado ?? 'Pendiente') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-map-location-dot fa-3x mb-3"></i>
                            <p class="mb-0">Esta ruta no tiene paradas asignadas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Total de paradas</small>
                        <h3 class="mb-0">{{ $ruta->paradas->count() }}</h3>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Distancia total</small>
                        <h3 class="mb-0">{{ number_format($ruta->distancia_total_km ?? 0, 1) }} km</h3>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Tiempo estimado</small>
                        <h3 class="mb-0">
                            {{ floor(($ruta->tiempo_estimado_min ?? 0) / 60) }}h
                            {{ ($ruta->tiempo_estimado_min ?? 0) % 60 }}min
                        </h3>
                    </div>
                </div>
            </div>

            @if($ruta->estado === 'completada')
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Resumen de ejecución</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $completadas = $ruta->paradas->where('estado', 'completada')->count();
                            $noAtendidas = $ruta->paradas->where('estado', 'no_atendida')->count();
                            $total = $ruta->paradas->count();
                            $porcentaje = $total > 0 ? round(($completadas / $total) * 100, 1) : 0;
                        @endphp
                        <div class="mb-3">
                            <small class="text-muted d-block">Paradas completadas</small>
                            <h4 class="mb-0 text-success">{{ $completadas }} / {{ $total }}</h4>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">No atendidas</small>
                            <h4 class="mb-0 text-danger">{{ $noAtendidas }}</h4>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Cumplimiento</small>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $porcentaje }}%">
                                    {{ $porcentaje }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Botón eliminar al final -->
    @if($ruta->estado !== 'completada')
        <div class="mt-3">
            <form action="{{ route('planificacion.rutas.destroy', $ruta) }}" method="POST"
                onsubmit="return confirm('¿Eliminar esta ruta? Esta acción no se puede deshacer.');">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">
                    <i class="fa-solid fa-trash"></i> Eliminar ruta
                </button>
            </form>
        </div>
    @endif
@endsection
