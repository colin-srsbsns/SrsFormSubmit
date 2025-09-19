<?php

namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'srs:create-client',
    description: 'Creates a new Client entity.'
)]
class CreateClientCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Client name')
            ->addOption('jwt-secret', null, InputOption::VALUE_OPTIONAL, 'JWT secret (auto-generated if not provided)')
            ->addOption('jwt-ttl', null, InputOption::VALUE_OPTIONAL, 'JWT TTL in seconds (e.g., 3600 for 1 hour)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $jwtSecret = $input->getOption('jwt-secret') ?: bin2hex(random_bytes(32));
        $jwtTtl = $input->getOption('jwt-ttl') ? (int) $input->getOption('jwt-ttl') : null;

        $client = new Client();
        $client->setName($name);
        $client->setJwtSecret($jwtSecret);
        $client->setCreatedAt(new \DateTimeImmutable());

        if ($jwtTtl !== null) {
            $client->setJwtTtl($jwtTtl);
        }

        $this->em->persist($client);
        $this->em->flush();

        $output->writeln("<info>Client '{$name}' created with ID: {$client->getId()}</info>");
        $output->writeln("<comment>JWT Secret: {$jwtSecret}</comment>");

        if ($jwtTtl !== null) {
            $output->writeln("<comment>JWT TTL: {$jwtTtl} seconds</comment>");
        }

        return Command::SUCCESS;
    }
}
