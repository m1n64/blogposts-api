<?php

namespace Modules\Posts\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'content' => ['required'],
        ];
    }
}
