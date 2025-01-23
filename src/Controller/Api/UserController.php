<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;


    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator
    ){
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

    }

    #[Route('/api/user/update', name: 'api_user_update' , methods:['PUT', 'PATCH'])]
    public function update(Request $request, ValidatorInterface $validator): Response
    {
        // Récupération de l'utilisateur authentifié
        $user = $this->getUser();

        if(!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        // récuperer les données json
        $data = json_decode($request->getContent(), true);

        // mise à jour des champs si il existe
        if(isset($data['email'])){
            $user->setEmail($data['email']);
        }
        if(isset($data['nom'])){
            $user->setNom($data['nom']);
        }
        if(isset($data['password'])){
            // Mot de passe temporaire avant le hashage
            $user->setPassword($data['password']);
        }
        
         // Validator pour valider les données de l'utilisateur
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        // Hashage du mot de passe 
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword); // On Set le mot de passe hashé

        // Sauvegarde les modification de l'utilisateur dans la BDD
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json( [
            'message' => 'Utilisateur mis à jour',
            'user' => [
                'email' => $user->getEmail(),
                'nom' => $user->getNom()
            ]
        ]);
    }
}
