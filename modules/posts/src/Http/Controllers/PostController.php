<?php

namespace Modules\Posts\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Posts\Http\Requests\Post\StoreRequest;
use Modules\Posts\Http\Resources\Post\PostResource;
use Modules\Posts\Models\Post;

class PostController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     * @unauthenticated
     */
    public function index(Request $request)
    {
        $request->query('page');

        return PostResource::collection(Post::with(['user'])->paginate(10));
    }

    /**
     * @param Request $request
     * @param Post $post
     * @return PostResource
     */
    public function show(Request $request, Post $post)
    {
        return PostResource::make($post);
    }

    /**
     * @param StoreRequest $request
     * @return PostResource
     */
    public function store(StoreRequest $request)
    {
        $post = $request->user()->posts()->create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        $neo4j = app('neo4j');
        $neo4j->run('CREATE (u:Post {id: $id})', [
            'id' => $post->id,
        ]);

        return PostResource::make($post);
    }

    /**
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function delete(Request $request, Post $post)
    {
        $request->user()->can('delete', $post);

        $neo4j = app('neo4j');
        $neo4j->run('MATCH (p:Post {id: $postId}) DETACH DELETE p', [
            'postId' => $post->id,
        ]);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted',
        ]);
    }

    /**
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function like(Request $request, Post $post)
    {
        $user = $request->user();
        $neo4j = app('neo4j');

        $neo4j->run('MERGE (u:User {id: $userId})', ['userId' => $user->id]);
        $neo4j->run('MERGE (p:Post {id: $postId})', ['postId' => $post->id]);

        $neo4j->run('MATCH (u:User {id: $userId}), (p:Post {id: $postId}) MERGE (u)-[:LIKED]->(p)', [
            'userId' => $user->id,
            'postId' => $post->id,
        ]);

        return response()->json(['message' => 'Post liked successfully']);
    }

    /**
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function dislike(Request $request, Post $post)
    {
        $user = $request->user();
        $neo4j = app('neo4j');

        $neo4j->run('MATCH (u:User {id: $userId})-[r:LIKED]->(p:Post {id: $postId}) DELETE r', [
            'userId' => $user->id,
            'postId' => $post->id,
        ]);

        return response()->json(['message' => 'Post disliked successfully']);
    }
}
