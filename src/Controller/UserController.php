<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/register', name: 'register', methods: 'post')]
    public function register(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent());
        $email = $data->email;

        $plaintextPassword = $data->password;
        $nom = $data->nom;
        $prenom = $data->prenom;
        $telephone = $data->telephone;


        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setNom($email);
        $user->setnom($nom);
        $user->setPrenom($prenom);
        $user->setTelephone($telephone);
        $user->setRoles(['ROLE_USER']);
        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'Inscription effectuée',
            'data' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'telephone' => $user->getTelephone(),
            ]
        ]);
    }

    // #[Route('/logout', name: 'logout', methods: 'post')]
    // public function logout(Request $request, Security $security)
    // {
    //     // Notez que cette méthode peut être vide car elle ne sera pas utilisée.
    //     // Symfony interceptera la demande avant qu'elle n'atteigne cette méthode.

    //     // Vous pouvez ajouter du code ici si nécessaire, par exemple, pour déconnecter
    //     // l'utilisateur de la session Symfony.

    //     return new JsonResponse(['message' => 'Déconnexion réussie.']);
    // }
}
