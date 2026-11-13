<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Repository\InscriptionRepository;
use App\Service\InscriptionImpossibleException;
use App\Service\InscriptionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/gestion')]
#[IsGranted('ROLE_GESTIONNAIRE')]
final class GestionController extends AbstractController
{
    #[Route('', name: 'gestion_index', methods: ['GET'])]
    public function index(InscriptionRepository $inscriptions): Response
    {
        return $this->render('gestion/index.html.twig', [
            'inscriptions' => $inscriptions->findEnAttente(),
        ]);
    }

    #[Route('/inscriptions/{id}/{decision}', name: 'gestion_decider', requirements: ['decision' => 'valider|refuser'], methods: ['POST'])]
    public function decider(Inscription $inscription, string $decision, Request $request, InscriptionManager $manager): Response
    {
        if (!$this->isCsrfTokenValid('decider'.$inscription->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        try {
            'valider' === $decision ? $manager->valider($inscription) : $manager->refuser($inscription);
            $this->addFlash('success', 'valider' === $decision ? 'Demande validée.' : 'Demande refusée.');
        } catch (InscriptionImpossibleException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('gestion_index');
    }
}
