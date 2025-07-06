<template>
  <div class="chat-layout">
    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <span class="modal-title text-dark">Start New Conversation</span>
          <button class="close-btn" @click="showModal = false">&times;</button>
        </div>
        <div class="modal-body">
          <select v-model="selectedUserId" class="form-control">
            <option value="">Select User</option>
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
          </select>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showModal = false">Cancel</button>
          <button class="btn btn-primary" @click="createConversation">Create</button>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <aside class="chat-sidebar">
      <div class="sidebar-header d-flex justify-content-between">
        <span>Conversations</span>
        <button class="btn btn-link p-0" @click="openModal">
          <i class="zmdi zmdi-plus-circle"></i>
        </button>
      </div>
      <ul class="conversation-list">
        <li
          v-for="conversation in conversations"
          :key="conversation.id"
          @click="loadMessages(conversation)"
          class="conversation-item"
          :class="{ active: conversation.id === activeConversation?.id }"
        >
          <div class="avatar">{{ getInitials(conversation.participant?.name) }}</div>
          <div class="conversation-info">
            <div class="conversation-name">{{ conversation.participant?.name }}</div>
          </div>
          <span class="conversation-time">{{ formatTime(conversation.last_updated) }}</span>
        </li>
      </ul>
    </aside>

    <!-- Chat Container -->
    <div class="chat-container">
      <div class="chat-header d-flex justify-content-between">
        <div class="chat-header-title">Chat with {{ activeConversation?.participant?.name || "..." }}</div>
        <button class="btn btn-link p-0" @click="loadMessages(activeConversation)">
          <i class="zmdi zmdi-refresh"></i>
        </button>
      </div>

      <div class="chat-messages" ref="chatMessages">
        <div v-if="messages.length === 0" class="text-center text-muted mt-4">No messages yet.</div>
        <div v-for="message in messages" :key="message.id" :class="['chat-bubble', message.type === 'mine' ? 'mine' : 'theirs']">
          <div class="chat-text">{{ message.text }}</div>
          <div class="chat-meta">
            <span class="chat-author">{{ message.sender?.name }}</span>
            <span class="chat-time">{{ formatTime(message.created_at) }}</span>
          </div>
        </div>
      </div>

      <form class="chat-input-area" @submit.prevent="sendMessage">
        <textarea class="chat-input" placeholder="Type a message..." v-model.trim="newMessage" :disabled="!activeConversation" />
        <button type="submit" class="btn btn-primary chat-send-btn" :disabled="!newMessage || !activeConversation">Send</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { auth_store } from "../../../../../GlobalStore/auth_store";
import { mapState } from "pinia";

export default {
  data() {
    return {
      showModal: false,
      selectedUserId: "",
      newMessage: "",
      users: [],
      conversations: [],
      messages: [],
      activeConversation: null,
    };
  },
  computed: {
    ...mapState(auth_store, {
      auth_info: "auth_info",
    }),
  },
  mounted() {
    this.loadConversations();

    const userId = this.auth_info?.id;
    if (userId) {
      window.Echo.private(`chat.${userId}`)
        .listen("MessageSent", (e) => {
          // Avoid duplicates
          const exists = this.messages.some((m) => m.id === e.message.id);
          if (!exists) {
            // Add only if it belongs to current conversation
            if (this.activeConversation?.id === e.message.conversation_id) {
              this.messages.push({
                ...e.message,
                sender: e.sender,
                type: e.message.sender_id === this.auth_info.id ? "mine" : "theirs",
              });
              this.scrollToBottom();
            } else {
              // If message is from another conversation, refresh conversation list
              this.loadConversations();
            }
          }
        })
        .error((error) => {
          console.error("❌ Pusher subscription error:", error);
        })
        .subscribed(() => {
          console.log("✅ Subscribed to chat." + userId);
        });
    }
  },
  methods: {
    async loadConversations() {
      try {
        const res = await axios.get("/messages/get-all-conversations");
        this.conversations = res.data.data;
      } catch (err) {
        console.error("Failed to load conversations", err);
      }
    },
    async loadUsers() {
      try {
        const res = await axios.get("users?get_all=1");
        this.users = res.data.data;
      } catch (err) {
        console.error("Failed to load users", err);
      }
    },
    openModal() {
      this.showModal = true;
      if (this.users.length === 0) {
        this.loadUsers();
      }
    },
    async createConversation() {
      if (!this.selectedUserId) return;
      try {
        await axios.post("/messages/start-conversation", {
          participant_id: this.selectedUserId,
        });
        this.showModal = false;
        this.selectedUserId = "";
        await this.loadConversations();
      } catch (err) {
        console.error("Failed to create conversation", err);
      }
    },
    async loadMessages(convo) {
      if (!convo) return;
      this.activeConversation = convo;
      try {
        const res = await axios.get(`/messages/get-conversation-messages/${convo.id}`);

        this.messages = res.data.data.map((m) => ({
          ...m,
          type: m.sender?.id === this.auth_info.id ? "mine" : "theirs",
        }));

        this.scrollToBottom();
      } catch (err) {
        console.error("Failed to load messages", err);
      }
    },
    async sendMessage() {
      if (!this.newMessage) return;
      try {
        const payload = {
          conversation_id: this.activeConversation.id,
          text: this.newMessage,
        };
        const res = await axios.post("/messages/send", payload);

        // Push immediately (optimistic UI)
        this.messages.push({
          ...res.data.data,
          sender: this.auth_info,
          type: "mine",
        });
        this.newMessage = "";
        this.scrollToBottom();
      } catch (err) {
        console.error("Failed to send message", err);
      }
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.chatMessages;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    },
    getInitials(name) {
      return name?.charAt(0).toUpperCase() || "U";
    },
    formatTime(time) {
      if (!time) return "";
      return new Date(time).toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    },
  },
};
</script>

<style scoped src="./Conversation.css"></style>
