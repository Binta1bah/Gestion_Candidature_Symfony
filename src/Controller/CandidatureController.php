<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Candidature;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api', name: 'api_')]
class CandidatureController extends AbstractController
{
    #[Route('/candidature', name: 'app_candidature')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CandidatureController.php',
        ]);
    }

    #[Route('/candidature/new/{formationId}', name: 'candidature_new', methods: "POST")]
    public function new(ManagerRegistry $doctrine, Request $request, Security $security, $formationId): JsonResponse
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $user = $security->getUser();

        $formation = $em->getRepository(Formation::class)->find($formationId);

        if (!$formation) {
            return $this->json([
                'error' => 'Formation non trouvée',
            ]);
        }

        $candidature = new Candidature();
        $candidature->setUser($user);
        $candidature->setFormation($formation);
        $candidature->setEtat('Attente');


        $em->persist($candidature);
        $em->flush();

        return $this->json([
            'message' => 'Candidature ajoutée',
            'data' => [
                'id' => $candidature->getId(),
                'user' => [
                    'id' => $user->getId(),
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),

                ],
                'formation' => [
                    'id' => $formation->getId(),
                    'libelle' => $formation->getLibelle(),
                    'durée' => $formation->getDuree(),
                ],
                'etat' => $candidature->getEtat(),
            ]
        ]);
    }

    #[Route('/candidature/accepter/{id}', name: 'candidature_accepter', methods: "PUT")]
    public function accepter($id, ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        // Vérifier si l'utilisateur a le rôle requis
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé. Vous n\'avez pas les autorisations nécessaires.');
        }

        $em = $doctrine->getManager();
        $candidaturesRepository = $em->getRepository(Candidature::class);
        $candidature = $candidaturesRepository->find($id);

        if (!$candidature) {
            throw $this->createNotFoundException('candidature non trouvée avec l\'identifiant ' . $id);
        }

        // Appliquer les modifications à la candidature
        $candidature->setEtat('Accepter');

        // Sauvegarder les modifications
        $em->flush();

        return $this->json([
            'message' => 'candidature acceptée',
        ]);
    }

    #[Route('/candidature/refuser/{id}', name: 'candidature_accepter', methods: "PUT")]
    #[IsGranted('ROLE_ADMIN', message: 'Accès refusé. Vous n\'avez pas les autorisations nécessaires.')]
    public function refuser($id, ManagerRegistry $doctrine): JsonResponse
    {


        $em = $doctrine->getManager();
        $candidaturesRepository = $em->getRepository(Candidature::class);
        $candidature = $candidaturesRepository->find($id);

        if (!$candidature) {
            throw $this->createNotFoundException('candidature non trouvée avec l\'identifiant ' . $id);
        }

        // Appliquer les modifications à la candidature
        $candidature->setEtat('Refuser');

        // Sauvegarder les modifications
        $em->flush();

        return $this->json([
            'message' => 'candidature refusée',
        ]);
    }

    #[Route('/candidatures/acceptees', name: 'candidatures_acceptees', methods: ['GET'])]
    public function CandidaturesAcceptees(ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();

        // Récupérer les candidatures acceptées depuis la base de données
        $candidatures = $em->getRepository(Candidature::class)->findBy(['etat' => 'Accepter']);
        $nombreCandidatureAcceptees = count($candidatures);

        $data = [];
        foreach ($candidatures as $candidature) {
            $data[] = [
                'id' => $candidature->getId(),
                'user' => [
                    'username' => $candidature->getUser()->getPrenom(),
                    // Ajoutez d'autres propriétés utilisateur que vous souhaitez inclure
                ],
                'formation' => [
                    'libelle' => $candidature->getFormation()->getLibelle(),
                    // Ajoutez d'autres propriétés de formation que vous souhaitez inclure
                ],
                'etat' => $candidature->getEtat(),
            ];
        }

        // Renvoyer la réponse JSON
        return $this->json([
            'message' => 'Liste des candidatures acceptées',
            'Nombre' => $nombreCandidatureAcceptees,
            'data' => $data,
        ]);
    }

    #[Route('/candidatures/refusees', name: 'candidatures_refusees', methods: ['GET'])]
    public function CandidaturesRefusees(ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();

        // Récupérer les candidatures acceptées depuis la base de données
        $candidatures = $em->getRepository(Candidature::class)->findBy(['etat' => 'Refuser']);
        $nombreCandidatureAcceptees = count($candidatures);

        $data = [];
        foreach ($candidatures as $candidature) {
            $data[] = [
                'id' => $candidature->getId(),
                'user' => [
                    'username' => $candidature->getUser()->getPrenom(),
                    // Ajoutez d'autres propriétés utilisateur que vous souhaitez inclure
                ],
                'formation' => [
                    'libelle' => $candidature->getFormation()->getLibelle(),
                    // Ajoutez d'autres propriétés de formation que vous souhaitez inclure
                ],
                'etat' => $candidature->getEtat(),
            ];
        }

        // Renvoyer la réponse JSON
        return $this->json([
            'message' => 'Liste des candidatures refusées',
            'Nombre' => $nombreCandidatureAcceptees,
            'data' => $data,
        ]);
    }
}
