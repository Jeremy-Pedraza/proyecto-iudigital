<?php

namespace App\Http\Controllers\Sigeruta\Rutas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rutas\StoreRutaRequest;
use App\Application\Rutas\GetPlannerDataUseCase;
use App\Domain\Contracts\Rutas\RutaServiceInterface;
use Illuminate\Http\Request;

class RutaPlannerController extends Controller
{
    public function __construct(
        private GetPlannerDataUseCase $getPlannerDataUC,
        private RutaServiceInterface $rutaService
    ) {}

    /**
     * Muestra el formulario de planificación de rutas
     * GET /planificacion/rutas/planificar
     */
    public function create()
    {
        // Obtener datos necesarios para el formulario
        $data = ($this->getPlannerDataUC)();

        return view('planificacion.rutas.planificar', [
            'comerciales' => $data['comerciales'],
            'ciudades' => $data['ciudades'],
            'frecuencias' => $data['frecuencias'],
            'criterios' => $data['criterios_optimizacion'],
            'defaults' => [
                'max_paradas' => $data['max_paradas_default'],
                'duracion_pausa' => $data['duracion_pausa_default'],
                'hora_inicio' => $data['hora_inicio_default'],
                'hora_fin' => $data['hora_fin_default'],
            ],
        ]);
    }

    /**
     * Procesa el formulario y genera la ruta
     * POST /planificacion/rutas/planificar
     */
    public function store(StoreRutaRequest $request)
    {
        try {
            // Validar solapamiento de fechas
            $haySolapamiento = $this->rutaService->validarSolapamiento(
                $request->validated()
            );

            if ($haySolapamiento) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'fecha_inicio' => 'Ya existe una ruta publicada para este comercial en el período seleccionado'
                    ]);
            }

            // Crear ruta en borrador
            $ruta = $this->rutaService->create($request->validated());

            // Generar paradas según algoritmo
            $ruta = $this->rutaService->generarParadas($ruta);

            return redirect()
                ->route('planificacion.rutas.show', $ruta)
                ->with('success', sprintf(
                    'Ruta generada exitosamente. Total: %d paradas en %d días.',
                    $ruta->total_paradas,
                    $ruta->dias_planificados
                ));
        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors(['general' => $e->getMessage()]);
        } catch (\Exception $e) {
            logger()->error('Error generando ruta', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Ocurrió un error al generar la ruta. Intente nuevamente.']);
        }
    }

    /**
     * Vista previa de clientes elegibles según filtros
     * GET /planificacion/rutas/planificar/preview
     */
    public function preview(Request $request)
    {
        $filtros = $request->only(['comercial_id', 'ciudad', 'frecuencia_filtro', 'prioridad_min']);

        $clientes = $this->rutaService->getClientesElegibles($filtros);

        return response()->json([
            'success' => true,
            'total_clientes' => $clientes->count(),
            'clientes' => $clientes->take(20)->map(fn($c) => [
                'id' => $c->id,
                'razon_social' => $c->razon_social,
                'ciudad' => $c->ciudad,
                'frecuencia' => ucfirst($c->frecuencia_visita),
                'prioridad' => $c->prioridad,
            ]),
        ]);
    }
}
