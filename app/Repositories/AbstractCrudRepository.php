<?php

namespace App\Repositories;

use Exception;
use App\Repositories\Interfaces\ItCanDelete;
use App\Repositories\Interfaces\ItCanFetch;
use App\Repositories\Interfaces\ItCanStore;
use App\Repositories\Interfaces\ItCanUpdate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class AbstractCrudRepository
 *
 * @package App\Repositories
 */
abstract class AbstractCrudRepository implements ItCanFetch, ItCanStore, ItCanUpdate, ItCanDelete
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * @param int $entityId
     * @return Model
     */
    public function findOrFail(int $entityId): Model
    {
        return $this->model->findOrFail($entityId);
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * @param int $entityId
     * @param array $attributes
     * @return Model
     */
    public function update(int $entityId, array $attributes): Model
    {
        $entity = $this->findOrFail($entityId);
        $entity->update($attributes);

        return $entity;
    }

    /**
     * @param int $entityId
     * @throws Exception
     */
    public function delete(int $entityId): void
    {
        $entity = $this->findOrFail($entityId);

        $entity->delete();
    }
}
