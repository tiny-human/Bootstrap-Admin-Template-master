<?php

namespace app\repositories;
use app\models;
use Flight;
use PDO;


class MessageRepository{
  private $pdo;
  public function __construct($pdo) { $this->pdo = $pdo; }

  public function getMessage($sender,$receiver){
    $sql = " SELECT * FROM message WHERE id_user_envoyer = ? AND id_user_recepteur = ? ORDER BY date_envoi DESC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$receiver,$sender]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  

}
