<?php
namespace App\Http\Requests\Testimonio;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestimonioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|required|string|max:255',
            'cargo'  => 'nullable|string|max:255',
            'texto'  => 'sometimes|required|string',
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'fecha_testimonio' => 'nullable|date',
        ];
    }
}