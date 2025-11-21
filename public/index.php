<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    // Active le mode debug si nécessaire
    if ($context['APP_DEBUG'] ?? false) {
        umask(0000);
        Debug::enable();
    }

    // Initialise le noyau de Symfony
    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);

    // Gère la requête HTTP
    $request = Request::createFromGlobals();
    $response = $kernel->handle($request);
    $response->send();

    // Termine la requête
    $kernel->terminate($request, $response);
};
