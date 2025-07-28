<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface ItCanUpdate
{
    /**
     * @param int $entityId
     * @param array $attributes
     * @return Model
     */
    public function update(int $entityId, array $attributes): Model;
}
