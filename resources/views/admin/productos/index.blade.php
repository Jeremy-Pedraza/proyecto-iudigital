@extends('layouts/contentNavbarLayout')
@section('title', 'Productos - Listado')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Productos</h4>
        <a href="{{ route('admin.productos.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Nuevo producto
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
                    <button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
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
                                        class="fa-regular fa-eye"></i></a>
                                <a href="{{ route('admin.productos.edit', $p) }}"
                                    class="btn btn-sm btn-icon btn-outline-primary" title="Editar"><i
                                        class="fa-regular fa-pen-to-square"></i></a>
                                <form action="{{ route('admin.productos.destroy', $p) }}" method="POST"
                                    id="delete-form-{{ $p->id }}" class="d-inline js-delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger js-open-delete"
                                        data-form="delete-form-{{ $p->id }}" data-name="{{ $p->name }}"
                                        title="Eliminar">
                                        <i class="fa-regular fa-square-minus"></i>
                                    </button>
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

@section('modals')
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Confirmar eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0">
                        ¿Seguro que deseas eliminar el producto
                        <strong data-product-name></strong>?
                        Esta acción no se puede deshacer.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="btn-confirm-delete">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let targetFormId = null;

            const modalEl = document.getElementById('modalDelete');
            const nameEl = modalEl.querySelector('[data-product-name]');
            const confirmEl = document.getElementById('btn-confirm-delete');
            const bsModal = new bootstrap.Modal(modalEl);

            // Delegación: cualquier botón .js-open-delete abre el modal
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.js-open-delete');
                if (!btn) return;

                targetFormId = btn.dataset.form || null;
                nameEl.textContent = btn.dataset.name || '';
                bsModal.show();
            });

            // Al confirmar, enviamos el formulario objetivo
            confirmEl.addEventListener('click', function() {
                if (!targetFormId) return;
                const form = document.getElementById(targetFormId);
                if (form) form.submit();
            });
        });
    </script>
@endpush
