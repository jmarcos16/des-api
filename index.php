<?php

require __DIR__ . '/public/bootstrap.php';

$router = new App\Core\Router($container);
$routes = require __DIR__ . '/routes/api.php';
$router->resolve($routes);
