<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Hash;  
use Illuminate\Database\Eloquent\Concerns\HasUuids;   
use App\Notifications\ResetPasswordNotification;   
use App\Notifications\VerifyEmailNotification;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'level_id',
        'access_group_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function level(): object
    {
        return $this->belongsTo(Level::class);
    }
    public function access_group(): object
    {
        return $this->belongsTo(AccessGroup::class);
    }

    public function setPasswordAttribute($value): void
{
    // Cek apakah password sudah hash (panjang hash bcrypt 60 karakter dan diawali $2y$)
    if ($value && (strlen($value) !== 60 || !preg_match('/^\$2y\$/', $value))) {
        $this->attributes['password'] = Hash::make($value);
    } else {
        $this->attributes['password'] = $value;
    }
}

    public function tokens(): object
    {
        return $this->morphMany(Sanctum::$personalAccessTokenModel, 'tokenable');
    }

    public function log(): object
    {
        return $this->morphMany(Log::class, 'loggable');
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getCreateAttribute(): bool
    {
        return $this->access_group->canAccess('create');
    }

    public function getReadAttribute(): bool
    {
        return $this->access_group->canAccess('read');
    }

    public function getUpdateAttribute(): bool
    {
        return $this->access_group->canAccess('update');
    }

    public function getDeleteAttribute(): bool
    {
        return $this->access_group->canAccess('delete');
    }

    public function scopeFilterLevel($query)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $level = $user && $user->level ? $user->level->code : null;
        if ($level !== 'root') {
            if ($level === 'user') {
                $query->where('id', $user ? $user->id : null);
            }
            $query->where('level_id', '!=', '1');
        }
        return $query;
    }

    public function getAllUserIdAttribute() : array
    {
        return $this->whereNotIn('level_id', [1])->pluck('id')->toArray();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }   

        public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }
}
