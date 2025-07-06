<?php

namespace App\State;

// src/State/FileUploadProcessor.php
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\FormSubmission;
use App\Entity\FormSubmissionFile;
use App\Message\FormSubmissionMessage;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class FileUploadProcessor implements ProcessorInterface
{

    public function __construct(
        private EntityManagerInterface $em,private MessageBusInterface $bus,
        private string $uploadDir = '%kernel.project_dir%/public/uploads'
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        /** @var FormSubmission $submission */
        $submission = $operation->getExtraProperties()['extra_data']['object'];
        $file       = $data->file;

        $storedName = sprintf('%s_%s', uniqid(), $file->getClientOriginalName());
        $file->move($this->uploadDir, $storedName);

        // persist as dedicated attachment entity
        $attachment = (new FormSubmissionFile())
            ->setFormSubmission($submission)
            ->setOriginalName($file->getClientOriginalName())
            ->setStoragePath($storedName)
            ->setMimeType($file->getClientMimeType() ?? 'application/octet-stream')
            ->setSize($file->getSize() ?? 0)
            ->setCreatedAt(new DateTimeImmutable());

        $this->em->persist($attachment);

        $this->bus->dispatch(new FormSubmissionMessage($submission->getId(), $data->siteKey));

        $this->em->flush();

        return null;                           // 204 No Content
    }
}
