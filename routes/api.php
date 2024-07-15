<?php

use App\Controllers\{ProductController, ProductTypeController, SaleController};
use App\Core\Route;

$route = new Route();

$route->get('/products', [ProductController::class, 'index']);
$route->post('/products', [ProductController::class, 'store']);
$route->get('/products/all', [ProductController::class, 'findAll']);

$route->get('/product-types', [ProductTypeController::class, 'index']);
$route->get('/product-types/all', [ProductTypeController::class, 'findAll']);
$route->post('/product-types', [ProductTypeController::class, 'store']);

$route->get('/sales', [SaleController::class, 'index']);
$route->post('/sales', [SaleController::class, 'create']);

return $route->getRoutes();
