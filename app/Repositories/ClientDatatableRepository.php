<?php

namespace App\Repositories;

use DB;
use Illuminate\Database\Query\Builder;
use App\Contracts\DatatableContract;
use App\Services\Queryable;

/**
 * Class ClientDatatableRepository
 *
 * @package App\Repositories
 */
class ClientDatatableRepository implements DatatableContract
{
    use Queryable;

    /**
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return  DB::table('clients as c')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('profile_statuses as ps', function ($join) {
                $join->on('ps.status', '=', 'u.profile_status_id')
                    ->on('ps.user_id', '=', 'u.id')
                    ->whereRaw('ps.created_at = (SELECT MAX(created_at) FROM profile_statuses WHERE profile_statuses.user_id = ps.user_id)');
            })
            ->whereNull('u.deleted_at')
            ->select([
                'c.id as id',
                'ps.created_at as request_status',
                'u.address',
                'u.phone_number',
                'c.company_name',
                'u.city',
                'c.office_number',
                'u.email'
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
            'u.phone_number'   => 'u.phone_number',
            'u.address'        => 'u.address',
            'u.email'          => 'u.email',
            'u.city'           => 'u.city',
            'c.office_number'  => 'c.office_number',
            'c.company_name'   => 'c.company_name',
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
    }
}
