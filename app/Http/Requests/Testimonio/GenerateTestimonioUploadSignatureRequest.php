<?php
namespace App\Http\Requests\Testimonio;

use Illuminate\Foundation\Http\FormRequest;

class GenerateTestimonioUploadSignatureRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'timestamp' => 'required',
        ];
    }
}