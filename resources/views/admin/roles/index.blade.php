@extends('layouts/contentNavbarLayout')

@section('title', 'Roles - Listado')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Roles</h4>
    </div>

    @if ($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control"
                        placeholder="Nombre, slug, descripción…">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ordenar por</label>
                    <select name="sort" class="form-select">
                        <option value="name" @selected($sort === 'name')>Nombre</option>
                        <option value="slug" @selected($sort === 'slug')>Slug</option>
                        <option value="created_at" @selected($sort === 'created_at')>Creado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dirección</label>
                    <select name="dir" class="form-select">
                        <option value="asc" @selected($dir === 'asc')>Asc</option>
                        <option value="desc" @selected($dir === 'desc')>Desc</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Por página</label>
                    <select name="per_page" class="form-select">
                        @foreach ([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected($perPage == $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 d-flex justify-content-end">
                    <button class="btn btn-secondary"><i class="ti ti-search"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Descripción</th>
                        <th>Creado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $r)
                        <tr>
                            <td><span class="badge bg-label-primary">{{ $r->name }}</span></td>
                            <td><code>{{ $r->slug }}</code></td>
                            <td>{{ $r->description ?? '—' }}</td>
                            <td>{{ optional($r->created_at)->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No hay roles.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php
            /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $roles */
        @endphp

        @if ($roles instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $roles->firstItem() }}–{{ $roles->lastItem() }} de {{ $roles->total() }}
                </small>
                {{ $roles->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
