<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
            'role' => 'sometimes',
            'role.name' => 'required_with:role|string|unique:roles,name',
            'role.permissions' => 'required_with:role|array|nullable',
            'roles' => 'sometimes',
            'roles.id' => 'required_with:roles|exists:roles,id',
            'roles.name' => 'required_with:roles|string|unique:roles,name,' . $this->input('roles.id'),
            'roles.permissions' => 'required_with:roles|array|nullable',
        ];
    }

    /* public function messages()
    {
        return [
            'role.name.required_with' => 'Role Name is required',
            'role.name.unique'      => 'Role name is already taken',
            'role.permissions.required_with' => 'Permission is required',
            'role.permissions.array' => 'Permission must be an array',
            'roles.id.required_with' => 'Role ID is required',
            'roles.name.required_with' => 'Role name is required',
            'roles.name.string' => 'Role name must be string',
            'roles.permissions.required_with' => 'Permission is required',
            'roles.permissions.array' => 'Role permissions must be an array',
        ];
    } */

}
