<?php

namespace App\Models;

use App\Notifications\RegisterNewAccountNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use YieldStudio\LaravelExpoNotifier\Models\ExpoToken;
use App\Services\UploudableImageTrait;
use App\Notifications\ActivateAccountNotification;
use App\Notifications\ResetPasswordNotification;

/**
 * Class User
 *
 * @package App\Models
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, UploudableImageTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'province',
        'user_image_path',
        'city',
        'postal_code',
        'address',
        'first_name',
        'last_name',
        'phone_number',
        'address',
        'profile_status_id',
        'notifications_enabled',
        'email_verified_at',
        'deleted_at',
        'suite'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * method used to set email_verified_at attribute
     *
     * @param $value
     */
    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = !empty($value) ? $value : null;
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new ActivateAccountNotification());
    }

    /**
     * @return HasMany
     */
    public function profileStatuses(): HasMany
    {
        return $this->hasMany(ProfileStatus::class, 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function softwares(): BelongsToMany
    {
        return $this->belongsToMany(Software::class, 'softwares_users', 'user_id', 'software_id')
            ->withTimestamps();
    }

    /**
     * @return HasOne
     */
    public function client(): HasOne
    {
        return  $this->hasOne(Client::class, 'user_id');
    }

    /**
     * @return HasOne
     */
    public function candidate(): HasOne
    {
        return  $this->hasOne(Candidate::class, 'user_id');
    }

    /**
     * @return HasOne
     */
    public  function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id');
    }

    /**
     * @return MorphMany
     */
    public function expoTokens(): MorphMany
    {
        return $this->morphMany(ExpoToken::class, 'owner');
    }

    /**
     * @return HasMany
     */
    public function twilioSms()
    {
        return $this->hasMany(TwilioSms::class, 'to', 'phone_number')->whereNull('read_at');
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = URL::temporarySignedRoute(
            'password.reset', now()->addMinutes(60), ['token' => $token]
        );

        $this->notify(new ResetPasswordNotification($url, 60));
    }
}
