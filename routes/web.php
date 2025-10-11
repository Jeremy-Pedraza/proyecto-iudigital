<?php

use Illuminate\Support\Facades\Route;

// ───────────────────────────────────────────────────────────────────────────────
// 1) IMPORTS EXISTENTES (Materio) – conserva los tuyos
// ───────────────────────────────────────────────────────────────────────────────
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\ForgotPasswordBasic;


// ───────────────────────────────────────────────────────────────────────────────
// 2) NUEVOS IMPORTS – SIGERUTA (ajusta los namespaces a tu estructura)
// ───────────────────────────────────────────────────────────────────────────────
use App\Http\Controllers\Sigeruta\Admin\UsuarioController;
use App\Http\Controllers\Sigeruta\Admin\RolController;
use App\Http\Controllers\Sigeruta\Admin\ProductosController;
use App\Http\Controllers\Sigeruta\Admin\PermisoController;
use App\Http\Controllers\Sigeruta\Admin\AuditoriaController;

use App\Http\Controllers\Sigeruta\Catalogos\ListasRapidasController;

use App\Http\Controllers\Sigeruta\Geo\ZonasController;
use App\Http\Controllers\Sigeruta\Reglas\ReglasController;

use App\Http\Controllers\Sigeruta\Clientes\ClientesController;
use App\Http\Controllers\Sigeruta\Comerciales\ComercialesController;

use App\Http\Controllers\Sigeruta\Rutas\RutaPlannerController;
use App\Http\Controllers\Sigeruta\Rutas\RutasController;
use App\Http\Controllers\Sigeruta\Operacion\AgendaController;
use App\Http\Controllers\Sigeruta\Operacion\ParadasController;
use App\Http\Controllers\Sigeruta\Operacion\PedidosController;
use App\Http\Controllers\Sigeruta\Operacion\CobrosController;
use App\Http\Controllers\Sigeruta\Operacion\EvidenciasController;

use App\Http\Controllers\Sigeruta\Reportes\ReportesController;
use App\Http\Controllers\Sigeruta\Exportes\ExportesController;
use App\Http\Controllers\Sigeruta\Seguridad\DispositivosController;

// ───────────────────────────────────────────────────────────────────────────────
// 3) RUTAS PÚBLICAS (Auth) – Login/Register/Forgot
// ───────────────────────────────────────────────────────────────────────────────

// Landing -> Login
Route::get('/', fn() => redirect()->route('auth-login'))->name('welcome');

// Login
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login');
Route::post('/auth/login-basic', [LoginBasic::class, 'login'])->name('submit-auth-login');
Route::post('/auth/logout', [LoginBasic::class, 'logout'])->name('auth-logout'); // << añade logout

// Registro
Route::get('/auth/register', [RegisterBasic::class, 'index'])->name('auth-register');
Route::post('/auth/register', [RegisterBasic::class, 'register'])->name('submit-auth-register');

