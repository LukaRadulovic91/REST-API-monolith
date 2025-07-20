<?php

namespace App\Repositories;

use DB;
use Illuminate\Database\Query\Builder;
use App\Enums\Roles;
use App\Services\Queryable;
use App\Contracts\DatatableContract;

/**
 * Class JobAdFeedbackDatatableRepository
 *
 * @package App\Repositories
 */
class JobAdFeedbackDatatableRepository implements DatatableContract
{
    use Queryable;


    public function getQuery(): Builder
    {
        $candidateFeedbackQuery = DB::table('candidates_job_ads as cja')
            ->leftJoin('job_ads as ja', 'ja.id', '=','cja.job_ad_id')
            ->leftJoin('positions as p', 'p.id', '=', 'ja.title')
            ->leftJoin('candidates as ca', 'ca.id', '=', 'cja.candidate_id')
            ->leftJoin('users as user_candidate', function ($join) {
                $join->on('user_candidate.id', '=', 'ca.user_id');
            })
            ->whereNotNull('cja.candidates_feedback')
            ->whereNull('user_candidate.deleted_at')
            ->select(
                DB::raw("CONCAT(user_candidate.first_name, ' ', user_candidate.last_name) as full_name"),
                'cja.candidates_feedback as feedback',
                'cja.job_ad_id',
                'p.title',
                'user_candidate.role_id'
            );


        $clientFeedbackQuery = DB::table('job_ads as ja')
            ->leftJoin('positions as p', 'p.id', '=', 'ja.title')
            ->leftJoin('clients as c', 'c.id', '=', 'ja.client_id')
            ->leftJoin('users as user_client', function ($join) {
                $join->on('user_client.id', '=', 'c.user_id');
            })
            ->whereNotNull('ja.client_feedback')
            ->whereNull('user_client.deleted_at')
            ->select(
                DB::raw("CONCAT(user_client.first_name, ' ', user_client.last_name) as full_name"),
                'ja.client_feedback as feedback',
                'ja.id as job_ad_id',
                'p.title',
                'user_client.role_id'
            );


        $subQueryCandidateFeedback = $candidateFeedbackQuery->toSql();
        $subQueryClientFeedback = $clientFeedbackQuery->toSql();

        $unionQuery = DB::table(DB::raw("($subQueryCandidateFeedback) as candidate_feedback"))
            ->mergeBindings($candidateFeedbackQuery)
            ->leftJoin(DB::raw("($subQueryClientFeedback) as client_feedback"), 'candidate_feedback.job_ad_id', '=', 'client_feedback.job_ad_id')
            ->select(
                'candidate_feedback.full_name',
                'candidate_feedback.feedback',
                'candidate_feedback.job_ad_id as id',
                'candidate_feedback.title',
                'candidate_feedback.role_id as role_id'
            )
            ->unionAll(
                DB::table(DB::raw("($subQueryClientFeedback) as client_feedback"))
                    ->mergeBindings($clientFeedbackQuery)
                    ->leftJoin(DB::raw("($subQueryCandidateFeedback) as candidate_feedback"), 'client_feedback.job_ad_id', '=', 'candidate_feedback.job_ad_id')
                    ->select(
                        'client_feedback.full_name',
                        'client_feedback.feedback',
                        'client_feedback.job_ad_id as id',
                        'client_feedback.title',
                        'client_feedback.role_id as role_id'
                    )
            );

        return DB::table(DB::raw("({$unionQuery->toSql()}) as unioned"))
            ->mergeBindings($unionQuery);

    }

    public function searchableColumns(): array
    {
        return [
            'full_name' => 'full_name',
            'feedback' => 'feedback'
        ];
    }

    public function filterByCustomQuery(Builder $query, array $attributes = []): void
    {
          if (isset($attributes['formData']['is_client'])) {
              if ($attributes['formData']['is_client'] === '1')
              {
                  $query->where('role_id',Roles::CANDIDATE);
              } else {
                  $query->where('role_id', Roles::CLIENT);
              }
          }
    }
}
