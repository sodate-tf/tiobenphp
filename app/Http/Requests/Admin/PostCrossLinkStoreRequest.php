<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PostCrossLinkStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // middleware já blinda /admin
    }

    public function rules(): array
    {
        return [
            'post_id'        => ['required', 'integer', 'exists:posts,id'],
            'linked_post_id' => ['required', 'integer', 'different:post_id', 'exists:posts,id'],
            'link_date'      => ['required', 'date'],
            'paragraph'      => ['required', 'string', 'max:4000'],
            'sort_order'     => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active'      => ['nullable', 'boolean'],
        ];
    }
}
