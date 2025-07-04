<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FormSubmissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormSubmissionRepository::class)]
#[ApiResource]
class FormSubmission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $raw = [];

    #[ORM\Column]
    private ?bool $processed = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, FormSubmissionField>
     */
    #[ORM\OneToMany(targetEntity: FormSubmissionField::class, mappedBy: 'FormSubmission', orphanRemoval: true)]
    private Collection $formSubmissionFields;

    #[ORM\ManyToOne(inversedBy: 'formSubmissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    public function __construct()
    {
        $this->formSubmissionFields = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaw(): array
    {
        return $this->raw;
    }

    public function setRaw(array $raw): static
    {
        $this->raw = $raw;

        return $this;
    }

    public function isProcessed(): ?bool
    {
        return $this->processed;
    }

    public function setProcessed(bool $processed): static
    {
        $this->processed = $processed;

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
     * @return Collection<int, FormSubmissionField>
     */
    public function getFormSubmissionFields(): Collection
    {
        return $this->formSubmissionFields;
    }

    public function addFormSubmissionField(FormSubmissionField $formSubmissionField): static
    {
        if (!$this->formSubmissionFields->contains($formSubmissionField)) {
            $this->formSubmissionFields->add($formSubmissionField);
            $formSubmissionField->setFormSubmission($this);
        }

        return $this;
    }

    public function removeFormSubmissionField(FormSubmissionField $formSubmissionField): static
    {
        if ($this->formSubmissionFields->removeElement($formSubmissionField)) {
            // set the owning side to null (unless already changed)
            if ($formSubmissionField->getFormSubmission() === $this) {
                $formSubmissionField->setFormSubmission(null);
            }
        }

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }
}
