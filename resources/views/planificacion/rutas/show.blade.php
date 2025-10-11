@extends('layouts/contentNavbarLayout')

@section('title', 'Rutas - Detalle')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">{{ $ruta->nombre }}</h4>
            <small class="text-muted">ID: #{{ $ruta->id }}</small>
        </div>
        <div class="d-flex gap-2">
            @if(in_array($ruta->estado, ['borrador', 'calculada']))
                <a href="{{ route('planificacion.rutas.editor', $ruta) }}" class="btn btn-primary">
                    <i class="fa-solid fa-pen-to-square"></i> Editar
                </a>
            @endif
            @if($ruta->estado === 'calculada')
                <form action="{{ route('planificacion.rutas.publicar', $ruta) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success"
                            onclick="return confirm('¿Publicar esta ruta? Estará disponible en móvil.')">
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
                        <dt class="col-sm-4">Período</dt>
                        <dd class="col-sm-8">
                            {{ $ruta->fecha_inicio->format('d/m/Y') }} - {{ $ruta->fecha_fin->format('d/m/Y') }}
                            <span class="badge bg-label-secondary ms-2">{{ ucfirst($ruta->tipo_periodo) }}</span>
                        </dd>

                        <dt class="col-sm-4">Comercial asignado</dt>
                        <dd class="col-sm-8">{{ $ruta->comercial->name ?? '—' }}</dd>

                        <dt class="col-sm-4">Estado</dt>
                        <dd class="col-sm-8">
                            @php
                                $badgeClass = match($ruta->estado) {
                                    'borrador' => 'secondary',
                                    'calculada' => 'info',
                                    'publicada' => 'primary',
                                    'en_ejecucion' => 'warning',
                                    'completada' => 'success',
                                    'cancelada' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $ruta->estado)) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Horario de jornada</dt>
                        <dd class="col-sm-8">
                            {{ \Carbon\Carbon::parse($ruta->hora_inicio_jornada)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($ruta->hora_fin_jornada)->format('H:i') }}
                        </dd>

                        <dt class="col-sm-4">Pausa</dt>
                        <dd class="col-sm-8">{{ $ruta->duracion_pausa_minutos }} minutos</dd>

                        <dt class="col-sm-4">Criterio de priorización</dt>
                        <dd class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $ruta->prioridad_criterio)) }}</dd>

                        @if($ruta->fecha_publicacion)
                            <dt class="col-sm-4">Publicada</dt>
                            <dd class="col-sm-8">
                                {{ $ruta->fecha_publicacion->format('d/m/Y H:i') }}
                                @if($ruta->publicadoPor)
                                    por {{ $ruta->publicadoPor->name }}
                                @endif
                            </dd>
                        @endif

                        @if($ruta->notas)
                            <dt class="col-sm-4">Notas</dt>
                            <dd class="col-sm-8">{{ $ruta->notas }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Paradas -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Paradas ({{ $ruta->paradas->count() }})</h5>
                    @if($ruta->paradas->count() > 0)
                        <span class="text-muted">
                            {{ $ruta->dias_planificados }} día(s) planificados
                        </span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($ruta->paradas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Orden</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Dirección</th>
                                        <th>Hora est.</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ruta->paradas as $parada)
                                        <tr>
                                            <td>
                                                <span class="badge bg-label-primary">
                                                    #{{ $parada->orden_secuencia }}
                                                </span>
                                            </td>
                                            <td>{{ $parada->fecha_planificada->format('d/m') }}</td>
                                            <td>
                                                <strong>{{ $parada->cliente->razon_social ?? 'Sin cliente' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $parada->cliente->ciudad ?? '' }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $parada->cliente->direccion ?? '—' }}</small>
                                            </td>
                                            <td>{{ $parada->hora_estimada_llegada_formateada ?? '—' }}</td>
                                            <td>
                                                @php
                                                    $estadoBadge = match($parada->estado) {
                                                        'pendiente' => 'secondary',
                                                        'en_ruta' => 'warning',
                                                        'completada' => 'success',
                                                        'no_atendida' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-label-{{ $estadoBadge }}">
                                                    {{ ucfirst(str_replace('_', ' ', $parada->estado)) }}
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
                            @if($ruta->estado === 'borrador')
                                <a href="{{ route('planificacion.rutas.editor', $ruta) }}" class="btn btn-sm btn-primary mt-2">
                                    Agregar paradas
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Métricas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <small class="text-muted d-block">Total de paradas</small>
                        <h3 class="mb-0">{{ $ruta->total_paradas }}</h3>
                    </div>
                    <div class="mb-4">
                        <small class="text-muted d-block">Distancia total</small>
                        <h3 class="mb-0">{{ number_format($ruta->distancia_total_km ?? 0, 1) }} km</h3>
                    </div>
                    <div class="mb-4">
                        <small class="text-muted d-block">Tiempo estimado</small>
                        <h3 class="mb-0">
                            @php
                                $horas = floor(($ruta->tiempo_total_minutos ?? 0) / 60);
                                $minutos = ($ruta->tiempo_total_minutos ?? 0) % 60;
                            @endphp
                            {{ $horas }}h {{ $minutos }}min
                        </h3>
                    </div>
                    <div>
                        <small class="text-muted d-block">Días planificados</small>
                        <h3 class="mb-0">{{ $ruta->dias_planificados }}</h3>
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
    @if(!in_array($ruta->estado, ['en_ejecucion', 'completada']))
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
