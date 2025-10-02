<?php

namespace App\Http\Controllers\Sigeruta\Clientes;

use App\Http\Requests\Clientes\StoreClienteRequest;
use App\Http\Requests\Clientes\UpdateClienteRequest;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Application\Clientes\{
    ListClientesUseCase,
    CreateClienteUseCase,
    UpdateClienteUseCase,
    DeleteClienteUseCase,
    GetClienteByIdUseCase
};
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function __construct(
        private ListClientesUseCase $listUC,
        private GetClienteByIdUseCase $getUC,
        private CreateClienteUseCase $createUC,
        private UpdateClienteUseCase $updateUC,
        private DeleteClienteUseCase $deleteUC,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'ciudad', 'estado', 'comercial_id', 'frecuencia', 'sort_by', 'sort_dir']);
        $clientes = ($this->listUC)($filters, (int)$request->get('limit', 15));
        return view('planificacion.clientes.index', compact('clientes', 'filters'));
    }

    public function create()
    {
        return view('planificacion.clientes.create');
    }

    public function store(StoreClienteRequest $request)
    {
        $cliente = ($this->createUC)($request->validated());
        return redirect()->route('planificacion.clientes.show', $cliente)->with('success', 'Cliente creado.');
    }

    // usando el caso de uso ObtenerCliente (patrón nuevo)
    public function show(int $cliente)
    {
        $cliente = ($this->getUC)($cliente, ['comercial:id,name']); // ModelNotFound (404) si no existe
        return view('planificacion.clientes.show', compact('cliente'));
    }

    // si prefieres binding automático, cambia firma a: edit(Cliente $cliente)
    public function edit(Cliente $cliente)
    {
        return view('planificacion.clientes.edit', compact('cliente'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente, UpdateClienteUseCase $actualizar)
    {
        $actualizar($cliente, $request->validated());
        return redirect()->route('planificacion.clientes.show', $cliente)->with('success', 'Cliente actualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        ($this->deleteUC)($cliente);
        return redirect()->route('planificacion.clientes.index')->with('success', 'Cliente eliminado.');
    }
}
