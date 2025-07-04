<?php

namespace App\Entity;

use App\Repository\FormSubmissionFieldRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormSubmissionFieldRepository::class)]
class FormSubmissionField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'formSubmissionFields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormSubmission $FormSubmission = null;

    #[ORM\Column(length: 100)]
    private ?string $fieldName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $fieldValue = null;

    #[ORM\Column]
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
