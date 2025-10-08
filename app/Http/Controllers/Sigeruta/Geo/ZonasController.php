<?php

namespace App\Http\Controllers\Sigeruta\Geo;

use App\Http\Requests\Zonas\StoreZonaRequest;
use App\Http\Requests\Zonas\UpdateZonaRequest;
use App\Http\Controllers\Controller;
use App\Models\Zona;
use App\Application\Zonas\{
    ListZonasUseCase,
    CreateZonaUseCase,
    UpdateZonaUseCase,
    DeleteZonaUseCase,
    GetZonaByIdUseCase,
    GetZonasActivasUseCase
};
use Illuminate\Http\Request;

class ZonasController extends Controller
{
    public function __construct(
        private ListZonasUseCase $listUC,
        private GetZonaByIdUseCase $getUC,
        private CreateZonaUseCase $createUC,
        private UpdateZonaUseCase $updateUC,
        private DeleteZonaUseCase $deleteUC,
        private GetZonasActivasUseCase $activasUC,
    ) {}

    /**
     * Listado de zonas con filtros
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'ciudad', 'estado', 'sort_by', 'sort_dir']);
        $zonas = ($this->listUC)($filters, (int)$request->get('limit', 15));

        return view('planificacion.zonas.index', compact('zonas', 'filters'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        return view('planificacion.zonas.create');
    }

    /**
     * Guardar nueva zona
     */
    public function store(StoreZonaRequest $request)
    {
        $zona = ($this->createUC)($request->validated());

        return redirect()
            ->route('planificacion.zonas.show', $zona)
            ->with('success', 'Zona creada exitosamente.');
    }

    /**
     * Ver detalle de zona
     */
    public function show(int $zona)
    {
        $zona = ($this->getUC)($zona);

        return view('planificacion.zonas.show', compact('zona'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Zona $zona)
    {
        return view('planificacion.zonas.edit', compact('zona'));
    }

    /**
     * Actualizar zona
     */
    public function update(UpdateZonaRequest $request, Zona $zona)
    {
        ($this->updateUC)($zona, $request->validated());

        return redirect()
            ->route('planificacion.zonas.show', $zona)
            ->with('success', 'Zona actualizada exitosamente.');
    }

    /**
     * Eliminar zona
     */
    public function destroy(Zona $zona)
    {
        ($this->deleteUC)($zona);

        return redirect()
            ->route('planificacion.zonas.index')
            ->with('success', 'Zona eliminada exitosamente.');
    }

    /**
     * API: Obtener zonas activas (para selects)
     */
    public function activas()
    {
        $zonas = ($this->activasUC)();

        return response()->json($zonas);
    }
}
