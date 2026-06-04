<?php

namespace App\Http\Requests\ConfiguracionTiempo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfiguracionTiempoRequest extends FormRequest
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
            'email' => 'required|array',
            'email.*.numero_mensaje' => 'required|integer|min:1',
            'email.*.unidad_tiempo' => 'required|in:minutos,horas,dias',
            'email.*.valor_tiempo' => 'required|integer|min:0',
            'whatsapp' => 'required|array',
            'whatsapp.*.numero_mensaje' => 'required|integer|min:1',
            'whatsapp.*.unidad_tiempo' => 'required|in:minutos,horas,dias',
            'whatsapp.*.valor_tiempo' => 'required|integer|min:0',
        ];
    }
}
