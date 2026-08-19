<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'currentPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Custom localized messages.
     */
    public function messages(): array
    {
        return [
            'currentPassword.required' => __('Please enter your current password.'),
            'newPassword.required' => __('Please enter a new password.'),
            'newPassword.min' => __('New password must be at least 8 characters long.'),
            'newPassword.confirmed' => __('Password confirmation does not match.'),
        ];
    }
}
