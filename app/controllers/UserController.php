<?php
namespace app\controllers;
use app\repositories\UserRepository;

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
        $db = Flight::db();
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $userRepository = new UserRepository($db);
        $userRepository->create($nom,$email);
        session_start();
        $_SESSION['user'] = $nom;
    }
}