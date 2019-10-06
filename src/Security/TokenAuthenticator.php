<?php
declare(strict_types=1);

namespace App\Security;

use App\Service\Response\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var array
     */
    private $tokens;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(array $tokens, ResponseFactoryInterface $responseFactory)
    {
        $this->tokens          = $tokens;
        $this->responseFactory = $responseFactory;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->responseFactory->createResponse('Unauthenticated', 401);
    }

    public function supports(Request $request)
    {
        if ($request->headers->has('Authorization')) {
            return true;
        }

        return false;
    }

    public function getCredentials(Request $request)
    {
        return [
            'token' => $request->headers->get('Authorization'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        foreach ($this->tokens as $authorName => $token) {
            if ($token === $credentials['token']) {
                return new User($authorName, null);
            }
        }

        return null;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!isset($credentials['token'])) {
            return false;
        }

        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return $this->responseFactory->createResponse('Authentication failure', 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
