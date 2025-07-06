<?php

namespace App\State;

// src/State/FileUploadProcessor.php
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\FileUploadInput;
use App\Entity\FormSubmission;
use App\Entity\FormSubmissionFile;
use App\Message\FormSubmissionMessage;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

final class FileUploadProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
        private RequestStack $requestStack,
        private string $uploadDir
    ) {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
    }

    public function process(
        mixed     $data,            // this is the FormSubmission entity
        Operation $operation,
        array     $uriVariables = [],
        array     $context      = []
    ): void {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $file    = $request->files->get('file'); // 'file' is the form field name
        //dump($request->getContent());

        if (!$file) {
            throw new \RuntimeException('No file uploaded');
        }
        /* ⇩ grab metadata first */
        $size     = $file->getSize() ?? 0;
        $mimeType = $file->getClientMimeType() ?? 'application/octet-stream';
        $origName = $file->getClientOriginalName();

        $storedName = sprintf('%s_%s', uniqid(), $file->getClientOriginalName());
        $file->move($this->uploadDir, $storedName);

        $attachment = (new FormSubmissionFile())
            ->setFormSubmission($data)                   // $data is the entity
            ->setOriginalName($origName)
            ->setStoragePath($storedName)
            ->setMimeType($mimeType ?? 'application/octet-stream')
            ->setSize($size)
            ->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($attachment);
        $this->em->flush();

        // Prefer siteKey from the multipart fields, fall back to the one stored on the entity
        $siteKeyFromRequest = $request->request->get('siteKey');
        $siteKey = $siteKeyFromRequest ?: ($data->getSiteKey() ?? '');
        $this->bus->dispatch(new FormSubmissionMessage($data->getId(), $siteKey));
    }
}
