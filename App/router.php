<?php

use App\Core\Route;
use App\Controller\User as UserController;
use App\Controller\FoodCategory as FCController;
use App\Controller\Food as FoodController;
use App\Controller\Meals as MealsController;

$path = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$home_uri = "/diyet_takvimi";
$router = new Route($path, $method);

//ANASAYFA
$router->get($home_uri.'/', function() {
    echo 'e';
});

// USER İŞLEMLERİ

$router->post($home_uri.'/giris', function(){
    UserController::login($_POST);
});

$router->post($home_uri.'/kayit', function(){
    UserController::register($_POST);
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

// Yemek Kategorisi İşlemleri

$router->get($home_uri.'/category/([[:alnum:]]{32})', function ($token){
    FCController::categoryPage($token);
});

// Yemek İşlemleri

$router->get($home_uri.'/food/([[:alnum:]]{32})/([0-9]+)', function ($token, $id){
   FoodController::foodPageWithCategory($token, $id);
});

$router->get($home_uri.'/food/([[:alnum:]]{32})/([0-9]+)/([0-9]+)', function ($token, $min,$max){
    FoodController::foodPageWithBetweenCalory($token, $min, $max);
});

$router->post($home_uri.'/food/([[:alnum:]]{32})', function ($token){
   FoodController::createFood($token, $_POST);
});

$router->get($home_uri.'/food/([[:alnum:]]{32})/([[:alnum:]]{3,})', function ($token, $pattern){
    FoodController::searchFood($token, $pattern);
});

// Menü İşlemleri

$router->post($home_uri.'/meal/([[:alnum:]]{32})', function ($token){
    MealsController::createMeal($token, $_POST);

});
$router->get($home_uri.'/meal/([[:alnum:]]{32})/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))', function ($token, $date){
    MealsController::mealPageWithDate($token, $date);
});

$router->run();