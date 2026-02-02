<?php
namespace app\controllers;

use app\repositories\UserRepository;
use app\Services\Validator;
use app\Services\UserService;
use Flight;
class AuthController {
  public static function showRegister() {
    Flight::render('auth/register', [
      'values' => ['nom'=>'','prenom'=>''],
      'errors' => ['nom'=>'','prenom'=>''],
      'success' => false
    ]);
  }

  public static function validateRegisterAjax() {
    header('Content-Type: application/json; charset=utf-8');

    try {
      $pdo  = Flight::db();
      $repo = new UserRepository($pdo);
      $req = Flight::request();

      $input = [
        'nom' => $req->data->nom,
        'prenom' => $req->data->prenom,
      ];

      $res = Validator::validateRegister($input, $repo);

      Flight::json([
        'ok' => $res['ok'],
        'errors' => $res['errors'],
        'values' => $res['values'],
      ]);
    } catch (\Throwable $e) {
      http_response_code(500);
      Flight::json([
        'ok' => false,
        'errors' => ['_global' => 'Erreur serveur lors de la validation.'],
        'values' => []
      ]);
    }
  }

  public static function postRegister() {
    $pdo  = Flight::db();
    $repo = new UserRepository($pdo);
    $svc  = new UserService($repo);

    $req = Flight::request();

    $input = [
      'nom' => $req->data->nom,
      'prenom' => $req->data->prenom,
    ];

    $res = Validator::validateRegister($input, $repo);

    if ($res['ok']) {
      $svc->register($res['values'], (string)$input['password']);
      Flight::render('auth/register', [
        'values' => ['nom'=>'','prenom'=>''],
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
