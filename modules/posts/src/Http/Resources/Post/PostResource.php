<?php

namespace Modules\Posts\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Http\Resources\Auth\AuthUserResource;

class PostResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content_short' => $this->content_short,
            'content_html' => $this->content_html,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'author' => AuthUserResource::make($this->user),
            'likes' => AuthUserResource::collection($this->likes),
        ];
    }
}
