<?php

namespace App\Http\Controllers\Sigeruta\Rutas;

use App\Http\Requests\Rutas\{UpdateRutaRequest, ReordenarParadasRequest};
use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Application\Rutas\{
    ListRutasUseCase,
    GetRutaByIdUseCase,
    UpdateRutaUseCase,
    DeleteRutaUseCase
};
use App\Domain\Contracts\Rutas\RutaServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RutasController extends Controller
{
    public function __construct(
        private ListRutasUseCase $listUC,
        private GetRutaByIdUseCase $getUC,
        private UpdateRutaUseCase $updateUC,
        private DeleteRutaUseCase $deleteUC,
        private RutaServiceInterface $rutaService
    ) {}

    /**
     * Listado de rutas con filtros
     * RF-04: Visualizar rutas
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'estado',
            'comercial_id',
            'fecha_inicio',
            'fecha_fin',
            'sort_by',
            'sort_dir'
        ]);

        $rutas = ($this->listUC)($filters, (int)$request->get('limit', 15));

        return view('planificacion.rutas.index', compact('rutas', 'filters'));
    }

    /**
     * Ver detalle de ruta
     * RF-04: Visualizar ruta específica con todas sus paradas
     */
    public function show(int $ruta)
    {
        $ruta = ($this->getUC)($ruta, ['comercial:id,name', 'paradas.cliente', 'publicadoPor:id,name']);

        return view('planificacion.rutas.show', compact('ruta'));
    }

    /**
     * Vista del editor de rutas con drag & drop
     * RF-04: Edición manual de rutas
     */
    public function editor(Ruta $ruta)
    {
        // Cargar ruta con todas sus relaciones necesarias
        $ruta->load(['comercial:id,name', 'paradas.cliente']);

        // Verificar si es editable (borrador o calculada)
        if (!in_array($ruta->estado, ['borrador', 'calculada'])) {
            return redirect()
                ->route('planificacion.rutas.show', $ruta)
                ->with('error', 'Esta ruta no puede ser editada en su estado actual.');
        }

        return view('planificacion.rutas.editor', compact('ruta'));
    }

    /**
     * Actualizar información general de la ruta
     * RF-04: Editar ruta
     */
    public function update(UpdateRutaRequest $request, Ruta $ruta)
    {
        try {
            $rutaActualizada = ($this->updateUC)($ruta, $request->validated());

            return redirect()
                ->route('planificacion.rutas.show', $rutaActualizada)
                ->with('success', 'Ruta actualizada correctamente.');
        } catch (\DomainException $e) {
            return back()
                ->withInput()
                ->withErrors(['general' => $e->getMessage()]);
        } catch (\Exception $e) {
            logger()->error('Error actualizando ruta', [
                'ruta_id' => $ruta->id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Error al actualizar la ruta. Intente nuevamente.']);
        }
    }

    /**
     * Reordenar paradas de la ruta (drag & drop)
     * RF-04: Edición manual con validación en tiempo real
     */
    public function reordenarParadas(ReordenarParadasRequest $request, Ruta $ruta)
    {
        try {
            // Validar que la ruta sea editable
            if (!in_array($ruta->estado, ['borrador', 'calculada'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden reordenar paradas de rutas editables'
                ], 422);
            }

            // Reordenar usando el servicio
            $this->rutaService->reordenarParadas($ruta, $request->validated()['orden']);

            // Recargar ruta con datos actualizados
            $ruta->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Orden de paradas actualizado correctamente',
                'data' => [
                    'distancia_total_km' => (float) $ruta->distancia_total_km,
                    'tiempo_total_minutos' => $ruta->tiempo_total_minutos,
                    'total_paradas' => $ruta->total_paradas
                ]
            ]);
        } catch (\Exception $e) {
            logger()->error('Error reordenando paradas', [
                'ruta_id' => $ruta->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Publicar ruta para que esté disponible en móvil
     * RF-04: Publicar ruta
     */
    public function publicar(Ruta $ruta)
    {
        try {
          $usuario = Auth::user();

            $rutaPublicada = $this->rutaService->publicar($ruta, $usuario->id);

            return redirect()
                ->route('planificacion.rutas.show', $rutaPublicada)
                ->with('success', 'Ruta publicada correctamente. Ya está disponible en la aplicación móvil.');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        } catch (\Exception $e) {
            logger()->error('Error publicando ruta', [
                'ruta_id' => $ruta->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['general' => 'Error al publicar la ruta. Intente nuevamente.']);
        }
    }

    /**
     * Eliminar ruta
     * RF-04: Eliminar ruta
     */
    public function destroy(Ruta $ruta)
    {
        try {
            ($this->deleteUC)($ruta);

            return redirect()
                ->route('planificacion.rutas.index')
                ->with('success', 'Ruta eliminada correctamente.');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        } catch (\Exception $e) {
            logger()->error('Error eliminando ruta', [
                'ruta_id' => $ruta->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['general' => 'Error al eliminar la ruta.']);
        }
    }
}
