<?php

namespace App\Http\Requests\Empleado;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GenerateEmpleadoUploadSignatureRequest extends FormRequest
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
            'timestamp' => [
                'required',
                'numeric',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (abs(time() - (int) $value) > 120) {
                        $fail('Firma expirada, por favor reintente');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'timestamp.required' => 'Timestamp requerido',
            'timestamp.numeric' => 'Timestamp requerido',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ], 422)
        );
    }
}
