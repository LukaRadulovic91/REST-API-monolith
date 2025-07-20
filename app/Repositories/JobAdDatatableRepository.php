<?php

namespace App\Repositories;

use DB;
use Illuminate\Database\Query\Builder;
use App\Services\Queryable;
use App\Contracts\DatatableContract;

/**
 * Class JobAdDatatableRepository
 *
 * @package App\Repositories
 */
class JobAdDatatableRepository implements DatatableContract
{
    use Queryable;


    public function getQuery(): Builder
    {

        return DB::table('job_ads as ja')
            ->leftJoin('shifts as s', function ($join) {
                $join->on('ja.id', '=', 's.job_ad_id')
                    ->whereRaw('s.id = (SELECT MIN(id) FROM shifts WHERE job_ad_id = s.job_ad_id)');
            })
            ->leftJoin('positions as p', 'p.id', '=', 'ja.title')
            ->leftJoin('clients as c', 'c.id', '=', 'ja.client_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->whereNull('u.deleted_at')
            ->select([
                'ja.id as id',
                DB::raw('DATE_FORMAT(ja.permament_start_date, "%d-%m-%Y") as start_date'),
                DB::raw('DATE_FORMAT(s.start_date, "%d-%m-%Y") as shift_start_date'),
                's.end_date as shift_end_date',
                'ja.job_ad_type as category',
                'p.title as position',
                'ja.job_ad_status_id as status',
                'c.company_name as company_name',
                DB::raw('DATE_FORMAT(ja.created_at, "%d-%m-%Y") as created'),
                DB::raw('(SELECT COUNT(*) FROM candidates_job_ads WHERE job_ad_id = ja.id) as applicant_count'),
            ])
            ->selectRaw('concat(u.first_name,\' \', u.last_name) as client_name')
            ->selectRaw("CASE
                    WHEN s.start_date IS NOT NULL THEN s.start_date
                    ELSE ja.permament_start_date
                 END as sort_start_date");
    }

    public function searchableColumns(): array
    {
        return [
            'p.title' => 'p.title',
            'client_name'     => DB::raw('concat(u.first_name,\' \', u.last_name)'),
        ];
    }

    public function filterByCustomQuery(Builder $query, array $attributes = []): void
    {
        if (isset($attributes['formData']['statuses'])) {
            $query->whereIn('ja.job_ad_status_id', $attributes['formData']['statuses']);
        }
        if (isset($attributes['formData']['category'])) {
            $query->whereIn('ja.job_ad_type', $attributes['formData']['category']);
        }
        if (isset($attributes['formData']['positions'])) {
            $query->whereIn('p.id', $attributes['formData']['positions']);
        }
        if (isset($attributes['formData']['start_date'])) {
            $start_date = explode(' - ', $attributes['formData']['start_date']);

            $start_date_from = date('Y-m-d H:i', strtotime($start_date[0]));
            $start_date_to = date('Y-m-d H:i', strtotime($start_date[1]));

            $query->leftJoin('shifts as sh', function ($join) use ($start_date_from, $start_date_to) {
                $join->on('ja.id', '=', 'sh.job_ad_id');
            });
            $query->where(function ($subQuery) use ($start_date_from, $start_date_to) {
                $subQuery->whereBetween('sh.start_date', [$start_date_from, $start_date_to])
                    ->orWhereBetween('ja.permament_start_date', [$start_date_from, $start_date_to]);
            });
            $query->groupBy('ja.id',
                'ja.permament_start_date',
                's.start_date',
                's.end_date',
                'ja.job_ad_type',
                'p.title',
                'ja.job_ad_status_id');

        }

    }

}
