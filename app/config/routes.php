<?php

use Flight;
use app\controllers\IndexController;
use app\controllers\MessageController;
use app\controllers\UserController;
use app\controllers\AuthController;

use app\models\User;

Flight::route('GET /home', function(){
    $index = new IndexController();
    $index->showIndex();
});

 $router->get('/test', function () {
        $db = Flight::db();
        var_dump($db->query("SELECT version()")->fetch());
});

 $router->get('/forms', function () {
     Flight::render('dist-modern/forms');
});
//mapiseo formulaire
 $router->get('/', function () {
       Flight::render('dist-modern/login');
});

Flight::route('GET /messages', function(){
    $message = new MessageController();
    $message->showMessages();
});
$router->post('/register' , function(){
    $usercontroller = new UserController();
    $usercontroller->register();
});

$router ->post('/api/validate/register', function(){
    $auth = new AuthController();
    $auth->validateRegisterAjax();
});



// Flight::route('POST /register', ['AuthController', 'postRegister']);
//Flight::route('POST /api/validate/register', ['AuthController', 'validateRegisterAjax']);
// Flight::route('GET /login', ['AuthController', 'showLogin']);
// Flight::route('POST /login', ['AuthController', 'postLogin']);
// Flight::route('POST /api/validate/login', ['AuthController', 'validateLoginAjax']);