<?php 

namespace app\models;
class User{
    public $id;
    public $nom;
    
    public function __construct($id, $nom, $prenom, $email, $password_hash, $telephone){
        $this->id = $id;
        $this->nom = $nom;
    }

}

?>