<template>
  <div>
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5 class="text-capitalize">
            {{ param_id ? `${setup.edit_page_title}` : `${setup.create_page_title}` }}
          </h5>
          <div>
            <router-link class="btn btn-outline-warning btn-sm" :to="{ name: `All${setup.route_prefix}` }">
              {{ setup.all_page_title }}
            </router-link>
          </div>
        </div>
        <div class="card-body">
          <div class="attendance-form-container">
            

            <!-- Time Input Grid -->
            <div class="time-grid">
              <!-- Check In Card -->
              <div class="time-card check-in-card">
                <div class="time-card-header">
                  <div class="time-icon check-in-icon">
                    <i class="fa fa-play"></i>
                  </div>
                  <div class="time-label">
                    <h6>Check In</h6>
                    <small>Start of workday</small>
                  </div>
                </div>
                <div class="time-input-wrapper">
                  <input
                    type="datetime-local"
                    class="time-input"
                    name="check_in"
                    id="check_in"
                    v-model="form_fields.check_in"
                    :min="todayStart"
                    :max="todayEnd"
                    :disabled="param_id"
                    placeholder="Select check-in time"
                  />
                  <div class="time-display">
                    <span v-if="form_fields.check_in" class="time-value">
                      {{ formatDisplayTime(form_fields.check_in) }}
                    </span>
                    <span v-else class="time-placeholder"> No time selected </span>
                  </div>
                </div>
              </div>

              <!-- Arrow Connector -->
              <div class="time-connector" v-if="form_fields.check_in && param_id">
                <i class="fa fa-arrow-right"></i>
              </div>

              <!-- Check Out Card -->
              <div class="time-card check-out-card" v-if="form_fields.check_in && param_id">
                <div class="time-card-header">
                  <div class="time-icon check-out-icon">
                    <i class="fa fa-stop"></i>
                  </div>
                  <div class="time-label">
                    <h6>Check Out</h6>
                    <small>End of workday</small>
                  </div>
                </div>
                <div class="time-input-wrapper">
                  <input
                    type="datetime-local"
                    class="time-input"
                    :class="{ 'warning-input': isCheckoutBefore7PM }"
                    name="check_out"
                    id="check_out"
                    v-model="form_fields.check_out"
                    :min="form_fields.check_in || todayStart"
                    :max="todayEnd"
                    :disabled="!form_fields.check_in"
                    placeholder="Select check-out time"
                  />
                  <div class="time-display">
                    <span v-if="form_fields.check_out" class="time-value">
                      {{ formatDisplayTime(form_fields.check_out) }}
                    </span>
                    <span v-else class="time-placeholder"> No time selected </span>
                  </div>
                  <div class="early-warning" v-if="isCheckoutBefore7PM">
                    <i class="fa fa-exclamation-circle"></i>
                    <span>Early checkout</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Early Checkout Notice -->
            <div class="early-checkout-notice" v-if="isCheckoutBefore7PM">
              <div class="notice-card">
                <div class="notice-header">
                  <div class="notice-icon">
                    <i class="fa fa-info-circle"></i>
                  </div>
                  <div class="notice-content">
                    <h6>Early Checkout Detected</h6>
                    <p>You're checking out before 7:00 PM. Please provide a reason.</p>
                  </div>
                </div>
                <div class="notes-input-section">
                  <div class="input-group">
                    <div class="input-label">
                      <label for="notes">Reason for early checkout</label>
                      <span class="required-badge">Required</span>
                    </div>
                    <textarea
                      class="notes-textarea"
                      name="notes"
                      id="notes"
                      v-model="form_fields.notes"
                      placeholder="Please provide a detailed reason for early checkout (e.g., medical appointment, family emergency, personal matter, etc.)"
                      rows="4"
                      maxlength="500"
                      required
                    ></textarea>
                    <div class="input-footer">
                      <span class="char-count" :class="{ 'char-limit': form_fields.notes.length > 450 }"> {{ form_fields.notes.length }}/500 </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-actions d-flex justify-content-center mt-4">
              <button type="submit" class="btn btn-light btn-square px-5"><i class="icon-lock"></i> Submit</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { store } from "../store";
