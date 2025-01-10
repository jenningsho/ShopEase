<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProduitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ApiResource()]
#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank( message:"Le nom ne peut pas être vide")]
    #[Assert\Length(
        max: 255,
        maxMessage:"Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank( message:"La description ne peut pas être vide")]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank( message:"Le prix ne peut pas être vide")]
    #[Assert\Positive( message:"La prix doit être positif")]
    private ?float $prix = null;

    #[ORM\Column]
    #[Assert\NotBlank( message:"Le stock ne peut pas être vide.")]
    #[Assert\PositiveOrZero( message: "Le stock doit être un entier positif ou zéro.")]
    private ?int $stock = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank( message:"L'image ne peut pas être vide")]
    #[Assert\Length(
        max:255,
        maxMessage:"Le chemin de l'image ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Url( message:"Le chemin de l'image doit être une URL valide.")]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[Assert\NotNull( message: "La catégorie est obligatoire.")]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Categorie $categorie_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }

    public function getCategorieId(): ?Categorie
    {
        return $this->categorie_id;
    }

    public function setCategorieId(?Categorie $categorie_id): static
    {
        $this->categorie_id = $categorie_id;

        return $this;
    }
}
