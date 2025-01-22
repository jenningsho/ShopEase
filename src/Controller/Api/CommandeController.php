<?php

namespace App\Controller\Api;

use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController
{
    #[Route('/api/commande', name: 'api_commandes')]

    
    private $entityManager;


        
        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;
        }
    
        #[Route('/create', name: 'create', methods: ['POST'])]
        public function create(Request $request, ProduitRepository $produitRepository): JsonResponse
        {
            $data = json_decode($request->getContent(), true);
    
            // Valider les données
            if (!isset($data['utilisateur_id'], $data['produits'])) {
                return new JsonResponse(['error' => 'Données invalides'], 400);
            }
    
            // Créer la commande
            $commande = new Commande();
            $commande->setUtilisateur($data['utilisateur_id']);
            $commande->setStatut('en attente');
            $commande->setCreatedAt(new \DateTimeImmutable());
            $commande->setUpdatedAt(new \DateTimeImmutable());
    
            $this->entityManager->persist($commande);
    
            // Ajouter les produits à la commande
            foreach ($data['produits'] as $produitData) {
                $produit = $produitRepository->find($produitData['id']);
                if (!$produit) {
                    return new JsonResponse(['error' => "Produit ID {$produitData['id']} non trouvé"], 404);
                }
    
                $commandeProduit = new CommandeProduit();
                $commandeProduit->setCommande($commande);
                $commandeProduit->setProduit($produit);
                $commandeProduit->setQuantite($produitData['quantite']);
                $commandeProduit->setPrixUnitaire($produit->getPrix());
    
                $this->entityManager->persist($commandeProduit);
            }
    
            $this->entityManager->flush();
    
            return new JsonResponse(['message' => 'Commande enregistrée avec succès', 'commande_id' => $commande->getId()], 201);
        }

        #[Route('/{utilisateur_id}', name: 'get', methods: ['GET'])]
        public function getByUser(int $utilisateur_id): JsonResponse
        {
            $commandes = $this->entityManager->getRepository(Commande::class)->findBy(['utilisateurId' => $utilisateur_id]);

            $result = [];
            foreach ($commandes as $commande) {
                $produits = [];
                foreach ($commande->getCommandeProduits() as $commandeProduit) {
                    $produits[] = [
                        'id' => $commandeProduit->getProduit()->getId(),
                        'nom' => $commandeProduit->getProduit()->getNom(),
                        'quantite' => $commandeProduit->getQuantite(),
                        'prix_unitaire' => $commandeProduit->getPrixUnitaire(),
                    ];
                }

                $result[] = [
                    'id' => $commande->getId(),
                    'statut' => $commande->getStatut(),
                    'created_at' => $commande->getCreatedAt(),
                    'produits' => $produits,
                ];
            }

            return new JsonResponse($result, 200);
        }
    }
