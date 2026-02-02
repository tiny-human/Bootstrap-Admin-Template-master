<?php 

namespace app\models;
class Message{
   public $db;
    
    public function __construct($db){
        $this->db = $db;
    }

}

?>