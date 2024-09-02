<?php

namespace Modules\User\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Modules\Posts\Models\Post;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'profile_photo_path',
        'description',
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
     * @var string[]
     */
    protected $appends = [
        'profile_photo'
    ];

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function isFriendWith(int $userId): mixed
    {
        $query = '
            MATCH (u1:User {id: $userId})-[:FRIEND]-(u2:User {id: $friendId})
            RETURN COUNT(u2) > 0 AS isFriend
        ';

        $neo4j = app('neo4j');
        $result = $neo4j->run($query, ['userId' => $this->id, 'friendId' => $userId]);

        return $result->first()->get('isFriend');
    }

    /**
     * @return Collection
     */
    public function friends(): Collection
    {
        $userId = $this->id;

        $query = '
            MATCH (u:User {id: $userId})-[:FRIEND]-(f:User)
            WHERE u.id <> f.id
            RETURN DISTINCT f.id AS friendId
        ';

        $neo4j = app('neo4j');
        $result = $neo4j->run($query, ['userId' => $userId]);

        $friendIds = collect($result)
            ->map(fn($record) => $record->get('friendId'))
            ->toArray();

        return User::whereIn('id', $friendIds)->get();
    }

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

    /**
     * @return Attribute
     */
    protected function profilePhoto(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value)->toString() : null,
        );
    }
}
