<?php

namespace App\Security\Voter;

use App\Entity\Agent;
use App\Entity\Inscription;
use App\Entity\InscriptionStatut;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Contrôle d'accès fin, objet par objet.
 * Usage : $this->denyAccessUnlessGranted(InscriptionVoter::ANNULER, $inscription)
 *         ou en Twig : is_granted('INSCRIPTION_ANNULER', inscription)
 *
 * @extends Voter<string, Inscription>
 */
final class InscriptionVoter extends Voter
{
    public const VOIR = 'INSCRIPTION_VOIR';
    public const ANNULER = 'INSCRIPTION_ANNULER';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::VOIR, self::ANNULER], true) && $subject instanceof Inscription;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof Agent) {
            return false;
        }

        $estProprietaire = $subject->getAgent()->getId() === $user->getId();

        return match ($attribute) {
            self::VOIR => $estProprietaire || $this->security->isGranted('ROLE_GESTIONNAIRE'),
            self::ANNULER => $estProprietaire && InscriptionStatut::EnAttente === $subject->getStatut(),
            default => false,
        };
    }
}
