<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrador', 'slug' => 'admin', 'description' => 'Acceso total'],
            ['name' => 'Supervisor',    'slug' => 'supervisor', 'description' => 'Planifica y supervisa rutas'],
            ['name' => 'Cobranzas',     'slug' => 'cobranzas', 'description' => 'Gestión de cobros en campo'],
        ];

        foreach ($roles as $data) {
            Role::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
