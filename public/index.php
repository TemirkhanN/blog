<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    if ($context['APP_ENV'] == 'dev') {
        Debug::enable();
    }

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
