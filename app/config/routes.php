<?php

use Flight;
use app\controllers\IndexController;
use app\controllers\MessageController;


Flight::route('GET /', function(){
    $index = new IndexController();
    $index->showIndex();
});

 $router->get('/test', function () {
        $db = Flight::db();
        var_dump($db->query("SELECT version()")->fetch());
});

Flight::route('GET /messages', function(){
    $message = new MessageController();
    $message->showMessages();
});
// Flight::route('POST /register', ['AuthController', 'postRegister']);
// Flight::route('POST /api/validate/register', ['AuthController', 'validateRegisterAjax']);
// Flight::route('GET /login', ['AuthController', 'showLogin']);
// Flight::route('POST /login', ['AuthController', 'postLogin']);
// Flight::route('POST /api/validate/login', ['AuthController', 'validateLoginAjax']);