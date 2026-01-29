<?php
require_once __DIR__ . '/controllers/IndexController.php';
require_once __DIR__ . '/controllers/MessageController.php';
// require_once __DIR__ . '/services/Validator.php';
// require_once __DIR__ . '/services/UserService.php';
// require_once __DIR__ . '/repositories/UserRepository.php';

Flight::route('GET /', ['IndexController', 'showIndex']);
Flight::route('GET /messages', ['MessageController', 'showMessages']);
// Flight::route('POST /register', ['AuthController', 'postRegister']);
// Flight::route('POST /api/validate/register', ['AuthController', 'validateRegisterAjax']);
// Flight::route('GET /login', ['AuthController', 'showLogin']);
// Flight::route('POST /login', ['AuthController', 'postLogin']);
// Flight::route('POST /api/validate/login', ['AuthController', 'validateLoginAjax']);
