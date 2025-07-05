<template>
  <div class="chat-layout">
    <!-- Sidebar -->
    <aside class="chat-sidebar">
      <div class="sidebar-header d-flex justify-content-between">
        <span>Conversations</span>
        <button class="btn btn-link p-0" @click="showModal = true">
          <i class="zmdi zmdi-plus-circle"></i>
        </button>
      </div>

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
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="showModal = false">Cancel</button>
            <button class="btn btn-primary" @click="createConversation">Create</button>
          </div>
        </div>
      </div>

      <!-- Conversation List -->
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
          <span class="conversation-time">
            {{ formatTime(conversation.last_updated) }}
          </span>
        </li>
      </ul>
    </aside>

    <!-- Chat Area -->
    <div class="chat-container">
      <div class="chat-header d-flex justify-content-between">
        <div class="chat-header-title">Chat with {{ activeConversation?.participant?.name || "..." }}</div>
        <button class="btn btn-link p-0" @click="loadMessages(activeConversation)">
          <i class="zmdi zmdi-refresh"></i>
        </button>
      </div>

      <div class="chat-messages" ref="chatMessages">
        <div v-if="messages.length === 0" class="text-center text-muted" style="margin-top: 30px">No messages yet.</div>
        <div v-for="message in messages" :key="message.id" :class="['chat-bubble', message.type === 'mine' ? 'mine' : 'theirs']">
          <div class="chat-text">{{ message.text }}</div>
          <div class="chat-meta">
            <span class="chat-author">{{ message.sender?.name }}</span>
            <span class="chat-time">{{ formatTime(message.created_at) }}</span>
          </div>
        </div>
      </div>

      <form class="chat-input-area" @submit.prevent="sendMessage">
        <textarea class="chat-input" placeholder="Type a message..." v-model.trim="newMessage" :disabled="!activeConversation"></textarea>
        <button type="submit" class="btn btn-primary chat-send-btn" :disabled="!newMessage || !activeConversation">Send</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

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
      pollInterval: null,
    };
  },
  mounted() {
    this.loadConversations();
    this.loadUsers();
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
      try {
        this.activeConversation = convo;
        const res = await axios.get(`/messages/get-conversation-messages/${convo.id}`);
        this.messages = res.data.data;
        this.scrollToBottom();
        // this.startPolling();
      } catch (err) {
        console.error("Failed to load messages", err);
      }
    },
    async sendMessage() {
      try {
        const payload = {
          conversation_id: this.activeConversation.id,
          text: this.newMessage,
        };
        const res = await axios.post("/messages/send", payload);
        this.messages.push({
          ...res.data.data,
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
        container.scrollTop = container.scrollHeight;
      });
    },
    getInitials(name) {
      return name?.charAt(0).toUpperCase() || "U";
    },
    formatTime(time) {
      if (!time) return "";
      return new Date(time).toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    },

    startPolling() {
      clearInterval(this.pollInterval);
      this.pollInterval = setInterval(() => {
        if (this.activeConversation) {
          this.loadMessages(this.activeConversation);
        }
      }, 5000);
    },
  },
  beforeUnmount() {
    clearInterval(this.pollInterval);
  },
};
</script>

<style scoped>
.chat-layout {
  display: flex;
  height: 80vh;
  max-width: 900px;
  margin: 0 auto;
  background: #f5f6fa;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  border: 1px solid #e0e0e0;
}
.chat-sidebar {
  width: 260px;
  background: #fff;
  border-radius: 12px 0 0 12px;
  border-right: 1px solid #e0e0e0;
  display: flex;
  flex-direction: column;
  padding: 0;
}
.sidebar-header {
  font-size: 1.1rem;
  font-weight: 600;
  color: #222;
  padding: 18px 20px 12px 20px;
  border-bottom: 1px solid #e0e0e0;
}
.conversation-list {
  list-style: none;
  margin: 0;
  padding: 0;
  flex: 1;
  overflow-y: auto;
}
.conversation-item {
  display: flex;
  align-items: center;
  padding: 14px 18px;
  cursor: pointer;
  border-bottom: 1px solid #f0f0f0;
  transition: background 0.2s;
  min-width: 250px;
}
.conversation-item.active,
.conversation-item:hover {
  background: #f5f6fa;
}
.avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.1rem;
  color: #555;
  margin-right: 14px;
}
.conversation-info {
  flex: 1;
  min-width: 0;
}
.conversation-name {
  font-weight: 600;
  color: #222;
  font-size: 1rem;
  margin-bottom: 2px;
}
.conversation-last {
  font-size: 0.92em;
  color: #888;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.conversation-time {
  font-size: 0.8em;
  color: #bbb;
  margin-left: 10px;
}
.chat-container {
  display: flex;
  flex-direction: column;
  height: 80vh;
  width: 100%;
  margin: 0 auto;
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  background: #f5f6fa;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}
.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  background: #fff;
  border-bottom: 1px solid #e0e0e0;
  border-radius: 0 12px 0 0;
}
.chat-header-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #222;
}
.chat-header-actions {
  display: flex;
  gap: 8px;
}
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 24px 16px 16px 16px;
  display: flex;
  flex-direction: column;
  background: #f5f6fa;
}
.chat-bubble {
  max-width: 70%;
  margin-bottom: 14px;
  padding: 12px 18px;
  border-radius: 18px;
  position: relative;
  word-break: break-word;
  font-size: 1rem;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}
.mine {
  align-self: flex-end;
  background: #dcf8c6;
  color: #222;
  border-bottom-right-radius: 4px;
}
.theirs {
  align-self: flex-start;
  background: #fff;
  color: #222;
  border-bottom-left-radius: 4px;
  border: 1px solid #e0e0e0;
}
.chat-meta {
  display: flex;
  justify-content: space-between;
  margin-top: 6px;
  font-size: 0.8em;
  color: #888;
}
.chat-input-area {
  display: flex;
  align-items: center;
  padding: 14px 16px;
  border-top: 1px solid #e0e0e0;
  background: #fff;
  border-radius: 0 0 12px 12px;
}
.chat-input {
  flex: 1;
  resize: none;
  border-radius: 18px;
  border: 1px solid #ccc;
  padding: 10px 14px;
  margin-right: 10px;
  font-size: 1rem;
  background: #f9f9f9;
}
.chat-send-btn {
  padding: 8px 22px;
  border-radius: 18px;
  font-weight: 600;
  font-size: 1rem;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-content {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  width: 350px;
  max-width: 90vw;
  padding: 0;
  overflow: hidden;
}
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid #eee;
}
.modal-title {
  font-size: 1.1rem;
  font-weight: 600;
}
.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #888;
}
.modal-body {
  padding: 20px;
}
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 12px 20px 16px 20px;
  border-top: 1px solid #eee;
}
</style>
