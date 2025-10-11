<?php

namespace App\Http\Controllers\Sigeruta\Comerciales;

use App\Http\Requests\Comerciales\StoreComercialRequest;
use App\Http\Requests\Comerciales\UpdateComercialRequest;
use App\Http\Controllers\Controller;
use App\Models\Comercial;
use App\Models\Zona;
use App\Application\Comerciales\{
    ListComercialesUseCase,
    CreateComercialesUseCase,
    UpdateComercialesUseCase,
    DeleteComercialesUseCase,
    GetComercialByIdUseCase
};
use Illuminate\Http\Request;

class ComercialesController extends Controller
{
    public function __construct(
        private ListComercialesUseCase $listUC,
        private GetComercialByIdUseCase $getUC,
        private CreateComercialesUseCase $createUC,
        private UpdateComercialesUseCase $updateUC,
        private DeleteComercialesUseCase $deleteUC,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'zona_id', 'estado', 'sort_by', 'sort_dir']);
        $comerciales = ($this->listUC)($filters, (int)$request->get('limit', 15));

        $zonas = Zona::orderBy('nombre')->get(['id', 'nombre']);

        return view('planificacion.comerciales.index', compact('comerciales', 'filters', 'zonas'));
    }

    public function create()
    {
          // 👇 Cargar zonas para el formulario de creación
          $zonas = Zona::orderBy('nombre')->get(['id', 'nombre']);
        return view('planificacion.comerciales.create', compact('zonas'));
    }

    public function store(StoreComercialRequest $request)
    {
        $comercial = ($this->createUC)($request->validated());

        return redirect()
            ->route('planificacion.comerciales.show', $comercial)
            ->with('success', 'Comercial creado exitosamente.');
    }

    public function show(int $comercial)
    {
        $comercial = ($this->getUC)($comercial, ['user:id,name', 'zona:id,nombre,color']);

        // 👇 Cargar zonas para posibles acciones (aunque no se edita aquí)
        $zonas = Zona::orderBy('nombre')->get(['id', 'nombre']);

        return view('planificacion.comerciales.show', compact('comercial', 'zonas'));
    }

    public function edit(Comercial $comercial)
    {
        // 👇 Cargar zonas para el formulario de edición
        $zonas = Zona::orderBy('nombre')->get(['id', 'nombre']);

        // Opcional: cargar relaciones si las necesitas
        $comercial->load(['zona:id,nombre']);

        return view('planificacion.comerciales.edit', compact('comercial', 'zonas'));
    }
    public function update(UpdateComercialRequest $request, Comercial $comercial)
    {
        ($this->updateUC)($comercial, $request->validated());

        return redirect()
            ->route('planificacion.comerciales.show', $comercial)
            ->with('success', 'Comercial actualizado exitosamente.');
    }

    public function destroy(Comercial $comercial)
    {
        try {
            ($this->deleteUC)($comercial);
            return redirect()
                ->route('planificacion.comerciales.index')
                ->with('success', 'Comercial eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()
                ->route('planificacion.comerciales.index')
                ->with('error', $e->getMessage());
        }
    }
}
