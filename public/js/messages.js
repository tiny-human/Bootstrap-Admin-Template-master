/**
 * Système de messagerie - JavaScript côté client
 * Ce fichier gère toute la logique de la messagerie via AJAX
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('messagesComponent', () => ({
        // État
        conversations: [],
        filteredConversations: [],
        currentMessages: [],
        selectedConversation: null,
        searchQuery: '',
        newMessage: '',
        isTyping: false,
        sidebarVisible: true,
        showEmojiPicker: false,
        currentUserId: null,
        pollingInterval: null,
        
        // Emojis disponibles
        emojis: ['😀', '😂', '😍', '🥰', '😊', '😎', '🤔', '😢', '😡', '👍', '👎', '❤️', '🔥', '✨', '🎉'],

        /**
         * Initialisation du composant
         */
        async init() {
            await this.loadConversations();
            await this.loadUsers();
            
            // Polling pour les nouveaux messages (toutes les 5 secondes)
            this.pollingInterval = setInterval(() => {
                if (this.selectedConversation) {
                    this.loadMessages(this.selectedConversation.id, false);
                }
                this.loadConversations();
            }, 5000);
        },

        /**
         * Charger la liste des conversations
         */
        async loadConversations() {
            try {
                const response = await fetch('/api/messages/conversations');
                const data = await response.json();
                
                if (data.success) {
                    this.conversations = data.conversations.map(conv => ({
                        id: conv.other_user_id,
                        name: conv.other_username,
                        avatar: this.getAvatarUrl(conv.other_username),
                        lastMessage: conv.last_message,
                        lastMessageTime: this.formatTime(conv.last_message_time),
                        unread: parseInt(conv.unread_count) || 0,
                        online: Math.random() > 0.5, // Simulé - à remplacer par vraie logique
                        type: 'Direct',
                        lastSeen: '2 hours ago'
                    }));
                    this.filterConversations();
                }
            } catch (error) {
                console.error('Erreur lors du chargement des conversations:', error);
            }
        },

        /**
         * Charger les utilisateurs disponibles
         */
        async loadUsers() {
            try {
                const response = await fetch('/api/users');
                const data = await response.json();
                
                if (data.success) {
                    // Ajouter les utilisateurs sans conversation existante
                    data.users.forEach(user => {
                        const exists = this.conversations.find(c => c.id == user.id);
                        if (!exists) {
                            this.conversations.push({
                                id: user.id,
                                name: user.username,
                                avatar: this.getAvatarUrl(user.username),
                                lastMessage: 'Démarrer une conversation...',
                                lastMessageTime: '',
                                unread: 0,
                                online: false,
                                type: 'Nouveau',
                                lastSeen: 'Jamais contacté'
                            });
                        }
                    });
                    this.filterConversations();
                }
            } catch (error) {
                console.error('Erreur lors du chargement des utilisateurs:', error);
            }
        },

        /**
         * Charger les messages d'une conversation
         */
        async loadMessages(userId, scroll = true) {
            try {
                const response = await fetch(`/api/messages/${userId}`);
                const data = await response.json();
                
                if (data.success) {
                    this.currentUserId = data.currentUserId;
                    this.currentMessages = data.messages.map(msg => ({
                        id: msg.id,
                        text: msg.content,
                        time: this.formatTime(msg.created_at),
                        sent: msg.sender_id == data.currentUserId,
                        read: msg.is_read == 1
                    }));
                    
                    if (scroll) {
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    }
                    
                    // Mettre à jour le compteur de non-lus
                    const conv = this.conversations.find(c => c.id == userId);
                    if (conv) conv.unread = 0;
                }
            } catch (error) {
                console.error('Erreur lors du chargement des messages:', error);
            }
        },

        /**
         * Sélectionner une conversation
         */
        async selectConversation(conversation) {
            this.selectedConversation = conversation;
            this.sidebarVisible = false; // Cacher sur mobile
            await this.loadMessages(conversation.id);
        },

        /**
         * Envoyer un message
         */
        async sendMessage() {
            if (!this.newMessage.trim() || !this.selectedConversation) return;
            
            const messageText = this.newMessage.trim();
            this.newMessage = '';
            
            // Ajouter immédiatement le message (optimistic update)
            const tempMessage = {
                id: Date.now(),
                text: messageText,
                time: 'Maintenant',
                sent: true,
                read: false
            };
            this.currentMessages.push(tempMessage);
            this.scrollToBottom();
            
            try {
                const response = await fetch('/api/messages/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        receiver_id: this.selectedConversation.id,
                        content: messageText
                    })
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    // En cas d'erreur, retirer le message
                    this.currentMessages = this.currentMessages.filter(m => m.id !== tempMessage.id);
                    alert('Erreur: ' + (data.error || 'Impossible d\'envoyer le message'));
                } else {
                    // Mettre à jour l'ID du message
                    const msg = this.currentMessages.find(m => m.id === tempMessage.id);
                    if (msg) msg.id = data.message_id;
                    
                    // Mettre à jour la conversation
                    this.selectedConversation.lastMessage = messageText;
                    this.selectedConversation.lastMessageTime = 'Maintenant';
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du message:', error);
                this.currentMessages = this.currentMessages.filter(m => m.id !== tempMessage.id);
                alert('Erreur de connexion');
            }
        },

        /**
         * Filtrer les conversations par recherche
         */
        filterConversations() {
            if (!this.searchQuery) {
                this.filteredConversations = this.conversations;
            } else {
                const query = this.searchQuery.toLowerCase();
                this.filteredConversations = this.conversations.filter(conv => 
                    conv.name.toLowerCase().includes(query) ||
                    conv.lastMessage.toLowerCase().includes(query)
                );
            }
        },

        /**
         * Nouvelle conversation
         */
        newConversation() {
            // Afficher la sidebar pour choisir un utilisateur
            this.sidebarVisible = true;
            this.selectedConversation = null;
            this.searchQuery = '';
            this.filterConversations();
        },

        /**
         * Ajouter un emoji
         */
        addEmoji(emoji) {
            this.newMessage += emoji;
            this.showEmojiPicker = false;
        },

        /**
         * Toggle emoji picker
         */
        toggleEmojiPicker() {
            this.showEmojiPicker = !this.showEmojiPicker;
        },

        /**
         * Gérer l'indicateur de frappe
         */
        handleTyping() {
            // Logique pour notifier que l'utilisateur tape
            // (À implémenter avec WebSocket pour le temps réel)
        },

        /**
         * Auto-resize du textarea
         */
        autoResize(event) {
            const textarea = event.target;
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 150) + 'px';
        },

        /**
         * Scroll vers le bas
         */
        scrollToBottom() {
            const container = document.getElementById('chatMessages');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        /**
         * Formater le temps
         */
        formatTime(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            
            // Moins d'une minute
            if (diff < 60000) return 'Maintenant';
            
            // Moins d'une heure
            if (diff < 3600000) return Math.floor(diff / 60000) + ' min';
            
            // Aujourd'hui
            if (date.toDateString() === now.toDateString()) {
                return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            }
            
            // Cette semaine
            if (diff < 604800000) {
                return date.toLocaleDateString('fr-FR', { weekday: 'short' });
            }
            
            // Plus ancien
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
        },

        /**
         * Générer une URL d'avatar
         */
        getAvatarUrl(name) {
            // Utilise un service d'avatar générique
            return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=6366f1&color=fff&size=128`;
        },

        /**
         * Marquer tous comme lus
         */
        markAllRead() {
            this.conversations.forEach(conv => {
                conv.unread = 0;
            });
        },

        /**
         * Appels vidéo/audio (placeholder)
         */
        videoCall() { alert('Appel vidéo non implémenté'); },
        voiceCall() { alert('Appel audio non implémenté'); },
        muteConversation() { alert('Conversation mise en sourdine'); },
        archiveConversation() { alert('Conversation archivée'); },
        deleteConversation() { 
            if (confirm('Supprimer cette conversation ?')) {
                alert('Conversation supprimée');
            }
        },
        toggleAttachment() { alert('Pièce jointe non implémentée'); },
        toggleSidebar() { this.sidebarVisible = !this.sidebarVisible; }
    }));
});
