<?php

declare(strict_types=1);

namespace App\Controller;

use App\Lib\Response\ResponseFactoryInterface;
use App\Service\TokenIssuer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class IssueTokenController
{
    public function __construct(
        private readonly TokenIssuer $tokenIssuer,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(Request $request, RateLimiterFactory $sensitiveApiLimiter): Response
    {
        $limiter = $sensitiveApiLimiter->create($request->getClientIp());
        if (!$limiter->consume()->isAccepted()) {
            return $this->responseFactory->tooManyRequests();
        }

        $data     = $request->getPayload();
        $login    = (string) $data->get('login');
        $password = (string) $data->get('password');
        if ($login === '' || $password === '') {
            return $this->responseFactory->badRequest('Login or password can not be empty');
        }

        $token = $this->tokenIssuer->createToken($login, $password);
        if ($token === null) {
            return $this->responseFactory->unauthorized('Invalid credentials');
        }

        return $this->responseFactory->createResponse(['token' => $token], 201);
    }
}
