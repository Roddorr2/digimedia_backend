<?php
namespace App\Http\Requests\Testimonio;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestimonioImageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'public_id'  => 'required|string',
            'secure_url' => 'required|url',
        ];
    }
}