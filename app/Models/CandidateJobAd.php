<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CandidateJobAd
 *
 * @package App\Models
 */
class CandidateJobAd extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    public $guarded = ['id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'candidates_job_ads';
}
