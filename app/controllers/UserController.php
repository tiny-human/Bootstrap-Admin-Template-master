<?php

class UserController   
{
    public function findById($id)
    {
        $userRepository = new UserRepository(Database::getConnection());
        return $userRepository->findById($id);

    }
}