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

Flight::route('POST /messages/send', function(){
    $message = new MessageController();
    $message->sendMessageForm();
});

// Récupérer la liste des conversations
Flight::route('GET /api/messages/conversations', function(){
    $controller = new MessageController();
    $controller->getConversations();
});

// Récupérer les messages d'une conversation avec un utilisateur
Flight::route('GET /api/messages/@userId', function($userId){
    $controller = new MessageController();
    $controller->getMessages($userId);
});

// Envoyer un message
Flight::route('POST /api/messages/send', function(){
    $controller = new MessageController();
    $controller->sendMessage();
});

// Récupérer la liste des utilisateurs
Flight::route('GET /api/users', function(){
    $controller = new MessageController();
    $controller->getUsers();
});

// Marquer les messages comme lus
Flight::route('POST /api/messages/read/@senderId', function($senderId){
    $controller = new MessageController();
    $controller->markAsRead($senderId);
});

// Compter les messages non lus
Flight::route('GET /api/messages/unread/count', function(){
    $controller = new MessageController();
    $controller->getUnreadCount();
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