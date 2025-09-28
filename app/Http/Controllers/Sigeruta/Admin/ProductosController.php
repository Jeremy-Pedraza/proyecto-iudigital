<?php

namespace App\Http\Controllers\Sigeruta\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use App\Application\Products\ListProductsUseCase;
use App\Application\Products\GetProductUseCase;
use App\Application\Products\CreateProductUseCase;
use App\Application\Products\UpdateProductUseCase;
use App\Application\Products\DeleteProductUseCase;

use App\Models\Product;

class ProductosController extends Controller
{
    public function __construct(
        private readonly ListProductsUseCase $listProducts,
        private readonly GetProductUseCase $getProduct,
        private readonly CreateProductUseCase $createProduct,
        private readonly UpdateProductUseCase $updateProduct,
        private readonly DeleteProductUseCase $deleteProduct,
    ) {}

    public function index(Request $request)
    {
        $q       = trim($request->string('q'));
        $status  = $request->string('status'); // active|inactive|null
        $sort    = $request->string('sort', 'name');
        $dir     = strtolower($request->string('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = (int)$request->integer('per_page', 10) ?: 10;

        try {
            $products = $this->listProducts->handle(
                query: $q,
                status: $status,
                perPage: $perPage,
                sortBy: $sort,
                sortDir: $dir
            )->appends($request->query());

            return view('admin.productos.index', compact('products', 'q', 'status', 'sort', 'dir', 'perPage'));
        } catch (\Throwable $e) {
            Log::error('Error al listar productos', ['ex' => $e]);
            return redirect()->route('admin.productos.index', $request->query())
                ->withErrors(['general' => 'Ocurrió un error al cargar los productos.']);
        }
    }

    public function create()
    {
        $producto = new Product();
        return view('admin.productos.create', compact('producto'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'sku'         => ['required', 'string', 'max:64', 'unique:products,sku'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'cost'        => ['nullable', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        try {
            $data = [
                ...$validated,
                'is_active' => (bool)($validated['is_active'] ?? true),
            ];

            $product = $this->createProduct->handle($data);

            return redirect()->route('admin.productos.edit', $product)
                ->with('success', 'Producto creado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear producto', ['ex' => $e]);
            return back()->withInput()->withErrors(['general' => 'No se pudo crear el producto.']);
        }
    }

    public function show(Product $producto)
    {
        return view('admin.productos.show', compact('producto'));
    }

    public function edit(Product $producto)
    {
        return view('admin.productos.edit', compact('producto'));
    }

    public function update(Request $request, Product $producto)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'sku'         => ['required', 'string', 'max:64', Rule::unique('products', 'sku')->ignore($producto->id)],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'cost'        => ['nullable', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        try {
            $changes = [
                ...$validated,
                'is_active' => (bool)($validated['is_active'] ?? $producto->is_active),
            ];

            $product = $this->updateProduct->handle($producto->id, $changes);

            return redirect()->route('admin.productos.edit', $product)
                ->with('success', 'Producto actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar producto', ['id' => $producto->id, 'ex' => $e]);
            return back()->withInput()->withErrors(['general' => 'No se pudo actualizar el producto.']);
        }
    }

    public function destroy(Product $producto)
    {
        try {
            $this->deleteProduct->handle($producto->id);

            return redirect()->route('admin.productos.index')
                ->with('success', 'Producto eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar producto', ['id' => $producto->id, 'ex' => $e]);
            return redirect()->route('admin.productos.index')
                ->withErrors(['general' => 'No se pudo eliminar el producto.']);
        }
    }
}
