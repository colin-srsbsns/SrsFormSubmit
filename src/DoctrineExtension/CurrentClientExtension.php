<?php

// src/Doctrine/CurrentClientExtension.php
namespace App\DoctrineExtension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

use App\Entity\FormSubmission;

final class CurrentClientExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security) {}

    /** @inheritDoc */
    public function applyToCollection(
        QueryBuilder                           $queryBuilder,
        QueryNameGeneratorInterface            $queryNameGenerator,
        string                                 $resourceClass,
        string|\ApiPlatform\Metadata\Operation $operation = null,
        array                                  $context = []
    ): void {
        $this->addClientConstraint($queryBuilder, $resourceClass);
    }

    /** @inheritDoc */
    public function applyToItem(
        QueryBuilder                           $queryBuilder,
        QueryNameGeneratorInterface            $queryNameGenerator,
        string                                 $resourceClass,
        array                                  $identifiers,
        string|\ApiPlatform\Metadata\Operation $operation = null,
        array                                  $context = []
    ): void {
        $this->addClientConstraint($queryBuilder, $resourceClass);
    }

    private function addClientConstraint(QueryBuilder $qb, string $resourceClass): void
    {
        if (FormSubmission::class !== $resourceClass) {
            return;                          // skip other entities
        }

        $user = $this->security->getUser();
        if (!$user) {                        // should not happen (firewall), but be safe
            return;
        }

        $rootAlias = $qb->getRootAliases()[0];
        $qb->andWhere(sprintf('%s.client = :current_client', $rootAlias))
            ->setParameter('current_client', $user->getUserIdentifier());
    }
}
