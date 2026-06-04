<?php

namespace App\Http\Requests\Metricas;

class ListCardsByPlantillaRequest extends MonthYearRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'id_plantilla' => ['required', 'integer', 'min:1'],
        ]);
    }
}
