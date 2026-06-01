<?php

namespace App\Http\Requests\ConfiguracionTiempo;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfiguracionTiempoRequest extends FormRequest
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
            'tipo' => 'required|in:email,whatsapp',
            'numero_mensaje' => 'required|integer|min:1',
            'unidad_tiempo' => 'required|in:minutos,horas,dias',
            'valor_tiempo' => 'required|integer|min:0',
        ];
    }
}
