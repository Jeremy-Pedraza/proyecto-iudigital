@extends('layouts/contentNavbarLayout')
@section('title', 'Productos - Listado')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Productos</h4>
        <a href="{{ route('admin.productos.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Nuevo producto
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control"
                        placeholder="Nombre, SKU, descripción…">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">— Todos —</option>
                        <option value="active" @selected(($status ?? null) === 'active')>Activo</option>
                        <option value="inactive" @selected(($status ?? null) === 'inactive')>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Orden</label>
                    <select name="sort" class="form-select">
                        @foreach (['name' => 'Nombre', 'sku' => 'SKU', 'price' => 'Precio', 'stock' => 'Stock', 'created_at' => 'Creado'] as $k => $lbl)
                            <option value="{{ $k }}" @selected($sort === $k)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Dir</label>
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
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-secondary"><i class="ti ti-search"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Stock</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td><code>{{ $p->sku }}</code></td>
                            <td class="text-end">${{ number_format($p->price, 2) }}</td>
                            <td class="text-end">{{ $p->stock }}</td>
                            <td>
                                @if ($p->is_active)
                                    <span class="badge bg-label-success">Activo</span>
                                @else
                                    <span class="badge bg-label-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>{{ optional($p->created_at)->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.productos.show', $p) }}"
                                    class="btn btn-sm btn-icon btn-outline-secondary" title="Ver"><i
                                        class="ti ti-eye"></i></a>
                                <a href="{{ route('admin.productos.edit', $p) }}"
                                    class="btn btn-sm btn-icon btn-outline-primary" title="Editar"><i
                                        class="ti ti-edit"></i></a>
                                <form action="{{ route('admin.productos.destroy', $p) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('¿Eliminar este producto?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon btn-outline-danger" title="Eliminar"><i
                                            class="ti ti-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay productos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $products->firstItem() }}–{{ $products->lastItem() }} de {{ $products->total() }}
                </small>
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
