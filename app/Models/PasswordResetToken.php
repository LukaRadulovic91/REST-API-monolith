<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PasswordResetToken
 *
 * @package App\Models
 */
class PasswordResetToken extends Model
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
    public $table = 'password_reset_tokens';
}
