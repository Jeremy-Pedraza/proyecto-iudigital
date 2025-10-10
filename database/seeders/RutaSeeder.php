<?php

namespace Database\Seeders;

use App\Models\Ruta;
use App\Models\Parada;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Seeder;

class RutaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener o crear comerciales
        $comerciales = User::whereHas('roles', function($q) {
            $q->where('name', 'Comercial');
        })->get();

        if ($comerciales->isEmpty()) {
            $this->command->warn('No hay usuarios con rol Comercial. Creando algunos...');
            $comerciales = User::factory(3)->create()->each(function($user) {
                $user->assignRole('Comercial');
            });
        }

        // Obtener clientes existentes
        $clientes = Cliente::all();
        if ($clientes->isEmpty()) {
            $this->command->warn('No hay clientes. Creando algunos...');
            $clientes = Cliente::factory(20)->create();
        }

        $this->command->info('Generando rutas con paradas...');

        // Crear 15 rutas en diferentes estados
        foreach ($comerciales as $comercial) {
            // 2 rutas en borrador
            Ruta::factory()
                ->borrador()
                ->count(2)
                ->create(['comercial_id' => $comercial->id])
                ->each(function ($ruta) use ($clientes) {
                    $this->crearParadas($ruta, $clientes, rand(3, 6));
                });

            // 2 rutas planificadas
            Ruta::factory()
                ->planificada()
                ->count(2)
                ->create(['comercial_id' => $comercial->id])
                ->each(function ($ruta) use ($clientes) {
                    $this->crearParadas($ruta, $clientes, rand(5, 8));
                });

            // 1 ruta publicada
            Ruta::factory()
                ->publicada()
                ->create(['comercial_id' => $comercial->id])
                ->each(function ($ruta) use ($clientes) {
                    $this->crearParadas($ruta, $clientes, rand(6, 10));
                });

            // 2 rutas completadas con paradas completadas
            Ruta::factory()
                ->completada()
                ->count(2)
                ->create(['comercial_id' => $comercial->id])
                ->each(function ($ruta) use ($clientes) {
                    $numParadas = rand(6, 10);
                    $this->crearParadas($ruta, $clientes, $numParadas, true);
                });
        }

        $this->command->info('✓ Rutas creadas exitosamente');
    }

    /**
     * Crear paradas para una ruta
     */
    private function crearParadas(Ruta $ruta, $clientes, int $cantidad, bool $completadas = false): void
    {
        $clientesSeleccionados = $clientes->random(min($cantidad, $clientes->count()));
        $orden = 1;
        $distanciaTotal = 0;
        $tiempoTotal = 0;

        foreach ($clientesSeleccionados as $cliente) {
            $distancia = rand(5, 250) / 10; // 0.5 a 25 km
            $tiempo = rand(5, 60); // 5 a 60 minutos

            $distanciaTotal += $distancia;
            $tiempoTotal += $tiempo;

            $parada = Parada::factory()->create([
                'ruta_id' => $ruta->id,
                'cliente_id' => $cliente->id,
                'orden' => $orden++,
                'distancia_desde_anterior_km' => $distancia,
                'tiempo_desde_anterior_min' => $tiempo,
                'estado' => $completadas
                    ? (rand(1, 10) > 2 ? 'completada' : 'no_atendida')
                    : 'pendiente'
            ]);

            // Si es completada, agregar datos de check-in/out
            if ($completadas && $parada->estado === 'completada') {
                $parada->update([
                    'hora_real_llegada' => now()->addHours(rand(1, 8)),
                    'hora_salida' => now()->addHours(rand(2, 9)),
                    'duracion_real_min' => rand(10, 45),
                    'lat_checkin' => $cliente->lat,
                    'lng_checkin' => $cliente->lng,
                ]);
            }
        }

        // Actualizar totales de la ruta
        $ruta->update([
            'distancia_total_km' => $distanciaTotal,
            'tiempo_estimado_min' => $tiempoTotal
        ]);
    }
}
