<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\FormSubmissionInput;
use App\Entity\Client;
use App\Entity\FormSubmission;
use App\Message\FormSubmissionMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class FormSubmissionProcessor implements ProcessorInterface
{
public function __construct(
    private EntityManagerInterface $em,
    private MessageBusInterface $bus,
    private Security $security,
) {}

/** @param FormSubmissionInput $data */
public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        $entity              = new FormSubmission();
        $entity->setRaw($data->payload);
        $entity->setCreatedAt( new \DateTimeImmutable());
        $entity->setProcessed(false);

        /** @var Client $client */
        $client = $this->security->getUser();
        $entity->setClient($client);
        $this->em->persist($entity);
        $this->em->flush();

        $this->bus->dispatch(new FormSubmissionMessage($entity->getId(), $data->siteKey));

        // Return 202 Accepted, empty body
        return null;
    }
}