// Recuperar contraseña
Route::get('/auth/forgot-password', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password');

// ───────────────────────────────────────────────────────────────────────────────
// 4) RUTAS PROTEGIDAS – requiere autenticación
//    Nota: para control por rol uso 'role:' (spatie o tu middleware).
// ───────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

  // 4.1 Dashboard (Materio)
  Route::get('/dashboard', [Analytics::class, 'index'])->name('dashboard-analytics'); // KPIs base (puedes adaptarlo a tu tablero)

  // ───────────────────────────────────────────────────────────────────
  // 4.3 ADMINISTRACIÓN (Usuarios/Roles/Permisos, Catálogos, Auditoría)
  // Roles: Admin
  // ───────────────────────────────────────────────────────────────────


  Route::prefix('admin')->as('admin.')->middleware(['role:admin'])->group(function () {

    // Usuarios/Roles/Permisos
    // CRUD usuarios
    Route::resource('roles', RolController::class);                     // CRUD roles
    Route::resource('users', UsuarioController::class);
    // // Catálogo de productos y listas rápidas
    Route::resource('productos', ProductosController::class)->parameters(['productos' => 'producto']);           // RF-07
    Route::resource('listas-rapidas', ListasRapidasController::class);  // RF-07

    // // Auditoría/bitácora
    Route::get('auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index'); // RF-16
  });

  // ───────────────────────────────────────────────────────────────────
  // 4.4 PLANIFICACIÓN
  // Roles: Admin | Supervisor (Planner)
  // ───────────────────────────────────────────────────────────────────
  Route::prefix('planificacion')->as('planificacion.')->middleware(['role:admin|Supervisor'])->group(function () {

    // // Catálogos de planificación
    Route::resource('clientes', ClientesController::class)->parameters(['clientes' => 'cliente']);          // RF-05/06
    Route::resource('comerciales', ComercialesController::class)->parameters(['comerciales' => 'comercial']);;    // RF-02
    Route::resource('zonas', ZonasController::class);              // RF-02
    Route::resource('reglas', ReglasController::class)->only(['index', 'update']); // RF-03

    // Planificador de rutas
    Route::get('rutas/planificar', [RutaPlannerController::class, 'create'])->name('rutas.planificar.form'); // RF-01/02/03

    Route::post('rutas/planificar', [RutaPlannerController::class, 'store'])->name('rutas.planificar'); // RF-01/02/03

    Route::get('rutas/planificar/preview', [RutaPlannerController::class, 'preview'])->name('rutas.planificar.preview'); // Vista previa AJAX

    // CRUD de rutas
    Route::get('rutas', [RutasController::class, 'index'])->name('rutas.index'); // RF-04

    Route::get('rutas/{ruta}', [RutasController::class, 'show'])->name('rutas.show'); // RF-04

    Route::get('rutas/{ruta}/editor', [RutasController::class, 'editor'])->name('rutas.editor'); // RF-04 - UI drag&drop

    Route::put('rutas/{ruta}', [RutasController::class, 'update'])->name('rutas.update'); // RF-04

    Route::delete('rutas/{ruta}', [RutasController::class, 'destroy'])->name('rutas.destroy'); // RF-04

    // Operaciones adicionales
    Route::put('rutas/{ruta}/paradas/orden', [RutasController::class, 'reordenarParadas'])->name('rutas.paradas.reorden'); // RF-04 - Reordenar paradas

    Route::post('rutas/{ruta}/publicar', [RutasController::class, 'publicar'])->name('rutas.publicar'); // Publicación a móvil
  });

  // ───────────────────────────────────────────────────────────────────
  // 4.5 OPERACIÓN (Agenda, Paradas, Pedidos, Cobros, Evidencias)
  // Roles: Admin | Supervisor | Cobranzas
  // ───────────────────────────────────────────────────────────────────
  Route::prefix('operacion')->as('operacion.')->middleware(['role:admin|Supervisor|Cobranzas'])->group(function () {

    // Agenda de trabajo (hoy y general)
    // Route::get('agenda', [AgendaController::class, 'index'])->name('agenda.index'); // RF-08
    // Route::get('agenda/hoy', [AgendaController::class, 'hoy'])->name('agenda.hoy'); // RF-08

    // // Paradas: check-in / check-out / no atendido / reprogramar
    // Route::post('paradas/{parada}/check-in', [ParadasController::class, 'checkIn'])->name('paradas.checkin');     // RF-09
    // Route::post('paradas/{parada}/check-out', [ParadasController::class, 'checkOut'])->name('paradas.checkout');  // RF-09
    // Route::post('paradas/{parada}/no-atendido', [ParadasController::class, 'noAtendido'])->name('paradas.noatendido'); // RF-08
    // Route::post('paradas/{parada}/reprogramar', [ParadasController::class, 'reprogramar'])->name('paradas.reprogramar'); // RF-08

    // // Pedidos y Cobros
    // Route::resource('pedidos', PedidosController::class)->only(['index', 'show', 'store']); // RF-10
    // Route::resource('cobros', CobrosController::class)->only(['index', 'show', 'store']);   // RF-11

    // // Evidencias (fotos/firmas)
    // Route::post('evidencias', [EvidenciasController::class, 'store'])->name('evidencias.store'); // RF-11/12
  });

  // ───────────────────────────────────────────────────────────────────
  // 4.6 REPORTES & KPIs
  // Roles: Admin | Supervisor
  // ───────────────────────────────────────────────────────────────────
  Route::prefix('reportes')->as('reportes.')->middleware(['role:admin|Supervisor'])->group(function () {
    //     Route::get('cumplimiento', [ReportesController::class, 'cumplimiento'])->name('cumplimiento'); // RF-14
    //     Route::get('ventas', [ReportesController::class, 'ventas'])->name('ventas');                   // RF-15
    //     Route::get('cartera', [ReportesController::class, 'cartera'])->name('cartera');                // RF-15
  });

  // ───────────────────────────────────────────────────────────────────
  // 4.7 EXPORTES (CSV/XLSX/PDF)
  // Roles: Admin | Supervisor
  // ───────────────────────────────────────────────────────────────────
  Route::prefix('exportes')->as('exportes.')->middleware(['role:admin|Supervisor'])->group(function () {
    // Route::get('{reporte}.csv',  [ExportesController::class, 'csv'])->name('csv');   // RF-20
    // Route::get('{reporte}.xlsx', [ExportesController::class, 'xlsx'])->name('xlsx'); // RF-20
    // Route::get('{reporte}.pdf',  [ExportesController::class, 'pdf'])->name('pdf');   // RF-20
  });

  // ───────────────────────────────────────────────────────────────────
  // 4.8 SEGURIDAD: Dispositivos móviles
  // Roles: Admin | Supervisor
  // ───────────────────────────────────────────────────────────────────
  Route::prefix('dispositivos')->as('dispositivos.')->middleware(['role:admin|Supervisor'])->group(function () {
    //     Route::post('registrar', [DispositivosController::class, 'registrar'])->name('registrar'); // RF-19
    //     Route::delete('{device}', [DispositivosController::class, 'revocar'])->name('revocar');    // RF-19 (cierre remoto)
  });

  // ───────────────────────────────────────────────────────────────────
  // 4.9 Fallback 404 (opcional)
  // ───────────────────────────────────────────────────────────────────
  //Route::fallback(fn() => redirect()->route('pages-misc-error'));
});
