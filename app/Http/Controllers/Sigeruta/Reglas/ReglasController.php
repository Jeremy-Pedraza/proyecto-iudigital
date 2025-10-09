<?php

namespace App\Http\Controllers\Sigeruta\Reglas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reglas\UpdateReglaRequest;
use App\Application\Reglas\{
    ListReglasUseCase,
    UpdateReglaUseCase
};
use App\Models\Regla;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReglasController extends Controller
{
    public function __construct(
        private ListReglasUseCase $listUC,
        private UpdateReglaUseCase $updateUC
    ) {}

    /**
     * Muestra el listado de reglas agrupadas por categoría
     * 
     * @return View
     */
    public function index(): View
    {
        $reglasAgrupadas = ($this->listUC)(groupByCategory: true);

        // Obtener las categorías en orden específico para mejor UX
        $categoriasOrden = [
            'capacidad' => 'Capacidad',
            'tiempo' => 'Tiempo y Jornada',
            'distancia' => 'Distancias',
            'optimizacion' => 'Optimización',
            'restricciones' => 'Restricciones',
            'penalizaciones' => 'Penalizaciones',
            'general' => 'General'
        ];

        return view('planificacion.reglas.index', compact('reglasAgrupadas', 'categoriasOrden'));
    }

    /**
     * Actualiza una regla específica
     * 
     * @param UpdateReglaRequest $request
     * @param Regla $regla
     * @return RedirectResponse
     */
    public function update(UpdateReglaRequest $request, Regla $regla): RedirectResponse
    {
        try {
            // Preparar datos validados
            $data = ['valor' => $request->validated()['valor']];

            // Si se envió el estado 'activa', lo incluimos
            if ($request->has('activa')) {
                $data['activa'] = filter_var($request->input('activa'), FILTER_VALIDATE_BOOLEAN);
            }

            // Ejecutar el caso de uso
            ($this->updateUC)($regla, $data);

            return redirect()
                ->route('planificacion.reglas.index')
                ->with('success', "Regla '{$regla->nombre}' actualizada correctamente.");
        } catch (\DomainException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['valor' => $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar la regla. Inténtalo nuevamente.']);
        }
    }
}
