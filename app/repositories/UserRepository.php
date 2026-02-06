<?php

namespace app\repositories;
use app\models;
use Flight;
use PDO;


class UserRepository {
  private $pdo;
  public function __construct($pdo) { $this->pdo = $pdo; }

  public function findById($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getHash($email){
    $sql = "SELECT mdp FROM user WHERE email = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$email]);
    $mdp = $stmt->fetch(PDO::FETCH_ASSOC);
    return $mdp['mdp'];


  }
  
  public function verifyUser($email, $nom, $mdp) {
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (empty($user)) {
      $hash = password_hash($mdp, PASSWORD_DEFAULT);
      $this->createAndRedirect($nom, $email, $hash);
      return true;
    }
    
    return password_verify($mdp, $user['mdp']);
  }
  public function findAll() {
    $stmt = $this->pdo->query("SELECT * FROM users");
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
  }
  public function createAndRedirect($nom,$email,$hash_mdp){
    $sql = "INSERT INTO user(nom,email,mdp) VALUES(?,?,?)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$nom,$email,$hash_mdp]);
    session_start();
    $_SESSION['user'] = $nom;
    Flight::redirect('/home');
  }

}
