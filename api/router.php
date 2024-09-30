<?php

use App\Controllers\UserController;
use App\Core\Router;

$router = Router::getRouter();

$router->get('users', [UserController::class, 'index']);
$router->get('users/{id}', [UserController::class, 'show']);
$router->post('users', [UserController::class, 'create']);
$router->post('users/{id}/update', [UserController::class, 'update']);
$router->post(
  'users/{id}/update-password',
  [UserController::class, 'updatePassword']
);
$router->post(
  'users/{id}/delete',
  [UserController::class, 'delete']
);

$router->dispatch();
