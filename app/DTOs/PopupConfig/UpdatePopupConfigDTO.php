<?php

namespace App\DTOs\PopupConfig;

use Illuminate\Http\Request;

class UpdatePopupConfigDTO
{
    public function __construct(
        public readonly array $data,
        public readonly int $userId
    ) {}

    public static function fromRequest(Request $request): self
    {
        $fields = [
            'button_text', 'button_color', 'service_color', 'service_color_2', 
            'gradient_direction', 'trigger_time', 'trigger_type', 'layout', 
            'left_text', 'left_opacity', 'right_opacity', 'mobile_opacity',
            'left_alt', 'right_alt', 'mobile_alt'
        ];
        
        $data = [];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field);
            }
        }

        if ($request->has('show_logo')) {
            $data['show_logo'] = filter_var($request->input('show_logo'), FILTER_VALIDATE_BOOLEAN);
        }

        return new self(
            data: $data,
            userId: (int) $request->user()->id
        );
    }
}
