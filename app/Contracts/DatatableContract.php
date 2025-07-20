<?php

namespace App\Contracts;

use Illuminate\Database\Query\Builder;

/**
 * Interface DatatableContract
 *
 * @package App\Contracts
 */
interface DatatableContract
{
    /**
     * @return Builder
     */
    public function getQuery();

    /**
     * @param string $class
     * @param array  $attributes
     *
     * @return int
     */
    public function countByQuery(string $class, array $attributes): int;

    /**
     * @param Builder $query
     * @param array   $attributes
     *
     * @return Builder
     */
    public function filterByQuery(Builder $query, array $attributes): Builder;

    /**
     * @return array
     */
    public function searchableColumns(): array;

    /**
     * @param Builder $query
     * @param array   $attributes
     *
     * @return void
     */
    public function filterByCustomQuery(Builder $query, array $attributes = []): void;
}
