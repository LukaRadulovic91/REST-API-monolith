<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\ArrayRecursable;
use Illuminate\Database\Query\Builder;

/**
 * Trait Queryable
 *
 * @package App\Services
 */
trait Queryable
{
    use ArrayRecursable;

    /**
     * Returns query result
     *
     * @param array $attributes
     *
     * @return iterable
     */
    public function get(array $attributes): iterable
    {
        $result = $this->getQuery($attributes);

        return $result->offset($attributes['start'])
            ->limit($attributes['length'])
            ->get();
    }

    /**
     * Counts all the rows of the actual query
     *
     * @param string $class
     * @param array  $attributes
     *
     * @return int
     */
    public function countByQuery(string $class, array $attributes): int
    {
        return $this->getQuery($attributes)->count();
    }

    /**
     * Filters the query by the given datatable columns
     *
     * @param Builder $query
     * @param array   $attributes
     *
     * @return Builder
     */
    public function filterByQuery(Builder $query, array $attributes): Builder
    {
        $searchTerm = $attributes['search']['value'] ?? null;
        if (isset($searchTerm)) {
            $this->filterSearchTerm($searchTerm, $attributes, $query);
        }

        return $query;
    }

    /**
     * Orders the query by the selected datatables column
     *
     * @param Builder $query
     * @param array   $attributes
     *
     * @return Builder
     */
    public function orderByQuery(Builder $query, array $attributes): Builder
    {
        $order = $attributes['order'] ?? null;
        if (empty($order)) {
            return $query;
        }

        $ordering = '';
        foreach ($order as $index => $orderColumn) {
            $ordering .= max($attributes['columns'][$orderColumn['column']]['name'], '1') . ' ' . $orderColumn['dir'];
            if (count($order) == $index) {
                $ordering .= ', ';
            }
        }

        return $query->orderByRaw($ordering);
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function getFilters(array $attributes): array
    {
        return array_filter($attributes['filters'] ?? [], function ($filter) {
            if (is_array($filter)) {
                return static::arrayFilterRecursive($filter);
            }
            if ($filter === '0') {
                return true;
            }
            if (isset($filter) && strlen($filter)) {
                return $filter;
            }

            return null;
        });
    }

    /**
     * @param string  $searchTerm
     * @param array   $attributes
     * @param Builder $query
     *
     * @return void
     */
    public function filterSearchTerm(string $searchTerm, array $attributes, Builder &$query): void
    {
        $columns = array_column(collect($attributes['columns'])->where('searchable', 'true')->toArray(), 'data');
        $attributesTableColumns = array_combine($columns, $columns);
        $searchableColumns = $this->searchableColumns();
        $validatedColumns = array_intersect_key($attributesTableColumns, $searchableColumns);
        $columns = array_merge($validatedColumns, $searchableColumns);

        $query = $query->where(function ($query) use ($columns, $searchTerm): void {
            foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', '%' . $searchTerm . '%');
            }
        });
    }

    /**
     * @param Builder $query
     * @param array   $attributes
     *
     * @return void
     */
    public function filterByCustomQuery(Builder $query, array $attributes = []): void
    {
        return;
    }
}
