<?php

namespace App\DTOs\PopupConfig;

use Illuminate\Http\Request;

class UpdatePopupConfigDTO
{
    public function __construct(
        public readonly array $data,
        public readonly ?int $id_servicio,
        public readonly ?int $id_subservicio,
        public readonly int $userId
    ) {}

    public static function fromRequest(Request $request): self
    {
        $fields = [
            'button_text', 'button_color', 'service_color', 'service_color_2',
            'gradient_direction', 'trigger_time', 'trigger_type', 'layout',
            'left_text', 'left_opacity', 'right_opacity', 'mobile_opacity',
            'left_alt', 'right_alt', 'mobile_alt',
            'remove_left_image', 'remove_right_image', 'remove_mobile_image'
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
            id_servicio: $request->input('id_servicio') ? (int) $request->input('id_servicio') : null,
            id_subservicio: $request->input('id_subservicio') ? (int) $request->input('id_subservicio') : null,
            userId: (int) $request->user()->id
        );
    }
}
