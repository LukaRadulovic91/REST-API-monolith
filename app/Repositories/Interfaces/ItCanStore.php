<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface ItCanStore
{
    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model;
}
