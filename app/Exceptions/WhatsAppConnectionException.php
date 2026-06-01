<?php

namespace App\Exceptions;

use Exception;

class WhatsAppConnectionException extends Exception
{
    private array $whatsappStatus;

    public function __construct(string $message = "WhatsApp no está conectado.", int $code = 400, array $whatsappStatus = [], ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->whatsappStatus = $whatsappStatus;
    }

    /**
     * Get the WhatsApp connection status details.
     */
    public function getWhatsappStatus(): array
    {
        return $this->whatsappStatus;
    }
}