import setup from "../setup";
import TextEditor from "../../../../../../GlobalComponents/FormComponents/TextEditor.vue";
export default {
  components: { TextEditor },
  data: () => ({
    setup,
    param_id: null,
    isSubmitting: false,
    form_fields: {
      user_id: "",
      date: "",
      check_in: "",
      check_out: "",
      attendance_status: "",
      notes: "",
    },
    userProject: [],
    userData: [],
  }),
  created: async function () {
    let id = (this.param_id = this.$route.params.id);
    let project_id = this.$route.query.project_id;
    if (project_id) {
      this.form_fields.project_id = project_id;
    }

    if (id) {
      this.set_fields(id);
    }

    await this.get_project_data();
    await this.get_user_data();
  },
  methods: {
    ...mapActions(store, {
      create: "create",
      update: "update",
      details: "details",
      get_all: "get_all",
      set_only_latest_data: "set_only_latest_data",
    }),

    async get_project_data() {
      try {
        let res = await axios.get("/project");
        this.userProject = res.data.data; // ✅ Assign data properly
      } catch (error) {
        console.error("Error fetching project group", error);
      }
    },
    async get_user_data() {
      try {
        let res = await axios.get("/users");
        this.userData = res.data.data; // ✅ Assign data properly
      } catch (error) {
        console.error("Error fetching user", error);
      }
    },

    set_fields: async function (id) {
      this.param_id = id;
      await this.details(id);
      if (this.item) {
        this.form_fields.user_id = this.item.user_id;
        this.form_fields.date = this.item.date;
        this.form_fields.check_in = this.item.check_in;
        this.form_fields.check_out = this.item.check_out;
        this.form_fields.attendance_status = this.item.attendance_status;
        this.form_fields.notes = this.item.notes || "";
      }
    },

    submitHandler: async function ($event) {
      // Validate notes if checkout is before 7 PM
      if (this.isCheckoutBefore7PM && !this.form_fields.notes.trim()) {
        window.s_warning("Notes are required when checking out before 7:00 PM");
        return;
      }

      this.isSubmitting = true;

      try {
        // this.set_only_latest_data(true);
        if (this.param_id) {
          // this.setSummerEditor();
          let response = await this.update($event);
          // await this.get_all();
          if ([200, 201].includes(response.status)) {
            window.s_alert("Attendance record updated successfully");
            this.$router.push({
              name: `All${this.setup.route_prefix}`,
            });
          }
        } else {
          // this.setSummerEditor();
          let response = await this.create($event);
          // await this.get_all();
          if ([200, 201].includes(response.status)) {
            window.s_alert("Attendance record created successfully");
            this.$router.push({
              name: `All${this.setup.route_prefix}`,
            });
          }
        }
      } catch (error) {
        window.s_warning("An error occurred. Please try again.");
      } finally {
        this.isSubmitting = false;
      }
    },
    setSummerEditor() {
      var markupStr = $("#description").summernote("code");
      var target = document.createElement("input");
      target.setAttribute("name", "description");
      target.value = markupStr;
      document.getElementById("description").appendChild(target);
    },

    formatDisplayTime(datetime) {
      if (!datetime) return "Not set";
      try {
        const date = new Date(datetime);
        return date.toLocaleString("en-US", {
          weekday: "short",
          month: "short",
          day: "numeric",
          hour: "2-digit",
          minute: "2-digit",
          hour12: true,
        });
      } catch (error) {
        return "Invalid time";
      }
    },
  },
  computed: {
    ...mapState(store, {
      item: "item",
    }),

    todayDate() {
      const now = new Date();
      // Get local date in YYYY-MM-DD format (timezone adjusted)
      return now.toLocaleDateString("en-CA"); // 'en-CA' gives YYYY-MM-DD format
    },

    todayStart() {
      const today = new Date();
      // Set to start of day in local timezone
      today.setHours(0, 0, 0, 0);
      // Convert to ISO format but keep local time
      const localISO = new Date(today.getTime() - today.getTimezoneOffset() * 60000).toISOString();
      return localISO.slice(0, 16);
    },

    todayEnd() {
      const today = new Date();
      // Set to end of day in local timezone
      today.setHours(23, 59, 59, 999);
      // Convert to ISO format but keep local time
      const localISO = new Date(today.getTime() - today.getTimezoneOffset() * 60000).toISOString();
      return localISO.slice(0, 16);
    },

    isCheckoutBefore7PM() {
      if (!this.form_fields.check_out) return false;

      try {
        const checkoutDateTime = new Date(this.form_fields.check_out);
        if (isNaN(checkoutDateTime.getTime())) return false;

        const checkoutHour = checkoutDateTime.getHours();
        const checkoutMinute = checkoutDateTime.getMinutes();
        const checkoutTimeInMinutes = checkoutHour * 60 + checkoutMinute;
        const sevenPMInMinutes = 19 * 60; // 19:00 = 1140 minutes

        return checkoutTimeInMinutes < sevenPMInMinutes;
      } catch (error) {
        return false;
      }
    },
  },
};
</script>

<style scoped>
/* === MODERN PROFESSIONAL DESIGN === */

/* Main Container */
.attendance-form-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 2rem;
}

/* Form Header */
.form-header {
  margin-bottom: 3rem;
}

.header-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.header-icon i {
  font-size: 2rem;
  color: white;
}

.header-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 0.5rem;
}

.header-subtitle {
  font-size: 1rem;
  color: #718096;
  margin: 0;
}

/* Time Grid Layout */
.time-grid {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 2rem;
  margin-bottom: 3rem;
  flex-wrap: wrap;
}

/* Time Cards */
.time-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
  border: 1px solid #e2e8f0;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  min-width: 280px;
  position: relative;
  overflow: hidden;
}

.time-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #667eea, #764ba2);
}

.time-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
}

