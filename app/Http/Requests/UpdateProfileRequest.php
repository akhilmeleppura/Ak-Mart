<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $userId = auth()->id();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'locale' => ['nullable', 'string', 'in:en,ml,hi,ar,fr,de,ta,kn,it'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'town' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'post_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'marketing_opt_out' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom localized messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Name is required.'),
            'email.required' => __('Email address is required.'),
            'email.email' => __('Please enter a valid email address.'),
            'email.unique' => __('This email address is already registered.'),
            'locale.in' => __('The selected language is not supported.'),
        ];
    }
}
