<?php

namespace app\repositories;
use app\models;
use Flight;
use PDO;


class UserRepository {
  private $pdo;
  public function __construct($pdo) { $this->pdo = $pdo; }

  public function findById($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM user WHERE id = :id");
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
      return $this->create($nom);
    }
    
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    $_SESSION['user'] = $user['nom'];
    $_SESSION['user_id'] = $user['id_utilisateur'];
    
    return $user['id_utilisateur'];
  }

  public static function findAll() {
    $sql = "SELECT * FROM user";
    $stmt = Flight::db()->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  }
  
  public function create($nom){
    $sql = "INSERT INTO user(nom) VALUES(?)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$nom]);
    
    $userId = $this->pdo->lastInsertId();
    
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    $_SESSION['user'] = $nom;
    $_SESSION['user_id'] = $userId;
    
    return $userId;
  }

  public function findByName($nom) {
    $stmt = $this->pdo->prepare("SELECT * FROM user WHERE nom = :nom");
    $stmt->execute(['nom' => $nom]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
