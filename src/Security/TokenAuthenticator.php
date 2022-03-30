<?php

declare(strict_types=1);

namespace App\Security;

use App\Service\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    private string $ownerPassword;

    private ResponseFactoryInterface $responseFactory;

    public function __construct(string $ownerPassword, ResponseFactoryInterface $responseFactory)
    {
        $this->ownerPassword   = $ownerPassword;
        $this->responseFactory = $responseFactory;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $token = $request->headers->get('Authorization');
        if ($token === null) {
            throw new TokenNotFoundException('No API token provided');
        }

        if (!password_verify($this->ownerPassword, $token)) {
            throw new CustomUserMessageAuthenticationException('Invalid token');
        }

        return new SelfValidatingPassport(
            new UserBadge('admin', function () {
                return new InMemoryUser('admin', null);
            })
        );
    }

    public function supports(Request $request): ?bool
    {
        if ($request->headers->has('Authorization')) {
            return true;
        }

        return false;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return $this->responseFactory->createResponse('Authentication failure: ' . $exception->getMessage(), 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }
}
