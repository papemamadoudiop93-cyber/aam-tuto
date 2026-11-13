<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Formation;
use App\Form\InscriptionType;
use App\Repository\FormationRepository;
use App\Service\InscriptionImpossibleException;
use App\Service\InscriptionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FormationController extends AbstractController
{
    #[Route('/', name: 'formation_index', methods: ['GET'])]
    public function index(
        FormationRepository $formations,
        #[Autowire(param: 'app.places_alerte')] int $seuilAlerte,
    ): Response {
        return $this->render('formation/index.html.twig', [
            'formations' => $formations->findAVenir(),
            'seuil_alerte' => $seuilAlerte,
        ]);
    }

    // {id} est converti automatiquement en entité Formation (404 si introuvable).
    #[Route('/formations/{id}', name: 'formation_show', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function show(Formation $formation, Request $request, InscriptionManager $manager): Response
    {
        $form = $this->createForm(InscriptionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agent = $this->getUser();
            if (!$agent instanceof Agent) {
                return $this->redirectToRoute('app_login');
            }

            try {
                $manager->inscrire($formation, $agent, $form->get('motivation')->getData());
                $this->addFlash('success', 'Votre demande a été enregistrée.');

                return $this->redirectToRoute('inscription_mes');
            } catch (InscriptionImpossibleException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }
}
