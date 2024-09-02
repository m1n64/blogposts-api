<?php

namespace Modules\Posts\Policies\Post;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Posts\Models\Post;
use Modules\User\Models\User;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Определить, может ли пользователь удалить пост.
     *
     * @param User $user
     * @param Post $post
     * @return Response
     */
    public function delete(User $user, Post $post): Response
    {
        return $user->id === $post->user_id ? Response::allow() : Response::deny('You can not delete this post');
    }
}
