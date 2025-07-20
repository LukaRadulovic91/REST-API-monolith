<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class JobAd
 *
 * @package App\Models
 */
class JobAd extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    public $guarded = ['id'];

    /**
     * @param $action
     *
     * @return mixed
     */
    public function authorize($action): mixed
    {
        return $this->can($action, $this);
    }

    /**
     * @return HasMany
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class, 'job_ad_id');
    }

    /**
     * @return HasMany
     */
    public function statuses(): HasMany
    {
        return $this->hasMany(JobAdStatus::class, 'job_ad_id');
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function position(): HasOne
    {
        return $this->hasOne(Position::class, 'id', 'title');
    }


    /**
     * @return BelongsToMany
     */
    public function candidate(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class, 'candidates_job_ads', 'job_ad_id', 'candidate_id')
            ->withTimestamps()
            ->withPivot('candidates_feedback');
    }
}
