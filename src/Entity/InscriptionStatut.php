<?php

namespace App\Entity;

/** Enum PHP 8.1 mappée directement par Doctrine (colonne texte). */
enum InscriptionStatut: string
{
    case EnAttente = 'en_attente';
    case Validee = 'validee';
    case Refusee = 'refusee';

    public function libelle(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Validee => 'Validée',
            self::Refusee => 'Refusée',
        };
    }

    /** Classe du badge DSFR correspondant. */
    public function badge(): string
    {
        return match ($this) {
            self::EnAttente => 'fr-badge--info',
            self::Validee => 'fr-badge--success',
            self::Refusee => 'fr-badge--error',
        };
    }
}
