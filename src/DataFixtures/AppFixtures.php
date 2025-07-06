<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\ClientSiteKey;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ([
                     ['SrsBsns',       'colin@srsbsns.co.za','srsbsns'],
                     ['Bietou Capital', 'colin@srsbsns.co.za','bietou-capital'],
                 ] as [$name,$recipient,$siteKey]) {

            $c = new Client();
            $c->setName($name);
            $c->setRecipient($recipient);
            $clientSiteKey = new ClientSiteKey();
            $clientSiteKey->setSiteKey($siteKey);
            $clientSiteKey->setClient($c);
            $clientSiteKey->setCreatedAt(new \DateTimeImmutable());
            $c->addClientSiteKey($clientSiteKey);
            $manager->persist($clientSiteKey);
            $c->setJwtSecret(bin2hex(random_bytes(32)));
            $c->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($c);
            $manager->flush();
        }
    }
}
