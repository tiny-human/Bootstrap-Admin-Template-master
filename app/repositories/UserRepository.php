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
  
  public function verifyUser( $nom) {
    $sql = "SELECT * FROM user WHERE nom = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$nom]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (empty($user)) {
      $this->create($nom);
      return true;
    }
    return true;
  }

  public function findAll() {
    $stmt = $this->pdo->query("SELECT * FROM users");
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
  }
  
  public function create($nom){
    $sql = "INSERT INTO user(nom) VALUES(?)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$nom]);
    session_start();
    $_SESSION['user'] = $nom;
  }

  public function findByName($nom) {
    $stmt = $this->pdo->prepare("SELECT * FROM user WHERE nom = :nom");
    $stmt->execute(['nom' => $nom]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
