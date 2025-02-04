<?php

declare(strict_types=1);

namespace Frontend\Controller\Admin;

use DateInterval;
use DateTime;
use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Page;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class LoginController extends AbstractBlogController
{
    public function __invoke(Request $request): Response
    {
        $login    = '';
        $password = '';
        $error    = '';
        if ($request->isMethod('POST')) {
            $login    = $request->request->getAlnum('login');
            $password = (string) $request->request->get('password');

            $result = $this->blogApi->createUserToken($login, $password);

            if ($result->isSuccessful()) {
                $redirect   = new RedirectResponse('/');
                $expiryTime = new DateTime();
                $expiryTime->add(new DateInterval('P1M'));
                $redirect->headers->setCookie(Cookie::create('_authToken', $result->getData(), $expiryTime));

                return $redirect;
            }

            $error = $result->getError()->getMessage();
        }

        return new Response($this->renderer->render(Page::LOGIN, compact('login', 'password', 'error')));
    }
}
