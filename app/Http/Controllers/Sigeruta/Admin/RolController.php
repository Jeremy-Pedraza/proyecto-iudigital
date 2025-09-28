<?php

namespace App\Http\Controllers\Sigeruta\Admin;

use App\Application\Roles\ListRolesUseCase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RolController extends Controller
{
    public function __construct(
        private readonly ListRolesUseCase $listRoles
    ) {}

    /**
     * GET /admin/roles
     */
    public function index(Request $request)
    {
        $q       = trim((string) $request->query('q', ''));
        $status  = $request->query('status'); // 'active' | 'inactive' | null
        $perPage = (int) $request->integer('per_page', 10) ?: 10;
        $sort    = $request->query('sort', 'name');     // name|slug|created_at
        $dir     = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        try {
            $roles = $this->listRoles->handle(
                query: $q,
                perPage: $perPage,
                sortBy: $sort,
                sortDir: $dir,
                status: $status
            );

            // Conservar filtros en los links
            $roles = $roles->appends($request->query());

            return view('admin.roles.index', compact('roles', 'q', 'perPage', 'sort', 'dir'));
        } catch (Throwable $e) {
            Log::error('Error al listar roles', ['ex' => $e]);
            $bag = new ViewErrorBag();
            $bag->put('default', new MessageBag(['general' => 'Ocurrió un error al cargar los roles.']));

            return view('admin.roles.index', [
                'roles'   => collect(),
                'q'       => $q,
                'perPage' => $perPage,
                'sort'    => $sort,
                'dir'     => $dir,
            ])->with('errors', $bag);
        }
    }

    public function create()
    {
        abort(404);
    }
    public function store(Request $r)
    {
        abort(404);
    }
    public function show()
    {
        abort(404);
    }
    public function edit()
    {
        abort(404);
    }
    public function update(Request $r)
    {
        abort(404);
    }
    public function destroy()
    {
        abort(404);
    }
}
