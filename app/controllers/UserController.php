<?php
namespace app\controllers;
use app\repositories\UserRepository;
use app\services\UserService;
use app\services\Validator;

use Flight;

class UserController   
{
    public function findById($id)
    {
        $db = Flight::db();
        $userRepository = new UserRepository($db);
        return $userRepository->findById($id);
    }

    public static function register(){
        $pdo  = Flight::db();
        $repo = new UserRepository($pdo);
        $svc  = new UserService($repo);

        
        $req = Flight::request();

        $input=[
            'nom' => $req->data->nom
        ];

        $nom = $input['nom'];

        $res = Validator::validateRegister($input, $repo);

        if (!$res['ok']) {
            Flight::render('dist-modern/login', [
                'values' => $res['values'],
                'errors' => $res['errors'],
                'success' => false
            ]);
            return;
        }

        $userId = $repo->verifyUser($nom);
        if ($userId) {
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['user_id'] = $userId;
            }
            Flight::render('dist-modern/home', [
                'values' => ['nom' => $nom],
                'success' => true
            ]);
            return;
        }

        $svc->register($res['values']);
        Flight::render('dist-modern/login', [
            'values' => ['nom' => ''],
            'success' => true
        ]);
        return;

    }
   
    
}