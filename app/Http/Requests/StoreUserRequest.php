<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'integer', Rule::enum(Role::class)],
            'photo' => ['nullable', 'max:3072'],
            'cellphone' => ['nullable', 'string'],
            'document' => ['required', 'string', 'max:255', 'unique:users,document'],
            'status' => ['boolean'],
            'is_approved' => ['boolean'],
            'password' => ['required', 'string', Password::defaults()],
        ];
    }
}
