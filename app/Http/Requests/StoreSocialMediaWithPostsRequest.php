<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialMediaWithPostsRequest extends FormRequest
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
            'platform' => 'required|array',
            'location' => 'required',
            'date' => [
            'required',
            'date',
                function ($attribute, $value, $fail) {
                    $platformArray = $this->input('platform');
                    $platformName = $platformArray[0]['platform'] ?? null;

                    $platformExists = \App\Models\SocialMedia::where('platform', $platformName)->exists();

                    if (!$platformExists && $value !== \Carbon\Carbon::today()->toDateString()) {
                        $fail(__('validation.custom.date_must_be_today'));
                    }
                }
            ],
            'forms'        => 'required|array',
            'forms.*.name' => 'required|string|max:255',
            'forms.*.description' => 'required|string|max:255',
            'forms.*.tags' => 'array|nullable|required',
        ];
    }
}
