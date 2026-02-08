<?php 

namespace app\models;

use PDO;

class Message {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Envoyer un message
     */
    public function send($senderId, $receiverId, $content) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, content, created_at) 
                VALUES (:sender_id, :receiver_id, :content, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'content' => $content
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Récupérer la conversation entre deux utilisateurs
     */
    public function getConversation($userId1, $userId2, $limit = 50) {
        $sql = "SELECT m.*, 
                   u1.nom as sender_name, 
                   u2.nom as receiver_name
            FROM messages m
            JOIN user u1 ON m.sender_id = u1.id
            JOIN user u2 ON m.receiver_id = u2.id
                WHERE (m.sender_id = :user1 AND m.receiver_id = :user2)
                   OR (m.sender_id = :user2b AND m.receiver_id = :user1b)
                ORDER BY m.created_at ASC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user1', $userId1, PDO::PARAM_INT);
        $stmt->bindValue(':user2', $userId2, PDO::PARAM_INT);
        $stmt->bindValue(':user2b', $userId2, PDO::PARAM_INT);
        $stmt->bindValue(':user1b', $userId1, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer la liste des conversations d'un utilisateur
     */
    public function getConversationsList($userId) {
        $sql = "SELECT 
                    CASE 
                        WHEN m.sender_id = :user_id THEN m.receiver_id 
                        ELSE m.sender_id 
                    END as other_user_id,
                    u.nom as other_username,
                    m.content as last_message,
                    m.created_at as last_message_time,
                    (SELECT COUNT(*) FROM messages 
                     WHERE receiver_id = :user_id2 
                     AND sender_id = CASE WHEN m.sender_id = :user_id3 THEN m.receiver_id ELSE m.sender_id END
                     AND is_read = FALSE) as unread_count
                FROM messages m
                JOIN user u ON u.id = CASE 
                    WHEN m.sender_id = :user_id4 THEN m.receiver_id 
                    ELSE m.sender_id 
                END
                WHERE m.sender_id = :user_id5 OR m.receiver_id = :user_id6
                GROUP BY other_user_id
                ORDER BY m.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'user_id2' => $userId,
            'user_id3' => $userId,
            'user_id4' => $userId,
            'user_id5' => $userId,
            'user_id6' => $userId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marquer les messages comme lus
     */
    public function markAsRead($senderId, $receiverId) {
        $sql = "UPDATE messages SET is_read = TRUE 
                WHERE sender_id = :sender_id AND receiver_id = :receiver_id AND is_read = FALSE";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId
        ]);
    }

    /**
     * Compter les messages non lus
     */
    public function countUnread($userId) {
        $sql = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = :user_id AND is_read = FALSE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}

?>