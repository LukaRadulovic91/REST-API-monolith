<?php

namespace App\Models;

use App\Services\UploadableFileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Candidate
 *
 * @package App\Models
 */
class Candidate extends Model
{
    use HasFactory, UploadableFileTrait;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    public $guarded = ['id'];

    /**
     * @return BelongsToMany
     */
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'candidates_languages', 'candidate_id', 'language_id')
            ->withTimestamps();
    }

    /**
     * @return HasOne
     */
    public  function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function jobAd(): BelongsToMany
    {
        return $this->belongsToMany(JobAd::class, 'candidates_job_ads', 'candidate_id', 'job_ad_id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function candidateMedia(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'candidates_media', 'candidate_id', 'media_id');
    }

     /**
     * @return BelongsToMany
     */
    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'candidates_positions', 'candidate_id', 'position_id');
    }
}
