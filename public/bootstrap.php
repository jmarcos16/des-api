<?php

use App\Core\Application;
use App\Utils\JsonResponse;

require __DIR__ . '/../vendor/autoload.php';
(new Application())->setExeptionHandler();

$container = new \App\Core\Container();
$dotenv    = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

JsonResponse::resolveOptions();

$container->bind(
    \App\Interfaces\ProductRepositoryInterface::class,
    \App\Repositories\Postgre\ProductRepository::class
);
$container->bind(
    App\Interfaces\ProductTypeRepositoryInterface::class,
    App\Repositories\Postgre\ProductTypeRepository::class
);

Application::resolve($container);
