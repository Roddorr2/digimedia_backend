<?php

namespace App\Http\Requests\Empleado;

use App\Models\Empleado;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateEmpleadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');
        $empleado = Empleado::where('id_empleado', $id)->first();
        $userId = $empleado?->id_user;

        $emailRules = [
            'sometimes',
            'string',
            'email',
            'max:255',
            Rule::unique('empleados', 'email')->ignore($id, 'id_empleado'),
        ];

        if ($userId !== null) {
            $emailRules[] = Rule::unique('users', 'email')->ignore($userId, 'id');
        } else {
            $emailRules[] = Rule::unique('users', 'email');
        }

        return [
            'id' => 'required|numeric',
            'nombre' => 'sometimes|string|max:255',
            'apellido' => 'sometimes|string|max:255',
            'email' => $emailRules,
            'dni' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('empleados', 'dni')->ignore($id, 'id_empleado'),
            ],
            'telefono' => 'nullable|string|max:20',
            'id_rol' => 'sometimes|exists:roles,id_rol',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.string' => 'Debes ingresar un nombre',
            'apellido.string' => 'Debes ingresar un apellido',
            'email.string' => 'Debes ingresar un email',
            'dni.string' => 'Debes ingresar un DNI',
            'dni.unique' => 'Este número de DNI ya ha sido registrado.',
            'email.unique' => 'Este correo ya está en uso.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()->first()], 422)
        );
    }
}
