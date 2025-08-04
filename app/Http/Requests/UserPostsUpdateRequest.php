<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserPostsUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id'       => 'required|exists:social_media,id',
            'forms'        => 'sometimes|array',
            'forms.*.name' => 'required_with:forms|string|max:255',
            'forms.*.description' => 'required_with:forms|string|max:255',
            'forms.*.tags' => 'array|nullable|required_with:forms',
            'posts'        => 'sometimes|array',
            'posts.*.id'   => 'required_with:posts|exists:posts,id',
            'posts.*.name' => 'required_with:posts|string|max:255',
            'posts.*.description' => 'required_with:posts|string|max:255',
            'posts.*.tags' => 'array|nullable|required_with:posts',
        ];
    }

    public function messages() {
        return [
            'id.required' => 'The social media ID is required.',
            'id.exists' => 'Selected social media ID is invalid.',

            'forms.*.name.required_with' => 'Post name is required.',
            'forms.*.name.string' => 'Post name must be a string.',
            'forms.*.name.max' => 'The name must not exceed 255 characters.',
            'forms.*.description.required_with' => 'Post description is required.',
            'forms.*.description.string' => 'Post description must be a string.',
            'forms.*.description.max' => 'Post description must not exceed 255 characters.',
            'forms.*.tags.required_with' => 'At least one post tag is required.',
            'forms.*.tags.array' => 'Tags must be an array.',

            'posts.*.id.required_with' => 'The post ID is required.',
            'posts.*.id.exists' => 'Selected post ID is invalid.',
            'posts.*.name.required_with' => 'Post name is required.',
            'posts.*.name.string' => 'Post name must be a string.',
            'posts.*.name.max' => 'Post name must not exceed 255 characters.',
            'posts.*.description.required_with' => 'Post description is required.',
            'posts.*.description.string' => 'Post description must be a string.',
            'posts.*.description.max' => 'Post description must not exceed 255 characters.',
            'posts.*.tags.required_with' => 'At least one post tag is required',
            'posts.*.tags.array' => 'Post tags must be an array.',
        ];
    }
}
