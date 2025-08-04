<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialMediaRequest extends FormRequest
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
            'platform' => 'required|unique:social_media,platform|string',
            'location' => 'required',
            'date'     => 'required|date|in:' . \Carbon\Carbon::today()->toDateString(),
            'forms'        => 'sometimes|array',
            'forms.*.name' => 'required_with:forms|string|max:255',
            'forms.*.description' => 'required_with:forms|string|max:255',
            'forms.*.tags' => 'array|nullable|required_with:forms',
        ];
    }

    public function messages() {
        return [
            'platform.required' => 'Social media name is required.',
            'platform.unique'   => 'This social media name already exists.',
            'platform.string'   => 'Name must be a string.',

            'location.required' => 'Location is required.',

            'date.required'     => 'Date is required.',
            'date.date'         => 'Please enter a valid date.',
            'date.in'           => 'The selected date must be today',

            'forms.array'       => 'Posts data must be an array.',

            'forms.*.name.required_with' => 'Post name is required.',
            'forms.*.name.string'   => 'Post name must be text.',
            'forms.*.name.max:255'  => 'Post name cannot exceed 255 characters.',

            'forms.*.description.required_with' => 'Post description is required.',
            'forms.*.description.string'   => 'Post description must be text.',
            'forms.*.description.max:255'  => 'Post description cannot exceed 255 characters.',

            'forms.*.tags.array' => 'Post tags must be an array.',
            'forms.*.tags.required_with' => 'Post tags are required.',
        ];
    }
}
