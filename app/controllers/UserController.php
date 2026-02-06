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
        $mdp = $_POST['mdp'];
        $userRepository = new UserRepository($db);
        if(!$userRepository->verifyUser($email,$nom,$mdp)){
            Flight::redirect('/login?erreur=1');
        }
        else{
            Flight::redirect('/home');
        }
    }
}