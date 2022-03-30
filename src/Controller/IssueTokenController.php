<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Response\ResponseFactoryInterface;
use App\Service\TokenIssuer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IssueTokenController
{
    private TokenIssuer $tokenIssuer;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(TokenIssuer $tokenIssuer, ResponseFactoryInterface $responseFactory)
    {
        $this->tokenIssuer = $tokenIssuer;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(Request $request): Response
    {
        $login = (string) $request->request->get('login');
        $password = (string) $request->request->get('password');
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
