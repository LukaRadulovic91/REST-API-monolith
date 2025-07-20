<?php

namespace App\Models;

use App\Services\CreatedUpdatedByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class JobAdStatus
 *
 * @package App\Models
 */
class JobAdStatus extends Model
{
    use HasFactory, CreatedUpdatedByTrait;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    public $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function jobAd(): BelongsTo
    {
        return $this->belongsTo(JobAd::class);
    }
}
