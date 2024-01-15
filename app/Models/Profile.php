<?php

namespace App\Models;

use App\Enums\Sex;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property null|int $id
 * @property null|User $user
 * @property Collection $likesToUsers
 * @property Collection $likesFromUsers
 * @property null|int $user_id
 * @property string $name
 * @property string $bio
 * @property Carbon $birthday
 * @property null|int $height
 * @property null|int $longitude
 * @property null|int $latitude
 * @property Sex $sex
 * @property Carbon $updated_at
 * @property Collection $media
 */
class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
        'id',
        'created_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'birthday',
        'name',
        'bio',
        'sex',
        'height',
        'longitude',
        'latitude',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likesToUsers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'profile_likes', 'profile_id', 'profile_liked_id');
    }

    public function likesFromUsers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'profile_likes', 'profile_liked_id', 'profile_id');
    }

    public function matches(): BelongsToMany
    {
        return $this->likesFromUsers()->whereIn('profile_id', $this->likesToUsers->keyBy('id')->keys());
    }

    public function media(): HasMany
    {
        return $this->hasMany(Medium::class);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'age' => Carbon::createFromFormat('Y-m-d', $this->birthday)->age,
            'bio' => $this->bio ?? '',
            'height' => $this->height,
            'sex' => $this->sex,
            'updated_at' => $this->updated_at->toISOString(),
            'media' => $this->media->toArray(),
        ];
    }
}
