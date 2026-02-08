<?php

namespace app\controllers;

use Flight;
use app\models\Message;
use app\repositories\UserRepository;

class MessageController   
{
    private $messageModel;
    private $userRepository;

    public function __construct() {
        $db = Flight::db();
        $this->messageModel = new Message($db);
        $this->userRepository = new UserRepository($db);
    }

    /**
     * Afficher la page des messages
     */
    public function showMessages()
    {
        if (!isset($_SESSION['user_id'])) {
            Flight::redirect('/');
            return;
        }

        $currentUserId = (int)$_SESSION['user_id'];
        $currentUser = $_SESSION['user'] ?? 'Utilisateur';

        $users = UserRepository::findAll();
        $conversations = $this->messageModel->getConversationsList($currentUserId);
        $conversationMap = [];
        foreach ($conversations as $conv) {
            $conversationMap[(int)$conv['other_user_id']] = $conv;
        }

        $selectedUserId = (int)(Flight::request()->query['user_id'] ?? 0);
        $selectedUser = null;
        foreach ($users as $u) {
            if ((int)$u['id'] === $selectedUserId) {
                $selectedUser = $u;
                break;
            }
        }

        $messages = [];
        if ($selectedUserId > 0) {
            $messages = $this->messageModel->getConversation($currentUserId, $selectedUserId);
            $this->messageModel->markAsRead($selectedUserId, $currentUserId);
        }

        Flight::render('dist-modern/messages', [
            'currentUser' => $currentUser,
            'currentUserId' => $currentUserId,
            'users' => $users,
            'conversationMap' => $conversationMap,
            'selectedUserId' => $selectedUserId,
            'selectedUser' => $selectedUser,
            'messages' => $messages,
        ]);
    }

    public function sendMessageForm()
    {
        if (!isset($_SESSION['user_id'])) {
            Flight::redirect('/');
            return;
        }

        $currentUserId = (int)$_SESSION['user_id'];
        $data = Flight::request()->data;
        $receiverId = isset($data->receiver_id) ? (int)$data->receiver_id : 0;
        $content = isset($data->content) ? trim((string)$data->content) : '';

        if ($receiverId > 0 && $content !== '') {
            $this->messageModel->send($currentUserId, $receiverId, $content);
        }

        Flight::redirect('/messages?user_id=' . $receiverId);
    }

    /**
     * API: Récupérer la liste des conversations
     */
    public function getConversations()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            Flight::json(['error' => 'Non autorisé'], 401);
            return;
        }

        $userId = $_SESSION['user_id'];
        $conversations = $this->messageModel->getConversationsList($userId);
        
        Flight::json(['success' => true, 'conversations' => $conversations]);
    }

    /**
     * API: Récupérer les messages d'une conversation
     */
    public function getMessages($otherUserId)
    {
        if (!isset($_SESSION['user_id'])) {
            Flight::json(['error' => 'Non autorisé'], 401);
            return;
        }

        $userId = $_SESSION['user_id'];
        $messages = $this->messageModel->getConversation($userId, $otherUserId);
        
        // Marquer les messages reçus comme lus
        $this->messageModel->markAsRead($otherUserId, $userId);
        
        Flight::json(['success' => true, 'messages' => $messages, 'currentUserId' => $userId]);
    }

    /**
     * API: Envoyer un message
     */
    public function sendMessage()
    {
        if (!isset($_SESSION['user_id'])) {
            Flight::json(['error' => 'Non autorisé'], 401);
            return;
        }

        $data = Flight::request()->data;
        $receiverId = $data->receiver_id ?? null;
        $content = $data->content ?? null;

        // Validation
        if (empty($receiverId) || empty($content)) {
            Flight::json(['error' => 'Destinataire et contenu requis'], 400);
            return;
        }

        $senderId = $_SESSION['user_id'];
        
        // Empêcher de s'envoyer un message à soi-même
        if ($senderId == $receiverId) {
            Flight::json(['error' => 'Vous ne pouvez pas vous envoyer un message'], 400);
            return;
        }

        // Envoyer le message
        $messageId = $this->messageModel->send($senderId, $receiverId, $content);
        
        Flight::json([
            'success' => true, 
            'message_id' => $messageId,
            'message' => 'Message envoyé avec succès'
        ]);
    }

    /**
     * API: Récupérer tous les utilisateurs (pour démarrer une nouvelle conversation)
     */
    public function getUsers()
    {
        if (!isset($_SESSION['user_id'])) {
            Flight::json(['error' => 'Non autorisé'], 401);
            return;
        }

        $users = $this->userRepository->findAll();
        $currentUserId = $_SESSION['user_id'];
        
        // Filtrer l'utilisateur courant de la liste
        $filteredUsers = array_filter($users, function($user) use ($currentUserId) {
            return $user->id != $currentUserId;
        });
        
        Flight::json(['success' => true, 'users' => array_values($filteredUsers)]);
    }

    /**
     * API: Marquer les messages comme lus
     */
    public function markAsRead($senderId)
    {
        if (!isset($_SESSION['user_id'])) {
            Flight::json(['error' => 'Non autorisé'], 401);
            return;
        }

        $receiverId = $_SESSION['user_id'];
        $this->messageModel->markAsRead($senderId, $receiverId);
        
        Flight::json(['success' => true]);
    }

    /**
     * API: Compter les messages non lus
     */
    public function getUnreadCount()
    {
        if (!isset($_SESSION['user_id'])) {
            Flight::json(['error' => 'Non autorisé'], 401);
            return;
        }

        $count = $this->messageModel->countUnread($_SESSION['user_id']);
        Flight::json(['success' => true, 'count' => $count]);
    }
}