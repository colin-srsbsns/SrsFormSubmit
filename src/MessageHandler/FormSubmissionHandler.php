<?php

// src/MessageHandler/ContactSubmissionHandler.php
namespace App\MessageHandler;

use App\Entity\FormSubmissionField;

use App\Entity\ClientSiteKey;

use App\Entity\FormSubmission;
use App\Message\FormSubmissionMessage;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FormSubmissionHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private string $uploadDir
    ) {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
    }

    public function __invoke(FormSubmissionMessage $msg): void
    {
        /** @var FormSubmission|null $submission */
        $submission = $this->em->getRepository(FormSubmission::class)
            ->find($msg->submissionId);

        if (!$submission || $submission->isProcessed()) {
            return; // already handled or missing – nothing to do
        }

        $fields = $submission->getRaw();

        // persist normalised key/value pairs
        foreach ($fields as $name => $value) {
            $submission->addFormSubmissionField(
                (new FormSubmissionField())
                    ->setFieldName($name)
                    ->setFieldValue(is_scalar($value) ? (string) $value : json_encode($value))
                    ->setFormSubmission($submission)
                    ->setCreatedAt(new DateTimeImmutable())
            );
        }

        $email = (new TemplatedEmail())
            ->from('forms@srsbsns.co.za')
            ->to($this->lookupRecipient($msg->siteKey))
            ->subject('New contact form submission')
            ->htmlTemplate('email/form_submission.html.twig')
            ->context(['submission' => $submission]);

        // attach every uploaded file
        foreach ($submission->getFormSubmissionFiles() as $file) {
            $path = $this->uploadDir.'/'.$file->getStoragePath();
            $email->attachFromPath($path, $file->getOriginalName(), $file->getMimeType());
        }

        $this->mailer->send($email);

        $submission->setProcessed(true);
        $this->em->flush();
    }

    private function lookupRecipient(string $siteKey): string
    {
        /** @var ClientSiteKey|null $siteKeyEntity */
        $siteKeyEntity = $this->em
            ->getRepository(ClientSiteKey::class)
            ->findOneBy(['siteKey' => $siteKey]);

        if ($siteKeyEntity) {
            return $siteKeyEntity->getClient()->getRecipient();
        }

        // fallback – keeps prod live even if DB row missing
        return 'colin@srsbsns.co.za';
    }
}
