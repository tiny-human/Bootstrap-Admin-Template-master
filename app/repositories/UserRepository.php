<?php

namespace app\repositories;
use app\models;

use PDO;


class UserRepository {
  private $pdo;
  public function __construct($pdo) { $this->pdo = $pdo; }

  public function findById($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function verifyUser($email,$nom,$mdp){
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(empty($users)){
      $sql2 = "INSERT INTO user(nom,email,mdp) VALUES(?,?,?)";
      $stmt2 = $this->pdo->prepare($sql);
      $stmt2->execute([$nom,$email,$mdp]);

      
    }

    

  }
  public function findAll() {
    $stmt = $this->pdo->query("SELECT * FROM users");
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
  }
  // public function create($nom,$email){
  //   $sql = "INSERT INTO user(nom,email) VALUES(?,?)";
  //   $stmt = $this->pdo->prepare($sql);
  //   $stmt->execute([$nom,$email]);
  // }

}
