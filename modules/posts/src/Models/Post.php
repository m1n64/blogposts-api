<?php

namespace Modules\Posts\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laudis\Neo4j\Client;
use Modules\User\Models\User;
use Str;

class Post extends Model
{
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'slug',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'content_html',
        'content_short',
        'likes',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     *
     * @param string $title
     * @return void
     */
    public function setTitleAttribute(string $title)
    {
        $this->attributes['title'] = $title;
        $this->attributes['slug'] = Str::slug($title);
    }

    /**
     * Получение пользователей, которые лайкнули пост.
     *
     * @return Attribute
     */
    protected function likes(): Attribute
    {
        return new Attribute(
            get: function () {
                $query = '
                    MATCH (u:User)-[:LIKED]->(p:Post {id: $postId})
                    RETURN u.id AS userId
                ';

                /** @var Client $neo4j */
                $neo4j = app('neo4j');

                $result = $neo4j->run($query, ['postId' => $this->id]);

                $userIds = collect($result)
                    ->map(fn($record) => $record->get('userId'))
                    ->toArray();

                return User::whereIn('id', $userIds)->get();
            },
        );
    }

    /**
     * @return Attribute
     */
    protected function contentHtml(): Attribute
    {
        return new Attribute(
            get: fn() => (new \Parsedown())->text($this->content),
        );
    }

    /**
     * @return Attribute
     */
    protected function contentShort(): Attribute
    {
        return new Attribute(
            get: fn() => Str::limit((new \Parsedown())->text($this->content), 150) . '...',
        );
    }
}
