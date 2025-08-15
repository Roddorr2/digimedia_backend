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

    public $correo;
    public $data;
    public $idServicio;
    public $tipoCorreo;

    public function __construct($correo, $data, $idServicio, $tipoCorreo)
    {
        $this->correo = $correo;
        $this->data = $data;
        $this->idServicio = $idServicio;
        $this->tipoCorreo = $tipoCorreo;
    }

    public function handle()
    {
        Mail::to($this->correo)->send(
            new MailService($this->tipoCorreo, $this->data, $this->idServicio)
        );
    }
}
