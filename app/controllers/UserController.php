<?php
namespace app\controllers;
namespace app\repositories;

use Flight;

class UserController   
{
    public function findById($id)
    {
        $db = Flight::db();
        $userRepository = new UserRepository($db);
        return $userRepository->findById($id);

    }
}