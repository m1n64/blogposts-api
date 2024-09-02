<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Posts\Http\Resources\Post\PostResource;
use Modules\Posts\Models\Post;
use Modules\User\Http\Resources\Auth\AuthUserResource;
use Modules\User\Models\User;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return AuthUserResource
     */
    public function index(Request $request)
    {
        return AuthUserResource::make($request->user());
    }

    /**
     * @param Request $request
     * @param User $user
     * @return AuthUserResource
     */
    public function show(Request $request, User $user)
    {
        return AuthUserResource::make($user)->additional([
            /** @var bool */
            'is_friend' => $request->user()->isFriendWith($user->id),
            'posts' => PostResource::collection($user->posts()->with(['user'])->get()),
            'friends' => AuthUserResource::collection($user->friends()),
        ]);
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function likes(Request $request)
    {
        $userId = $request->user()->id;

        $query = '
            MATCH (u:User {id: $userId})-[:LIKED]->(p:Post)
            RETURN p.id AS postId
        ';

        $neo4j = app('neo4j');

        $result = $neo4j->run($query, ['userId' => $userId]);

        $postIds = collect($result)
            ->map(fn($record) => $record->get('postId'))
            ->toArray();

        $posts = Post::whereIn('id', $postIds)->with('user')->get();

        return PostResource::collection($posts);
    }

    /**
     * @param Request $request
     * @param User $friend
     * @return JsonResponse
     */
    public function addFriend(Request $request, User $friend)
    {
        $userId = $request->user()->id;

        $query = '
            MERGE (u1:User {id: $userId})
            MERGE (u2:User {id: $friendId})
            MERGE (u1)-[:FRIEND]->(u2)
            MERGE (u2)-[:FRIEND]->(u1)
        ';

        $neo4j = app('neo4j');
        $neo4j->run($query, ['userId' => $userId, 'friendId' => $friend->id]);

        return response()->json(['message' => 'Friend added']);
    }

    /**
     * @param Request $request
     * @param User $friend
     * @return JsonResponse
     */
    public function removeFriend(Request $request, User $friend)
    {
        $userId = $request->user()->id;

        $query = '
            MATCH (u1:User {id: $userId})-[f:FRIEND]-(u2:User {id: $friendId})
            DELETE f
        ';

        $neo4j = app('neo4j');
        $neo4j->run($query, ['userId' => $userId, 'friendId' => $friend->id]);

        return response()->json(['message' => 'Friend removed']);
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function friends(Request $request)
    {
        return AuthUserResource::collection($request->user()->friends());
    }
}
