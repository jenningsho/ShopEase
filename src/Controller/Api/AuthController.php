<?php 

// src/Controller/Api/AuthController.php
namespace App\Controller\Api;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class AuthController extends AbstractController
{
    private $passwordHasher;
    private $jwtManager;
    private $entityManager;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        EntityManagerInterface $entityManager // Injection de l'EntityManager
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
        $this->entityManager = $entityManager; // Initialisation de l'EntityManager
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        // Récupérer les données envoyées
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['error' => 'L\adresse email et le mot de passe sont requisent'], Response::HTTP_BAD_REQUEST);
        }

        // Chercher l'utilisateur par email via l'EntityManager
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return $this->json(['error' => 'Aucune utilisateur avec cette adresse mail.'], Response::HTTP_UNAUTHORIZED);
        }

        // Vérifier si le mot de passe est valide
        if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['error' => 'Mot de passe incorrect. Veuilez re-essayer.'], Response::HTTP_UNAUTHORIZED);
        }

        // Générer le token JWT
        $token = $this->jwtManager->create($user);

        // Retourner le token JWT
        return $this->json(['token' => $token]);
    }
}
