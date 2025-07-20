<?php

namespace App\Repositories;

use DB;
use Illuminate\Database\Query\Builder;
use App\Contracts\DatatableContract;
use App\Services\Queryable;

/**
 * Class CandidateDatatableRepository
 *
 * @package App\Repositories
 */
class CandidateDatatableRepository implements DatatableContract
{
    use Queryable;

    /**
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return DB::table('candidates as c')
            ->join('users as u', function ($join) {
                $join->on('u.id', '=', 'c.user_id')
                     ->whereNull('u.deleted_at');
            })
            ->leftJoin('profile_statuses as ps', function ($join) {
                $join->on('ps.status', '=', 'u.profile_status_id')
                    ->on('ps.user_id', '=', 'u.id')
                    ->whereRaw('ps.created_at = (SELECT MAX(created_at) FROM profile_statuses WHERE profile_statuses.user_id = ps.user_id)');
            })
            ->leftJoin('positions as p', 'p.id', '=', 'c.position_id')
            ->select([
                'c.id as id',
                //'ps.created_at as request_status',
                 DB::raw("DATE_FORMAT(ps.created_at, '%M %e, %Y %H:%i:%s') as request_status"),
                'u.address',
                'u.phone_number',
                'p.title'
            ])
            ->selectRaw('concat(u.first_name,\' \', u.last_name) as name')
            ->selectRaw(
                "CASE
                        WHEN ps.status = 1 THEN 'Pending Review'
                        WHEN ps.status = 2 THEN 'Approved'
                        WHEN ps.status = 3 THEN 'Rejected'
                   END
                   AS status"
            );
    }

    /**
     * @return array|string[]
     */
    public function searchableColumns(): array
    {
        return [
            'name'     => DB::raw('concat(u.first_name,\' \', u.last_name)'),
            'u.address' => 'u.address'
        ];
    }

    /**
     * @param Builder $query
     * @param array $attributes
     */
    public function filterByCustomQuery(Builder $query, array $attributes = []): void
    {
        if (isset($attributes['formData']['statuses'])) {
            $query->whereIn('ps.status', $attributes['formData']['statuses']);
        }
        if (isset($attributes['formData']['positions'])) {
            $query->whereIn('p.id', $attributes['formData']['positions']);
        }
    }
}
