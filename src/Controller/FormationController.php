<?php

namespace App\Controller;

use App\Entity\Formation;
use Symfony\Flex\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class FormationController extends AbstractController
{
    // #[Route('/formations', name: 'app_formation')]
    // public function index(): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/FormationController.php',
    //     ]);
    // }
    #[Route('/formations', name: 'liste_formation', methods: "GET")]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();
        $formationsRepository = $em->getRepository(Formation::class);
        $formations = $formationsRepository->findAll();

        $formattedFormations = [];

        foreach ($formations as $formation) {
            $formattedFormations[] = [
                'id' => $formation->getId(),
                'libelle' => $formation->getLibelle(),
                'description' => $formation->getDescription(),
                'durée' => $formation->getDuree(),
                'Date_ajout' => $formation->getCreatedAt(),
            ];
        }

        return $this->json([
            'message' => 'Lisste des formations',
            'data' => $formattedFormations,
        ]);
    }


    #[Route('/formation/new', name: 'formation_new', methods: "POST")]
    public function new(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $formation = new Formation();
        $formation->setLibelle($data['libelle']);
        $formation->setDescription($data['description']);
        $formation->setDuree($data['duree']);

        $em->persist($formation);
        $em->flush();

        return $this->json([
            'message' => 'Formation ajouter',
            'data' => [
                'id' => $formation->getId(),
                'libelle' => $formation->getLibelle(),
                'description' => $formation->getDescription(),
                'durée' => $formation->getDuree(),

            ]
        ]);
    }

    #[Route('/formation/update/{id}', name: 'formation_update', methods: "PUT")]
    public function update($id, ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $em = $doctrine->getManager();
        $formationsRepository = $em->getRepository(Formation::class);
        $formation = $formationsRepository->find($id);

        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée avec l\'identifiant ' . $id);
        }

        // if ($request->isMethod('PUT')) {
        // Récupérer les données du formulaire
        $data = json_decode($request->getContent(), true);

        // Appliquer les modifications à la formation
        $formation->setLibelle($data['libelle']);
        $formation->setDescription($data['description']);
        $formation->setDuree($data['duree']);

        // Sauvegarder les modifications
        $em->flush();

        return $this->json([
            'message' => 'Formation modifiée',
            'data' => [
                'id' => $formation->getId(),
                'libelle' => $formation->getLibelle(),
                'description' => $formation->getDescription(),
                'durée' => $formation->getDuree(),
            ]
        ]);
        // }

        // Si la requête n'est pas de type PUT, vous pouvez retourner une réponse d'erreur ou une redirection, selon votre logique d'application.
        // return $this->json([
        //     'error' => 'Méthode non autorisée',
        // ], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * @Route("/formations/{id}", name="formation_show", methods={"GET"})
     */
    public function show($id): Response
    {
        // Logique pour afficher une formation spécifique
    }

    /**
     * @Route("/formations/{id}/edit", name="formation_edit", methods={"GET","POST"})
     */
    public function edit($id): Response
    {
        // Logique pour éditer une formation existante
    }

    /**
     * @Route("/formations/{id}", name="formation_delete", methods={"DELETE"})
     */
    #[Route('/formation/delete/{id}', name: 'formation_delete', methods: ['DELETE'])]
    public function delete($id, ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();
        $formationsRepository = $em->getRepository(Formation::class);
        $formation = $formationsRepository->find($id);

        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée avec l\'identifiant ' . $id);
        }

        $em->remove($formation);
        $em->flush();

        return $this->json([
            'message' => 'Formation supprimée avec succès',
        ]);
    }
}
