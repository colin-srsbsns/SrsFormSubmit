<?php


namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Uid\Ulid;

#[AsCommand(
    name: 'srs:generate-client-token',
    description: 'Generates an HS256 JWT for a given Client (lookup by ULID or name).'
)]
class GenerateClientTokenCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('client', InputArgument::REQUIRED, 'Client ULID or name')
            ->addOption('ttl', null, InputOption::VALUE_OPTIONAL, 'Lifetime in **hours** (0 = no exp claim)', 24);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ref = $input->getArgument('client');
        $ttl = (int)$input->getOption('ttl');

        /** @var Client|null $client */
        $repo   = $this->em->getRepository(Client::class);
        $client = $repo->findOneBy(['name' => $ref]);              // 1️⃣ try by name

        if (!$client && Ulid::isValid($ref)) {                     // 2️⃣ fallback to ULID
            $client = $repo->find($ref);
        }

        if (!$client) {
            $output->writeln("<error>Client '$ref' not found</error>");
            return Command::FAILURE;
        }

        // Payload
        $claims = [
            'cid' => $client->getId(),   // our custom claim the authenticator looks for
            'iat' => time(),
        ];
        if ($ttl > 0) {
            $claims['exp'] = time() + $ttl * 3600;
        }

        $jwt = JWT::encode($claims, $client->getJwtSecret(), 'HS256');

        $output->writeln("\n<info>{$client->getName()} token:</info>\n$jwt\n");
        return Command::SUCCESS;
    }
}
