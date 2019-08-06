<?php

use App\Core\Route;
use App\Controller\User as UserController;
use App\Controller\Food as FoodController;
use App\Controller\Meals as MealsController;
use App\Controller\MealTypes as MTController;
use App\Controller\Calculaties;

$path = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$home_uri = "";
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

$router->get($home_uri.'/user/read/([[:alnum:]]{32})', function ($token){
    UserController::userPage($token);
});

$router->post($home_uri.'/user/update/([[:alnum:]]{32})', function ($token){
   UserController::editUser($token, $_POST);
});


// Yemek İşlemleri

$router->get($home_uri.'/food/list/([[:alnum:]]{32})/([0-9]+)/([0-9]+)', function ($token, $min,$max){
    FoodController::foodPageWithBetweenCalory($token, $min, $max);
});

$router->post($home_uri.'/food/create/([[:alnum:]]{32})', function ($token){
   FoodController::createFood($token, $_POST);
});

$router->post($home_uri.'/food/search/([[:alnum:]]{32})', function ($token){
    FoodController::searchFood($token, $_POST);
});

// Menü İşlemleri

$router->get($home_uri.'/meal/types/([[:alnum:]]{32})', function ($token){
    MTController::readTypes($token);
});

$router->post($home_uri.'/meal/create/([[:alnum:]]{32})', function ($token){
    MealsController::createMeal($token, $_POST);

});

$router->get($home_uri.'/meal/list/([[:alnum:]]{32})/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))', function ($token, $date){
    MealsController::mealPageWithDate($token, $date);
});

$router->post($home_uri.'/meal/update/([[:alnum:]]{32})', function ($token){
    MealsController::editMeal($token, $_POST);
});

$router->get($home_uri.'/meal/read/([[:alnum:]]{32})/([0-9]+)', function ($token, $meal_id){
    MealsController::readMeal($token, $meal_id);
});

// Hesaplamalar

$router->post($home_uri.'/calculate/basal-metabolism/([[:alnum:]]{32})', function ($token){
    Calculaties::getBM($token, $_POST);
});

$router->post($home_uri.'/calculate/ideal-weight/([[:alnum:]]{32})', function ($token){
    Calculaties::getIW($token, $_POST);
});

$router->post($home_uri.'/calculate/body-mass-index/([[:alnum:]]{32})', function ($token){
    Calculaties::getBMI($token, $_POST);
});

$router->post($home_uri.'/calculate/kg-to-calories/([[:alnum:]]{32})', function ($token){
    Calculaties::getKg2KCal($token, $_POST);
});

$router->run();