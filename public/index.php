<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    if ($context['APP_ENV'] === 'prod') {
        error_reporting(0);
    }

    if ($context['APP_ENV'] === 'dev') {
        Debug::enable();
    }

    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);

    if ($context['APP_ENV'] === 'prod') {
        $kernel = new HttpCache(
            $kernel,
            null,
            null,
            [
                'allow_reload'     => true,
                'allow_revalidate' => true,
            ]
        );
    }

    return $kernel;
};
