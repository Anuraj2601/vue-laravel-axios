<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'  => 'required|string|max:30',
            'email' => 'required|string|email|max:200|unique:users,email',
            'password'  => 'required|string|confirmed|min:8'
        ];
    }

    /**
     * Customize the failed validation response.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();

        // Flatten errors into string format (single error message per field)
        $formattedErrors = [];
        foreach ($errors as $field => $messages) {
            // Get the first error message for each field
            $formattedErrors[$field] = $messages[0]; // Single error message for each field
        }

        // Return the error response as a JSON
        throw new ValidationException($validator, response()->json([
            'msg' => 'Validation Errors',
            'error' => $formattedErrors // Send errors as strings instead of arrays
        ], 422));
    }
}
