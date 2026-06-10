<?php

namespace App\Jobs;

use App\Mail\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCustomEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public $correo,
        public $data,
        public $idServicio,
        public $tipoCorreo,
        public ?int $idSubservicio = null,
    ) {}

    public function handle(): void
    {
        Mail::to($this->correo)->send(
            new MailService($this->tipoCorreo, $this->data, $this->idServicio, $this->idSubservicio)
        );
    }
}
