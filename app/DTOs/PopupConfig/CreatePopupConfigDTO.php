<?php

namespace App\DTOs\PopupConfig;

use Illuminate\Http\Request;

class CreatePopupConfigDTO
{
    public function __construct(
        public readonly ?int $id_servicio,
        public readonly ?int $id_subservicio,
        public readonly string $button_text,
        public readonly ?string $button_color,
        public readonly string $service_color,
        public readonly ?string $service_color_2,
        public readonly ?string $gradient_direction,
        public readonly int $trigger_time,
        public readonly ?string $trigger_type,
        public readonly ?string $layout,
        public readonly bool $show_logo,
        public readonly ?string $left_text,
        public readonly ?int $left_opacity,
        public readonly ?string $left_alt,
        public readonly ?int $right_opacity,
        public readonly ?string $right_alt,
        public readonly ?int $mobile_opacity,
        public readonly ?string $mobile_alt,
        public readonly int $userId
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            id_servicio: $request->input('id_servicio') ? (int) $request->input('id_servicio') : null,
            id_subservicio: $request->input('id_subservicio') ? (int) $request->input('id_subservicio') : null,
            button_text: $request->input('button_text'),
            button_color: $request->input('button_color', '#7C3FD9'),
            service_color: $request->input('service_color'),
            service_color_2: $request->input('service_color_2'),
            gradient_direction: $request->input('gradient_direction', 'to bottom'),
            trigger_time: (int) $request->input('trigger_time'),
            trigger_type: $request->input('trigger_type', 'time'),
            layout: $request->input('layout', 'left-image'),
            show_logo: filter_var($request->input('show_logo'), FILTER_VALIDATE_BOOLEAN) ?? true,
            left_text: $request->input('left_text', ''),
            left_opacity: $request->input('left_opacity') !== null ? (int) $request->input('left_opacity') : 100,
            left_alt: $request->input('left_alt', ''),
            right_opacity: $request->input('right_opacity') !== null ? (int) $request->input('right_opacity') : 100,
            right_alt: $request->input('right_alt', ''),
            mobile_opacity: $request->input('mobile_opacity') !== null ? (int) $request->input('mobile_opacity') : 100,
            mobile_alt: $request->input('mobile_alt', ''),
            userId: (int) $request->user()->id
        );
    }
}
