<?php

namespace App\Entity;

use App\Repository\ClientSiteKeyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientSiteKeyRepository::class)]
class ClientSiteKey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $siteKey = null;

    #[ORM\ManyToOne(inversedBy: 'clientSiteKeys')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $Client = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteKey(): ?string
    {
        return $this->siteKey;
    }

    public function setSiteKey(string $siteKey): static
    {
        $this->siteKey = $siteKey;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->Client;
    }

    public function setClient(?Client $Client): static
    {
        $this->Client = $Client;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
