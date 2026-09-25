<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CredencialesEmpleadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }
// HOLA IZAID QUIERO KEKE
    public function build()
    {
        return $this->subject('Tus credenciales de acceso')
                    ->view('mails.credenciales')
                    ->with([
                        'nombre' => $this->user->name,
                        'email' => $this->user->email,
                        'password' => $this->password
                    ]);
    }
}
