<?php

namespace App\Infrastructure\Auditoria;

use App\Domain\Contracts\Auditoria\AuditoriaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Auditoria;

class AuditoriaRepository implements AuditoriaRepositoryInterface
{
    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $q = Auditoria::query()->with(['user']);

        if (!empty($filters['search'])) {
            $q->where('description', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['module'])) {
            $q->where('module', $filters['module']);
        }

        if (!empty($filters['action'])) {
            $q->where('action', $filters['action']);
        }

        if (!empty($filters['user_id'])) {
            $q->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $q->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $q->whereDate('created_at', '<=', $filters['date_to']);
        }

        $q->orderByDesc('created_at');

        return $q->paginate($perPage);
    }
}
