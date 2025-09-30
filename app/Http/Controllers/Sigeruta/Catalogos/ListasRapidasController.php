<?php

namespace App\Http\Controllers\Sigeruta\Catalogos;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListasRapidas\StoreListaRapidaRequest;
use App\Http\Requests\ListasRapidas\UpdateListaRapidaRequest;
use App\Application\ListasRapidas\ListListasRapidasUseCase;
use App\Application\ListasRapidas\GetListaRapidaByIdUseCase;
use App\Application\ListasRapidas\CreateListaRapidaUseCase;
use App\Application\ListasRapidas\UpdateListaRapidaUseCase;
use App\Application\ListasRapidas\DeleteListaRapidaUseCase;
use Illuminate\Http\Request;

class ListasRapidasController extends Controller
{
    public function __construct(
        private ListListasRapidasUseCase $listUC,
        private GetListaRapidaByIdUseCase $getUC,
        private CreateListaRapidaUseCase $createUC,
        private UpdateListaRapidaUseCase $updateUC,
        private DeleteListaRapidaUseCase $deleteUC,
    ) {}

    public function index(Request $request)
    {
        $filters = [
            'search'  => $request->string('search')->toString(),
            'grupo'   => $request->string('grupo')->toString(),
            'estado'  => $request->has('estado') ? $request->string('estado')->toString() : null,
            'sortBy'  => $request->get('sortBy', 'grupo'),
            'sortDir' => $request->get('sortDir', 'asc'),
        ];
        $perPage = (int) $request->get('perPage', 10);

        $items = ($this->listUC)($filters, $perPage);

        return view('admin.listas-rapidas.index', compact('items', 'filters'));
    }

    public function create()
    {
        return view('admin.listas-rapidas.create');
    }

    public function store(StoreListaRapidaRequest $request)
    {
        ($this->createUC)($request->validated());
        return redirect()->route('admin.listas-rapidas.index')->with('success', 'Registro creado correctamente.');
    }

    public function show(int $id)
    {
        $item = ($this->getUC)($id);
        return view('admin.listas-rapidas.show', compact('item'));
    }

    public function edit(int $id)
    {
        $item = ($this->getUC)($id);
        return view('admin.listas-rapidas.edit', compact('item'));
    }

    public function update(UpdateListaRapidaRequest $request, int $id)
    {
        ($this->updateUC)($id, $request->validated());
        return redirect()->route('admin.listas-rapidas.index')->with('success', 'Registro actualizado.');
    }

    public function destroy(int $id)
    {
        ($this->deleteUC)($id);
        return redirect()->route('admin.listas-rapidas.index')->with('success', 'Registro eliminado.');
    }
}
