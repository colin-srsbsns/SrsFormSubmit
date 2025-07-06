<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client implements UserInterface, \Serializable
{
    #[ORM\Id]
    #[ORM\Column(type: 'string',length: 26, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private ?string $id = null;

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

    /**
     * @var Collection<int, ClientSiteKey>
     */
    #[ORM\OneToMany(targetEntity: ClientSiteKey::class, mappedBy: 'Client', orphanRemoval: true)]
    private Collection $clientSiteKeys;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $timezone = null;

    public function __construct()
    {
        $this->formSubmissions = new ArrayCollection();
        $this->clientSiteKeys = new ArrayCollection();
    }

    public function getId(): ?string
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
    /* ---------------- JWTUserInterface ---------------- */

    public static function createFromPayload($id, array $payload): static
    {
        $client = new static();
        $client->id   = $id;          // sub claim
        $client->name = $payload['name'] ?? 'unknown';
        return $client;
    }

    public function getUserIdentifier(): string { return $this->id; }
    public function getRoles(): array
    {
        return ['ROLE_CLIENT'];
    }
    public function eraseCredentials(): void    {}

    public function serialize()
    {
        // TODO: Implement serialize() method.
    }

    public function unserialize(string $data)
    {
        // TODO: Implement unserialize() method.
    }

    public function __serialize(): array
    {
        // TODO: Implement __serialize() method.
    }

    public function __unserialize(array $data): void
    {
        // TODO: Implement __unserialize() method.
    }

    /**
     * @return Collection<int, ClientSiteKey>
     */
    public function getClientSiteKeys(): Collection
    {
        return $this->clientSiteKeys;
    }

    public function addClientSiteKey(ClientSiteKey $clientSiteKey): static
    {
        if (!$this->clientSiteKeys->contains($clientSiteKey)) {
            $this->clientSiteKeys->add($clientSiteKey);
            $clientSiteKey->setClient($this);
        }

        return $this;
    }

    public function removeClientSiteKey(ClientSiteKey $clientSiteKey): static
    {
        if ($this->clientSiteKeys->removeElement($clientSiteKey)) {
            // set the owning side to null (unless already changed)
            if ($clientSiteKey->getClient() === $this) {
                $clientSiteKey->setClient(null);
            }
        }

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }
}
