<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use App\Controller\ProduitByCategorieController;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(operations:[
    new GetCollection(),
    new Post(),
    new Delete(),
    new Patch(),
    new GetCollection(
        uriTemplate:'/produits/by-categorie/{id}',
        controller:ProduitByCategorieController::class,
        normalizationContext: ['groups' => [ 'produit:read']]
    ),
]
)]
#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['produit:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank( message:"Le nom ne peut pas être vide")]
    #[Assert\Length(
        max: 255,
        maxMessage:"Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Groups(['produit:read'])]

    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank( message:"La description ne peut pas être vide")]
    #[Groups(['produit:read'])]

    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank( message:"Le prix ne peut pas être vide")]
    #[Assert\Positive( message:"La prix doit être positif")]
    #[Groups(['produit:read'])]

    private ?float $prix = null;

    #[ORM\Column]
    #[Assert\NotBlank( message:"Le stock ne peut pas être vide.")]
    #[Assert\PositiveOrZero( message: "Le stock doit être un entier positif ou zéro.")]
    #[Groups(['produit:read'])]

    private ?int $stock = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank( message:"L'image ne peut pas être vide")]
    #[Assert\Length(
        max:255,
        maxMessage:"Le chemin de l'image ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Groups(['produit:read'])]
    // #[Assert\Url( message:"Le chemin de l'image doit être une URL valide.")]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Categorie $categorie = null;

    /**
     * @var Collection<int, CommandeProduit>
     */
    #[ORM\OneToMany(targetEntity: CommandeProduit::class, mappedBy: 'produit')]
    private Collection $commandeProduits;

    public function __construct()
    {
        $this->commandeProduits = new ArrayCollection();
    }

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

    //Méthode pour calculter le prix T.T.C
    public function getPrixTTC(): float
    {
        if($this->prix === null)
        {
            return null;
        }
        return $this->prix * (1  + 0.20);
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

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, CommandeProduit>
     */
    public function getCommandeProduits(): Collection
    {
        return $this->commandeProduits;
    }

    public function addCommandeProduit(CommandeProduit $commandeProduit): static
    {
        if (!$this->commandeProduits->contains($commandeProduit)) {
            $this->commandeProduits->add($commandeProduit);
            $commandeProduit->setProduit($this);
        }

        return $this;
    }

    public function removeCommandeProduit(CommandeProduit $commandeProduit): static
    {
        if ($this->commandeProduits->removeElement($commandeProduit)) {
            // set the owning side to null (unless already changed)
            if ($commandeProduit->getProduit() === $this) {
                $commandeProduit->setProduit(null);
            }
        }

        return $this;
    }
}
