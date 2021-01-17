<?php

require '../vendor/autoload.php';

use App\Exceptions\SecurityException;
use Bramus\Router\Router;

try {
    $router = new Router();
    $router->setNamespace('\App\Controllers');

    $router->get('/', 'RenderMainPageController@renderMainPage');
    $router->get('/tasks/(\d+)/(\d+)/(\d+)', 'TasksController@getTasksByPage');

    $router->post('/task', 'TasksController@createNewTask');

    $router->post('/task/text', 'TasksController@updateTaskText');
    $router->post('/task/completed', 'TasksController@completedTask');

    $router->post('/login', 'UsersController@login');
    $router->post('/logout', 'UsersController@logout');

    $router->delete('/task/(\d+)', 'TasksController@deleteTask');

    $router->set404(function () {
        readfile("./templates/404.html");
    });

    $router->run();

} catch (SecurityException $e) {
    $message = $e->getMessage();
    echo "<h1>Please send to technical support:</h1><br><h3>$message</h3>";
}

