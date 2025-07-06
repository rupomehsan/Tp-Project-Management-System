<template>
  <div class="chat-layout">
    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <p class="modal-title">Start new conversation</p>
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
    <aside class="chat-sidebar dark-mode">
      <div class="sidebar-header d-flex justify-content-between align-items-center">
        <span>Conversations</span>
        <button class="btn btn-dark btn-sm" @click="openModal">
          <i class="fa fa-plus text-success"></i>
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
          <img v-if="conversation.participant?.image" class="avatar" :src="conversation.participant?.image" alt="" />
          <div v-else class="avatar">{{ getInitials(conversation.participant?.name) }}</div>
          <div class="conversation-info">
            <div class="conversation-name">{{ conversation.participant?.name }}</div>
          </div>
          <span class="conversation-time">{{ formatTime(conversation.last_updated) }}</span>
        </li>
      </ul>
    </aside>

    <!-- Chat Container -->
    <div class="chat-container dark-mode">
      <div class="chat-header d-flex justify-content-between">
        <div class="d-flex align-items-center gap-3 justify-content-center">
          <img v-if="activeConversation?.participant?.image" class="avatar" :src="activeConversation?.participant?.image" alt="" />
          <div v-else class="avatar">{{ getInitials(activeConversation?.participant?.name) }}</div>
          {{ activeConversation?.participant?.name || "..." }}
        </div>
        <button class="btn btn-dark btn-sm" @click="loadMessages(activeConversation)">
          <i class="fa fa-refresh"></i>
        </button>
      </div>

      <div class="chat-messages" ref="chatMessages">
        <div v-if="messages.length === 0 && !selectedFile" class="text-center text-muted mt-4">No messages yet.</div>

        <!-- Existing messages -->
        <div v-for="message in messages" :key="message.id" :class="['chat-bubble', message.type === 'mine' ? 'mine' : 'theirs']">
          <div class="chat-text">
            <template v-if="message.file_url">
              <a :href="message.file_url" target="_blank" rel="noopener noreferrer">📎 Download Attachment</a>
              <br />
            </template>
            {{ message.text }}
          </div>
          <div class="chat-meta">
            <!-- <span class="chat-author">{{ message.sender?.name }}</span> -->
            <span class="chat-time">{{ formatTime(message.created_at) }}</span>
          </div>
        </div>

        <!-- Preview selected file as a temporary message -->
        <div v-if="selectedFile" class="chat-bubble mine temp-message-preview" key="temp-file-preview">
          <div class="chat-text">📎 {{ selectedFile.name }}</div>
          <div class="chat-meta">
            <span class="chat-author">{{ auth_info.name }}</span>
            <span class="chat-time">Sending...</span>
          </div>
        </div>
      </div>

      <form v-if="activeConversation" class="chat-input-area" @submit.prevent="sendMessage" enctype="multipart/form-data">
        <div class="textarea-wrapper" style="position: relative; flex-grow: 1; display: flex; align-items: center; gap: 10px">
          <!-- File name tag inside textarea area (left side) -->
          <label class="file-attach-btn mb-0" for="chat-file-upload" title="Attach file" style="flex-shrink: 0; cursor: pointer">
            <i class="zmdi zmdi-plus"></i>
          </label>

          <!-- Actual textarea -->
          <textarea
            class="chat-input text-area-wrapper"
            placeholder="Type a message..."
            v-model.trim="newMessage"
            :disabled="!activeConversation"
          ></textarea>
        </div>

        <!-- File upload button outside textarea on right side -->

        <input id="chat-file-upload" type="file" ref="fileInput" @change="handleFileUpload" style="display: none" />

        <button type="submit" class="btn btn-primary chat-send-btn" :disabled="(!newMessage && !selectedFile) || !activeConversation">Send</button>
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
      selectedFile: null, // For storing selected file
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
    handleFileUpload(event) {
      this.selectedFile = event.target.files[0];
    },
    removeSelectedFile() {
      this.selectedFile = null;
      this.$refs.fileInput.value = null;
    },
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
      if (!this.newMessage && !this.selectedFile) return;

      try {
        const formData = new FormData();
        formData.append("conversation_id", this.activeConversation.id);

        if (this.newMessage) {
          formData.append("text", this.newMessage);
        }
        if (this.selectedFile) {
          formData.append("attachment", this.selectedFile);
        }

        const res = await axios.post("/messages/send", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        this.messages.push({
          ...res.data.data,
          sender: this.auth_info,
          type: "mine",
        });

        this.newMessage = "";
        this.removeFile(); // reset file after sending
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
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.selectedFile = file;
      }
    },
    removeFile() {
      this.selectedFile = null;
      this.$refs.fileInput.value = null; // reset file input
    },
  },
};
</script>

<style scoped src="./Conversation.css"></style>
