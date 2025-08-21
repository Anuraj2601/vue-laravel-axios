<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
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
            'forms'       => 'sometimes',
            'forms.name' => 'required_with:forms|string|unique:permissions,name',

            'permissions'           => 'sometimes',
            'permissions.id'      => 'required_with:permissions|exists:permissions,id',
            'permissions.name'    => 'required_with:permissions|string|unique:permissions,name,' . $this->input('permissions.id'),
        ];
    }
    
    public function messages() {
        return [
            /* 'forms.name.required_with' => 'Permission name is required',
            'forms.name.unique' => 'Permission name already taken',

            'permissions.id.required_with' => 'Permission ID is required',
            'permissions.name.required_with' => 'Permission name is required',
            'permissions.name.unique' => 'Permission name already taken.',
            'permissions.name.string' => 'Permission name must be a string', */
        ];
    }
}
