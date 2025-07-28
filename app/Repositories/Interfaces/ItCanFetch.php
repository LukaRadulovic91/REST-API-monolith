<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ItCanFetch
{
    /**
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * @param int $entityId
     * @return Model
     */
    public function findOrFail(int $entityId): Model;
}
