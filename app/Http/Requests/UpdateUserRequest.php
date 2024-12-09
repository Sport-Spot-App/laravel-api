<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->input('user_id');
        return [
            'name' => ['string', 'max:255', 'required'],
            'email' => ['email', 'max:255', 'required', Rule::unique('users')->ignore($userId)],
            'role' => ['required', Rule::enum(Role::class)],
            'photo' => ['nullable', 'max:3072'],
            'cellphone' => ['min:8','string','required'],
            'document' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($userId)],
            'status' => ['boolean'],
            'is_approved' => ['boolean'],
        ];
    }
}