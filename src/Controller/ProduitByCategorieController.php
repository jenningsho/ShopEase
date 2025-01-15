<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;


class ProduitByCategorieController
{
    private ProduitRepository $produitRepository;
    private SerializerInterface $serializer;


    public function __construct(ProduitRepository $produitRepository, SerializerInterface $serializer)
    {
        $this->produitRepository = $produitRepository;
        $this->serializer = $serializer;

    }

    public function __invoke(int $id): JsonResponse
    {
        // récupere les produits de la catégorie
        $produits = $this->produitRepository->findByCategorie($id);

        // Retourne une erreur si on n'a pas trouvé cette catégorie
        if (empty($produits)) {
            return new JsonResponse(['message' => 'Catégorie introuvable'], 404);
        }

        // serialise les produits avec les groupes 'produit:read'
        $data = $this->serializer->normalize($produits, null, ['groups' => 'produit:read']);
        // retourne une réponse JSON et laisse symfony gérer la sérialisation
        return new JsonResponse($data, 200);
    }
}
