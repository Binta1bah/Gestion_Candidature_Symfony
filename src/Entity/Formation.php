<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Flex\Unpack\Operation;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Controller\FormationController;
use App\Repository\FormationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            name: 'liste_formation',
            uriTemplate: '/formations',
            controller: FormationController::class . '::index',
            description: 'Cet endpoint permet de listes les formations',
            read: false,
            deserialize: false
        ),
        new Post(
            name: 'formation_new',
            uriTemplate: 'formation/new',
            controller: FormationController::class . '::new',
            description: 'Cet endpoint permet d\'ajouter une formation',
            read: false,
            deserialize: false
        ),
        new Put(
            name: 'formation_update',
            uriTemplate: '/formation/update/{id}',
            controller: FormationController::class . '::update',
            description: 'Cet endpoint permet de modifier une formation',
            read: false,
            deserialize: false
        ),
        new Delete(
            name: 'formation_delete',
            uriTemplate: '/formation/delete/{id}',
            controller: FormationController::class . '::delete',
            description: 'Cet endpoint permet de supprimer une formation',
            read: false,
            deserialize: false
        ),

    ]
)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $libelle;

    #[ORM\Column(length: 255)]
    private string $description;

    #[ORM\Column(length: 255)]
    private string $duree;

    #[ORM\Column]
    private bool $is_clotured;

    #[ORM\Column]
    private \DateTimeImmutable $created_at;

    #[ORM\OneToMany(mappedBy: 'Formation', targetEntity: Candidature::class)]
    private Collection $candidatures;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->is_clotured = 0;
        $this->candidatures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function isIsClotured(): ?bool
    {
        return $this->is_clotured;
    }

    public function setIsClotured(bool $is_clotured): static
    {
        $this->is_clotured = $is_clotured;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Candidature>
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): static
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures->add($candidature);
            $candidature->setFormation($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): static
    {
        if ($this->candidatures->removeElement($candidature)) {
            // set the owning side to null (unless already changed)
            if ($candidature->getFormation() === $this) {
                $candidature->setFormation(null);
            }
        }

        return $this;
    }
}