.check-in-card::before {
  background: linear-gradient(90deg, #48bb78, #38a169);
}

.check-out-card::before {
  background: linear-gradient(90deg, #ed8936, #dd6b20);
}

/* Time Card Header */
.time-card-header {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
}

.time-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
}

.check-in-icon {
  background: linear-gradient(135deg, #48bb78, #38a169);
  color: white;
}

.check-out-icon {
  background: linear-gradient(135deg, #ed8936, #dd6b20);
  color: white;
}

.time-label h6 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2d3748;
  margin: 0;
}

.time-label small {
  color: #718096;
  font-size: 0.875rem;
}

/* Time Input */
.time-input-wrapper {
  position: relative;
}

.time-input {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 500;
  color: #2d3748;
  background: #f7fafc;
  transition: all 0.3s ease;
  margin-bottom: 1rem;
}

.time-input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.warning-input {
  border-color: #f6ad55 !important;
  background: #fffaf0 !important;
}

.warning-input:focus {
  border-color: #ed8936 !important;
  box-shadow: 0 0 0 3px rgba(237, 137, 54, 0.1) !important;
}

/* Time Display */
.time-display {
  background: #f7fafc;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
}

.time-value {
  font-size: 0.9rem;
  font-weight: 600;
  color: #2d3748;
}

.time-placeholder {
  font-size: 0.9rem;
  color: #a0aec0;
  font-style: italic;
}

/* Early Warning */
.early-warning {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #dd6b20;
  font-size: 0.875rem;
  font-weight: 500;
  margin-top: 0.5rem;
}

/* Time Connector */
.time-connector {
  font-size: 1.5rem;
  color: #cbd5e0;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Early Checkout Notice */
.early-checkout-notice {
  margin-top: 2rem;
  animation: slideInUp 0.4s ease-out;
}

.notice-card {
  background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
  border: 1px solid #fed7aa;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(251, 146, 60, 0.15);
}

.notice-header {
  display: flex;
  align-items: flex-start;
  margin-bottom: 1.5rem;
}

.notice-icon {
  width: 40px;
  height: 40px;
  background: #f97316;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  flex-shrink: 0;
}

.notice-icon i {
  color: white;
  font-size: 1.25rem;
}

.notice-content h6 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #9a3412;
  margin: 0 0 0.5rem 0;
}

.notice-content p {
  color: #c2410c;
  margin: 0;
  line-height: 1.5;
}

/* Notes Input Section */
.input-group {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #fed7aa;
}

.input-label {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.input-label label {
  font-weight: 600;
  color: #2d3748;
  font-size: 1rem;
}

.required-badge {
  background: #ef4444;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.notes-textarea {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.95rem;
  line-height: 1.6;
  resize: vertical;
  min-height: 120px;
  transition: all 0.3s ease;
}

.notes-textarea:focus {
  outline: none;
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.input-footer {
  display: flex;
  justify-content: flex-end;
  margin-top: 0.5rem;
}

.char-count {
  font-size: 0.875rem;
  color: #718096;
  font-weight: 500;
}

.char-limit {
  color: #ef4444;
}

/* Form Footer */
.form-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-radius: 0 0 12px 12px;
}

.footer-info {
  display: flex;
  align-items: center;
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.status-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #cbd5e0;
  transition: all 0.3s ease;
}

.status-dot.active {
  background: #48bb78;
  box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.2);
}

.status-dot.warning {
  background: #f6ad55;
  box-shadow: 0 0 0 3px rgba(246, 173, 85, 0.2);
}

.status-text {
  font-size: 0.875rem;
  color: #4a5568;
  font-weight: 500;
}

.footer-actions {
  display: flex;
  gap: 1rem;
}

/* Buttons */
.btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  border: none;
  position: relative;
  overflow: hidden;
}

.btn-cancel {
  background: #f7fafc;
  color: #4a5568;
  border: 2px solid #e2e8f0;
}

.btn-cancel:hover {
  background: #edf2f7;
  color: #2d3748;
  transform: translateY(-1px);
  text-decoration: none;
}

.btn-submit {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  min-width: 160px;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-submit.btn-warning {
  background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
  box-shadow: 0 4px 15px rgba(246, 173, 85, 0.3);
}

.btn-submit.btn-warning:hover:not(:disabled) {
  box-shadow: 0 8px 25px rgba(246, 173, 85, 0.4);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-loader {
  position: absolute;
  right: 1rem;
}

/* Animations */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .attendance-form-container {
    padding: 1rem;
  }

  .time-grid {
    flex-direction: column;
    gap: 1.5rem;
  }

  .time-connector {
    transform: rotate(90deg);
  }

  .form-footer {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .footer-actions {
    width: 100%;
    justify-content: center;
  }

  .btn {
    flex: 1;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .time-card {
    min-width: auto;
    padding: 1.5rem;
  }

  .header-icon {
    width: 60px;
    height: 60px;
  }

  .header-icon i {
    font-size: 1.5rem;
  }

  .footer-actions {
    flex-direction: column;
  }
}
</style>
