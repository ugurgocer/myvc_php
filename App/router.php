<?php

use App\Core\Route;
use App\Controller\User as UserController;

$path = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$home_uri = "/diyet_takvimi";
$router = new Route($path, $method);

$router->post($home_uri.'/giris', function(){
    UserController::login($_POST);
});

$router->post($home_uri.'/kayit', function(){
    UserController::register($_POST);
});

$router->get($home_uri.'/', function(){
    echo 'Hoşgeldin';
});

$router->run();