<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ([
                     ['SrsBsns',       'forms@srsbsns.co.za'],
                     ['Bietou Capital', 'admin@bietou.co.za'],
                 ] as [$name,$recipient]) {

            $c = new Client();
            $c->setName($name);
            $c->setRecipient($recipient);
            $c->setJwtSecret(bin2hex(random_bytes(32)));
            $c->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($c);
            $manager->flush();
        }
    }
}
