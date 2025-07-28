<?php

namespace App\Repositories\Interfaces;

interface ItCanDelete
{
    /**
     * @param int $entityId
     * @return void
     */
    public function delete(int $entityId): void;
}
