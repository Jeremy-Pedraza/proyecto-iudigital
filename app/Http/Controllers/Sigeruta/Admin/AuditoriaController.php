<?php

namespace App\Http\Controllers\Sigeruta\Admin;

use App\Application\Auditoria\ListAuditoriaUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function __construct(
        private readonly ListAuditoriaUseCase $listAuditLogs
    ) {}

    public function index(Request $request)
    {
        $filters = [
            'search'    => $request->query('search'),
            'module'    => $request->query('module'),
            'action'    => $request->query('action'),
            'user_id'   => $request->query('user_id'),
            'date_from' => $request->query('date_from'),
            'date_to'   => $request->query('date_to'),
        ];

        $perPage   = (int)($request->query('per_page', 15));
        $audits = ($this->listAuditLogs)($filters, $perPage)->appends($request->query());

        return view('admin.auditoria.index', [
            'audits'  => $audits,
            'filters' => $filters,
        ]);
    }
}
