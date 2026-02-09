<?php
namespace app\controllers;

use app\repositories\UserRepository;
use app\services\Validator;
use app\services\UserService;
use Flight;
class AuthController {
  public static function showRegister() {
    Flight::render('auth/register', [
      'values' => ['nom'=>'','email'=>'','mdp'=>''],
      'errors' => ['nom'=>'','email'=>'','mdp'=>''],
      'success' => false
    ]);
  }

  public static function validateRegisterAjax() {
    header('Content-Type: application/json; charset=utf-8');

    try {
      $req = Flight::request();

      $input = [
        'nom' => $req->data->nom,
      ];

      $res = Validator::validateRegister($input);

      Flight::json([
        'ok' => $res['ok'],
        'errors' => $res['errors'],
        'values' => $res['values'],
      ]);
    }catch (\Throwable $e) {
        http_response_code(500);
        $resp = [
          'ok' => false,
          'errors' => ['_global' => 'Erreur serveur lors de la validation.'],
          'values' => []
        ];
        Flight::json($resp);
      }
  }

  public static function postRegister() {
    $pdo  = Flight::db();
    $repo = new UserRepository($pdo);
    $svc  = new UserService($repo);

    $req = Flight::request();

    $input = [
      'nom' => $req->data->nom,
    ];

    $res = Validator::validateRegister($input, $repo);

    if ($res['ok']) {
      $svc->register($res['values']);
      Flight::render('auth/register', [
        'values' => ['nom'=>''],
        'success' => true
      ]);
      return;
    }

    Flight::render('auth/register', [
      'values' => $res['values'],
      'errors' => $res['errors'],
      'success' => false
    ]);
  }
  }
