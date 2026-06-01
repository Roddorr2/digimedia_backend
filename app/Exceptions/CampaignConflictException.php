<?php

namespace App\Exceptions;

use App\Models\CampaniaWhatsApp;
use Exception;

class CampaignConflictException extends Exception
{
    private ?CampaniaWhatsApp $activeCampaign;

    public function __construct(string $message = "Ya hay una campaña en proceso. Espera a que finalice.", int $code = 409, ?CampaniaWhatsApp $activeCampaign = null, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->activeCampaign = $activeCampaign;
    }

    /**
     * Get the active campaign associated with the conflict.
     */
    public function getActiveCampaign(): ?CampaniaWhatsApp
    {
        return $this->activeCampaign;
    }
}
