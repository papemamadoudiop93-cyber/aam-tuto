<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Inscription;
use App\Repository\InscriptionRepository;
use App\Security\Voter\InscriptionVoter;
use App\Service\InscriptionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class InscriptionController extends AbstractController
{
    #[Route('/mes-inscriptions', name: 'inscription_mes', methods: ['GET'])]
    public function mesInscriptions(#[CurrentUser] Agent $agent, InscriptionRepository $inscriptions): Response
    {
        return $this->render('inscription/mes.html.twig', [
            'inscriptions' => $inscriptions->findPourAgent($agent),
        ]);
    }

    #[Route('/mes-inscriptions/{id}/annuler', name: 'inscription_annuler', methods: ['POST'])]
    #[IsGranted(InscriptionVoter::ANNULER, subject: 'inscription')]
    public function annuler(Inscription $inscription, Request $request, InscriptionManager $manager): Response
    {
        if (!$this->isCsrfTokenValid('annuler'.$inscription->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $manager->annuler($inscription);
        $this->addFlash('success', 'Votre demande a été annulée.');

        return $this->redirectToRoute('inscription_mes');
    }
}
