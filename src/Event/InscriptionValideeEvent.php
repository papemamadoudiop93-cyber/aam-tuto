<?php

namespace App\Event;

use App\Entity\Inscription;

final class InscriptionValideeEvent
{
    public function __construct(public readonly Inscription $inscription)
    {
    }
}
