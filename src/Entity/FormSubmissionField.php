<?php

namespace App\Entity;

use App\Repository\FormSubmissionFieldRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FormSubmissionFieldRepository::class)]
class FormSubmissionField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['submission:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'formSubmissionFields')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['submission:read'])]
    private ?FormSubmission $FormSubmission = null;

    #[ORM\Column(length: 100)]
    #[Groups(['submission:read'])]
    private ?string $fieldName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['submission:read'])]
    private ?string $fieldValue = null;

    #[ORM\Column]
    #[Groups(['submission:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormSubmission(): ?FormSubmission
    {
        return $this->FormSubmission;
    }

    public function setFormSubmission(?FormSubmission $FormSubmission): static
    {
        $this->FormSubmission = $FormSubmission;

        return $this;
    }

    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    public function setFieldName(string $fieldName): static
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    public function getFieldValue(): ?string
    {
        return $this->fieldValue;
    }

    public function setFieldValue(?string $fieldValue): static
    {
        $this->fieldValue = $fieldValue;

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
