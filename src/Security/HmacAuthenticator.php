<?php
// src/Security/HmacAuthenticator.php
namespace App\Security;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class HmacAuthenticator extends AbstractAuthenticator
{
    public function __construct(private EntityManagerInterface $em) {}

    public function supports(Request $request): ?bool
    {
        return str_starts_with($request->headers->get('Authorization', ''), 'Bearer ');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $jwt = substr($request->headers->get('Authorization'), 7);

        // 1. grab cid without verifying (header+payload only)
        [$header, $payload] = array_map(
            fn($part) => json_decode(base64_decode($part), true),
            explode('.', $jwt, 3)
        );
        $cid = $payload['cid'] ?? null;
        if (!$cid) {
            throw new AuthenticationException('CID missing');
        }

        /** @var Client|null $client */
        $client = $this->em->getRepository(Client::class)->find($cid);
        if (!$client) {
            throw new AuthenticationException('Unknown client');
        }

        // 2. verify signature using client secret
        try {
            JWT::decode($jwt, new Key($client->getJwtSecret(), 'HS256'));
        } catch (\Throwable $e) {
            throw new AuthenticationException('Bad signature');
        }

        return new SelfValidatingPassport(
            new UserBadge($client->getId(), fn() => $client)
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        // Nothing fancy – authentication passed, hand control back to the controller
        return null;   // returning null tells Symfony to keep processing
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?Response {
        return new Response('Unauthorized', 401);
    }
}
