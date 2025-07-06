<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Dto\FileUploadInput;
use App\Dto\FormSubmissionInput;
use App\Repository\FormSubmissionRepository;
use App\State\FileUploadProcessor;
use App\State\FormSubmissionProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FormSubmissionRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            security: "is_granted('ROLE_CLIENT')",
            input: FormSubmissionInput::class,
            output: false,       // we don't return the entity
            processor: FormSubmissionProcessor::class
        ),
        new Get(
            normalizationContext: ['groups' => ['submission:read']],
            security: "is_granted('ROLE_CLIENT')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['submission:read']],
            security: "is_granted('ROLE_CLIENT')"
        ),
        new Post(
            uriTemplate: '/form_submissions/{id}/file',
            security: "object.getClient() == user",
            input: FileUploadInput::class,
            output: false,
            deserialize: false,
            processor: FileUploadProcessor::class,
        ),
    ])]
class FormSubmission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['submission:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['submission:read'])]
    private array $raw = [];

    #[ORM\Column]
    #[Groups(['submission:read'])]
    private ?bool $processed = null;

    #[ORM\Column]
    #[Groups(['submission:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, FormSubmissionField>
     */
    #[ORM\OneToMany(targetEntity: FormSubmissionField::class, mappedBy: 'FormSubmission', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['submission:read'])]
    private Collection $formSubmissionFields;

    #[ORM\ManyToOne(inversedBy: 'formSubmissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    /**
     * @var Collection<int, FormSubmissionFile>
     */
    #[ORM\OneToMany(targetEntity: FormSubmissionFile::class, mappedBy: 'formSubmission', orphanRemoval: true)]
    private Collection $formSubmissionFiles;


    public function __construct()
    {
        $this->formSubmissionFields = new ArrayCollection();
        $this->formSubmissionFiles = new ArrayCollection();
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

    /**
     * @return Collection<int, FormSubmissionFile>
     */
    public function getFormSubmissionFiles(): Collection
    {
        return $this->formSubmissionFiles;
    }

    public function addFormSubmissionFile(FormSubmissionFile $formSubmissionFile): static
    {
        if (!$this->formSubmissionFiles->contains($formSubmissionFile)) {
            $this->formSubmissionFiles->add($formSubmissionFile);
            $formSubmissionFile->setFormSubmission($this);
        }

        return $this;
    }

    public function removeFormSubmissionFile(FormSubmissionFile $formSubmissionFile): static
    {
        if ($this->formSubmissionFiles->removeElement($formSubmissionFile)) {
            // set the owning side to null (unless already changed)
            if ($formSubmissionFile->getFormSubmission() === $this) {
                $formSubmissionFile->setFormSubmission(null);
            }
        }

        return $this;
    }
}
