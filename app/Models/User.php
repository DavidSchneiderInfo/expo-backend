<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int|null $id
 * @property string $email
 * @property string $name
 * @property Carbon $birthday
 * @property Collection $likesToUsers
 * @property Collection $likesFromUsers
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'bio',
        'password',
        'birthday',
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

    public function likesToUsers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'user_likes', 'user_id', 'user_liked_id');
    }

    public function likesFromUsers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'user_likes', 'user_liked_id', 'user_id');
    }

    public function matches(): BelongsToMany
    {
        return $this->likesFromUsers()->whereIn('user_id', $this->likesToUsers->keyBy('id')->keys());
    }

    public function media(): HasMany
    {
        return $this->hasMany(Medium::class);
    }
}
