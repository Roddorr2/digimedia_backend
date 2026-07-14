<?php
namespace App\Http\Requests\Testimonio;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'cargo'  => 'nullable|string|max:255',
            'texto'  => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'fecha_testimonio' => 'nullable|date',
        ];
    }
}