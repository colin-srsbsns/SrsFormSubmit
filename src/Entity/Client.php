<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 64)]
    private ?string $jwtSecret = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $recipient = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, FormSubmission>
     */
    #[ORM\OneToMany(targetEntity: FormSubmission::class, mappedBy: 'client', orphanRemoval: true)]
    private Collection $formSubmissions;

    public function __construct()
    {
        $this->formSubmissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getJwtSecret(): ?string
    {
        return $this->jwtSecret;
    }

    public function setJwtSecret(string $jwtSecret): static
    {
        $this->jwtSecret = $jwtSecret;

        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(?string $recipient): static
    {
        $this->recipient = $recipient;

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

    /**
     * @return Collection<int, FormSubmission>
     */
    public function getFormSubmissions(): Collection
    {
        return $this->formSubmissions;
    }

    public function addFormSubmission(FormSubmission $formSubmission): static
    {
        if (!$this->formSubmissions->contains($formSubmission)) {
            $this->formSubmissions->add($formSubmission);
            $formSubmission->setClient($this);
        }

        return $this;
    }

    public function removeFormSubmission(FormSubmission $formSubmission): static
    {
        if ($this->formSubmissions->removeElement($formSubmission)) {
            // set the owning side to null (unless already changed)
            if ($formSubmission->getClient() === $this) {
                $formSubmission->setClient(null);
            }
        }

        return $this;
    }
}
