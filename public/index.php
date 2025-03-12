<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Routes\Router;
use Controllers\TaskController;

$router = new Router();
$router->get('/', [TaskController::class, 'index']);
$router->post('/tasks', [TaskController::class, 'saveTasks']);
$router->get('/tasks', [TaskController::class, 'listTasks']);
$router->get('/tasks/(\d+)', [TaskController::class, 'getById']);
$router->put('/tasks/(\d+)', [TaskController::class, 'updateTask']);
$router->put('/tasks/status/(\d+)', [TaskController::class, 'updateStatus']);
$router->delete('/tasks/(\d+)', [TaskController::class, 'deleteTask']);
$router->handleRequest();
