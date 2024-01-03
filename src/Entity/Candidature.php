<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Put;
use App\Controller\CandidatureController;
use App\Repository\CandidatureRepository;

#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            name: 'candidatures_acceptees',
            uriTemplate: 'candidatures/acceptees',
            controller: CandidatureController::class . '::CandidaturesAcceptees',
            description: 'Cet endpoint permet de recuperer les candidatures acceptées ',
            read: false,
            deserialize: false
        ),

        new Get(
            name: 'candidatures_refusees',
            uriTemplate: 'candidatures/refusees',
            controller: CandidatureController::class . '::CandidaturesRefusees',
            description: 'Cet endpoint permet de recuperer les candidatures refusées ',
            read: false,
            deserialize: false
        ),
        new Post(
            name: 'candidature_new',
            uriTemplate: '/candidature/new/{formationId}',
            controller: CandidatureController::class . '::new',
            description: 'Cet endpoint permet d\'ajouter une candidature pour une formations données',
            read: false,
            deserialize: false
        ),
        new Put(
            name: 'candidature_accepter',
            uriTemplate: '/candidature/accepter/{id}',
            controller: CandidatureController::class . '::accepter',
            description: 'Cet endpoint permet d\'accepter une candidature',
            read: false,
            deserialize: false
        ),
        new Post(
            name: 'candidature_refuser',
            uriTemplate: '/candidature/refuser/{id}',
            controller: CandidatureController::class . '::refuser',
            description: 'Cet endpoint permet de refuser une candidature',
            read: false,
            deserialize: false
        )
    ]
)]

class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'candidatures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'candidatures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formation $Formation = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->Formation;
    }

    public function setFormation(?Formation $Formation): static
    {
        $this->Formation = $Formation;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}
