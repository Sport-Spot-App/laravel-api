<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourtRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:3000',
            'zip_code' => 'required|string',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:10',
            'logradouro' => 'nullable|string',
            'complemento' => 'nullable|string',
            'bairro' => 'nullable|string',
            'localidade' => 'nullable|string',
            'estado' => 'nullable|string',
            'coordinate_x' => 'nullable|string',
            'coordinate_y' => 'nullable|string',
            'price_per_hour' => 'required|string',
            'sports' => 'nullable',
            'photos' => 'nullable',
            'photos.*' => 'image|mimes:jpeg,png,jpg',
            'work_days' => 'required',
            'initial_hour' => 'required|string',
            'final_hour' => 'required|string',
        ];
    }
}
