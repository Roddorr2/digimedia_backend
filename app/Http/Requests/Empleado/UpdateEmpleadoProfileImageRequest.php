<?php

namespace App\Http\Requests\Empleado;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmpleadoProfileImageRequest extends FormRequest
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
            'public_id' => 'required|string',
            'secure_url' => [
                'required',
                'url',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (parse_url((string) $value, PHP_URL_HOST) !== 'res.cloudinary.com') {
                        $fail('La URL de imagen no pertenece a un dominio autorizado.');
                    }
                },
            ],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 422,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
