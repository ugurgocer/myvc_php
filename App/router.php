<?php

use App\Core\Route;
use App\Controller\User as UserController;
use App\Controller\FoodCategory as FCController;
use App\Controller\Food as FoodController;

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

$router->get($home_uri.'/', function() {
    echo '';
});

$router->post($home_uri.'/cikis', function (){
   UserController::logout($_POST);
});

$router->get($home_uri.'/user/([[:alnum:]]{32})', function ($token){
    UserController::userPage($token);
});

$router->post($home_uri.'/user/update/([[:alnum:]]{32})', function ($token){
   UserController::editUser($token, $_POST);
});

$router->get($home_uri.'/category/([[:alnum:]]{32})', function ($token){
    FCController::categoryPage($token);
});

$router->get($home_uri.'/food/([[:alnum:]]{32})/([0-9]+)', function ($token, $id){
   FoodController::foodPageWithCategory($token, $id);
});

$router->run();