@extends('layouts/contentNavbarLayout')

@section('title', 'Auditoría - Listado')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Auditoría</h4>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <form method="GET" class="row gx-2 gy-2 align-items-center">
                <!-- Buscar más angosto -->
                <div class="col-12 col-md-2 col-lg-2" style="max-width: 220px;">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar..."
                        value="{{ $filters['search'] }}">
                </div>

                <div class="col-6 col-md-2 col-lg-2">
                    <input type="text" name="module" class="form-control form-control-sm" placeholder="Módulo"
                        value="{{ $filters['module'] }}">
                </div>

                <div class="col-6 col-md-2 col-lg-2">
                    <input type="text" name="action" class="form-control form-control-sm" placeholder="Acción"
                        value="{{ $filters['action'] }}">
                </div>

                <div class="col-6 col-md-2 col-lg-2">
                    <input type="number" name="user_id" class="form-control form-control-sm" placeholder="Usuario ID"
                        value="{{ $filters['user_id'] }}">
                </div>

                <!-- Rango de fechas compacto -->
                <div class="col-12 col-md-3 col-lg-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="date" name="date_from" class="form-control form-control-sm"
                                value="{{ $filters['date_from'] }}">
                        </div>
                        <div class="col-6">
                            <input type="date" name="date_to" class="form-control form-control-sm"
                                value="{{ $filters['date_to'] }}">
                        </div>
                    </div>
                </div>

                <!-- Botón a la derecha en desktop, ancho completo en móvil -->
                <div class="col-12 col-md-auto ms-md-auto">
                    <button class="btn btn-primary btn-sm w-100 w-md-auto">
                        <i class="fa-solid fa-search me-1"></i> Buscar
                    </button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Módulo</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $a)
                        <tr>
                            <td>{{ $a->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ optional($a->user)->name ?? '—' }}</td>
                            <td>{{ $a->module }}</td>
                            <td>{{ $a->action }}</td>
                            <td>{{ Str::limit($a->description, 120) }}</td>
                            <td>{{ $a->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Sin resultados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $audits->links() }}
        </div>
    </div>
@endsection
