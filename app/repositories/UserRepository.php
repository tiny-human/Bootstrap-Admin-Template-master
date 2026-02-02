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

  public function findAll() {
    $stmt = $this->pdo->query("SELECT * FROM users");
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
  }
}
