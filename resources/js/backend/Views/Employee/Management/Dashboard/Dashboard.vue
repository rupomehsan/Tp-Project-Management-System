<template>
  <div class="container-fluid px-4 py-3 my-3" :data-theme="currentTheme">
    <!--Start Dashboard Content-->

    <div class="row">
      <div class="col-md-3">
        <div class="card profile-card shadow-lg">
          <!-- Profile Header -->
          <div class="profile-header">
            <div class="profile-bg"></div>
            <div class="profile-image-container text-center">
              <div class="profile-avatar">
                <img class="avatar-img" :src="`${auth_info.image ?? 'avatar.png'}`" @error="$event.target.src = 'avatar.png'" alt="Profile Picture" />
                <!-- Professional Circular Rating Display -->
                <div class="rating-badge">
                  <div class="rating-circle" :data-rating="dashboard_data.user_rating || 0">
                    <div class="rating-value">{{ dashboard_data.user_rating || 0 }}</div>
                    <div class="rating-scale">/10</div>
                  </div>
                </div>
              </div>

              <!-- Profile Name Section -->
              <div class="profile-name mt-3">
                <h3 class="mb-1">{{ auth_info.name }}</h3>
                <p class="mb-0">{{ auth_info.designation }}</p>
              </div>
            </div>
          </div>

          <!-- Profile Information -->
          <div class="card-body">
            <div class="profile-details">
              <div class="detail-row">
                <div class="detail-icon">
                  <i class="zmdi zmdi-email text-primary"></i>
                </div>
                <div class="detail-content">
                  <span class="detail-label">Email</span>
                  <span class="detail-value">{{ auth_info.email }}</span>
                </div>
              </div>

              <div class="detail-row">
                <div class="detail-icon">
                  <i class="zmdi zmdi-phone text-success"></i>
                </div>
                <div class="detail-content">
                  <span class="detail-label">Phone</span>
                  <span class="detail-value">{{ auth_info.phone_number }}</span>
                </div>
              </div>

              <div class="detail-row">
                <div class="detail-icon">
                  <i class="zmdi zmdi-pin text-danger"></i>
                </div>
                <div class="detail-content">
                  <span class="detail-label">Address</span>
                  <span class="detail-value">{{ auth_info.address ?? "N/A" }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Social Media Section -->
          <div class="social-section">
            <div class="social-header">
              <h6 class="social-title"><i class="zmdi zmdi-share mr-2"></i>Professional Networks</h6>
            </div>
            <div class="social-content">
              <div class="social-links" v-if="auth_info.social_media && auth_info.social_media.length > 0">
                <a
                  v-for="(social, index) in auth_info.social_media"
                  :key="index"
                  :href="social.link"
                  target="_blank"
                  :title="social.name"
                  class="social-link"
                  :class="getSocialMediaClass(social.name)"
                >
                  <i :class="getSocialMediaIcon(social.name)"></i>
                  <div class="social-tooltip">{{ social.name }}</div>
                </a>
              </div>
              <div class="no-social-state" v-else>
                <i class="zmdi zmdi-info zmdi-hc-2x"></i>
                <p class="mb-0">No social media links available</p>
              </div>
            </div>
          </div>
        </div>
        <!-- Stats Cards Section -->
        <!-- TOTAL PROJECT -->
      </div>
      <div class="col-lg-9 col-md-9">
        <div class="row stats-row mb-4">
          <!-- Projects Card -->
          <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-3">
            <div class="stat-card stat-card-primary">
              <div class="stat-card-body">
                <div class="stat-icon-container">
                  <div class="stat-icon bg-primary">
                    <i class="zmdi zmdi-assignment"></i>
                  </div>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ dashboard_data.total_projects || 0 }}</div>
                  <div class="stat-label">Total Projects</div>
                  <div class="stat-trend">
                    <span class="trend-indicator positive">
                      <i class="zmdi zmdi-trending-up"></i>
                      Active
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Tasks Card -->
          <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-3">
            <div class="stat-card stat-card-warning">
              <div class="stat-card-body">
                <div class="stat-icon-container">
                  <div class="stat-icon bg-warning">
                    <i class="zmdi zmdi-assignment-check"></i>
                  </div>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ dashboard_data.total_tasks || 0 }}</div>
                  <div class="stat-label">Total Tasks</div>
                  <div class="stat-trend">
                    <span class="trend-indicator neutral">
                      <i class="zmdi zmdi-assignment"></i>
                      Overall
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Completed Tasks Card -->
          <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-3">
            <div class="stat-card stat-card-success">
              <div class="stat-card-body">
                <div class="stat-icon-container">
                  <div class="stat-icon bg-success">
                    <i class="zmdi zmdi-check-circle"></i>
                  </div>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ dashboard_data.total_complete_tasks || 0 }}</div>
                  <div class="stat-label">Completed Tasks</div>
                  <div class="stat-trend">
                    <span class="trend-indicator positive">
                      <i class="zmdi zmdi-check"></i>
                      Done
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Not Completed Tasks Card -->
          <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-3">
            <div class="stat-card stat-card-danger">
              <div class="stat-card-body">
                <div class="stat-icon-container">
                  <div class="stat-icon bg-danger">
                    <i class="zmdi zmdi-close-circle"></i>
                  </div>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ dashboard_data.total_not_completed_tasks || 0 }}</div>
                  <div class="stat-label">Not Completed</div>
                  <div class="stat-trend">
                    <span class="trend-indicator negative">
                      <i class="zmdi zmdi-close"></i>
                      Failed
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pending Tasks Card -->
          <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-3">
            <div class="stat-card stat-card-info">
              <div class="stat-card-body">
                <div class="stat-icon-container">
                  <div class="stat-icon bg-info">
                    <i class="zmdi zmdi-time"></i>
                  </div>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ dashboard_data.total_pending_tasks || 0 }}</div>
                  <div class="stat-label">Pending Tasks</div>
                  <div class="stat-trend">
                    <span class="trend-indicator neutral">
                      <i class="zmdi zmdi-time-countdown"></i>
                      Waiting
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- In Progress Tasks Card -->
          <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-3">
            <div class="stat-card stat-card-secondary">
              <div class="stat-card-body">
                <div class="stat-icon-container">
                  <div class="stat-icon bg-secondary">
                    <i class="zmdi zmdi-refresh"></i>
                  </div>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ dashboard_data.total_in_progress_tasks || 0 }}</div>
                  <div class="stat-label">In Progress</div>
                  <div class="stat-trend">
                    <span class="trend-indicator working">
                      <i class="zmdi zmdi-refresh"></i>
                      Active
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Present Attendance Card -->
          <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-3">
            <div class="stat-card stat-card-attendance">
              <div class="stat-card-body">
                <div class="stat-icon-container">
                  <div class="stat-icon bg-success">
                    <i class="zmdi zmdi-calendar-check"></i>
                  </div>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ dashboard_data.total_present_attendance || 0 }}</div>
                  <div class="stat-label">Present Days</div>
                  <div class="stat-trend">
                    <span class="trend-indicator positive">
                      <i class="zmdi zmdi-check"></i>
                      Good
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Absent Attendance Card -->
          <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-3">
            <div class="stat-card stat-card-dark">
              <div class="stat-card-body">
                <div class="stat-icon-container">
                  <div class="stat-icon bg-dark">
                    <i class="zmdi zmdi-calendar-close"></i>
                  </div>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ dashboard_data.total_absent_attendance || 0 }}</div>
                  <div class="stat-label">Absent Days</div>
                  <div class="stat-trend">
                    <span class="trend-indicator negative">
                      <i class="zmdi zmdi-minus-circle"></i>
                      Absent
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-3">
            <div class="stat-card stat-card-danger">
              <div class="stat-card-body">
                <div class="stat-icon-container">
                  <div class="stat-icon bg-danger">
                    <i class="fa fa-clock-o"></i>
                  </div>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ dashboard_data.total_late_days || 0 }}</div>
                  <div class="stat-label">Total Late Days</div>
                  <div class="stat-trend">
                    <span class="trend-indicator negative">
                      <i class="fa fa-clock-o"></i>
                      Late Day
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-3">
            <div class="stat-card stat-card-warning">
              <div class="stat-card-body">
                <div class="stat-icon-container">
                  <div class="stat-icon bg-warning">
                    <i class="zmdi zmdi-time"></i>
                  </div>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ dashboard_data.total_late_minutes || 0 }}</div>
                  <div class="stat-label">Total Lates (MNT)</div>
                  <div class="stat-trend">
                    <span class="trend-indicator negative">
                      <i class="zmdi zmdi-minus-circle"></i>
                      Late Minute
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-3">
            <div class="stat-card stat-card-info">
              <div class="stat-card-body">
                <div class="stat-icon-container">
                  <div class="stat-icon bg-info">
                    <i class="zmdi zmdi-time"></i>
                  </div>
                </div>
                <div class="stat-content">
                  <div class="stat-number">{{ dashboard_data.total_working_hours || 0 }}</div>
                  <div class="stat-label">Total Working Hours</div>
                  <div class="stat-trend">
                    <span class="trend-indicator negative">
                        <i class="zmdi zmdi-time"></i>
                     Working Hours
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--End Row-->

    <!--Calendar section start-->
    <div class="calendar-container mb-5">
      <!-- Header -->
      <div class="calendar-header">
        <h2 class="calendar-title">
          <i class="zmdi zmdi-calendar calendar-icon"></i>
          {{ todayFullDate }}
        </h2>
        <div class="calendar-controls">
          <span class="month-label">{{ currentMonthYear }}</span>
          <input type="date" v-model="selectedDate" @change="onDateChange" />
        </div>
      </div>

      <!-- Mobile Scrollable Calendar Wrapper -->
      <div class="calendar-wrapper">
        <!-- Weekday Headers -->
        <div class="weekday-headers">
          <div
            class="weekday-header"
            v-for="day in weekdayNames"
            :key="day"
            :class="['weekday-header', day === 'Fri' || day === 'Sat' ? 'bd-offday' : '']"
          >
            {{ day }}
          </div>
        </div>

        <!-- Calendar Grid -->
        <div class="calendar-grid">
          <div v-for="n in startDayOfMonth" :key="'empty-' + n" class="calendar-cell empty-cell"></div>
          <div v-for="(date, index) in daysInMonth" :key="index" :class="calendarCellClass(date)" class="calendar-cell-item position-relative">
            <!-- Meeting Badge -->
            <router-link
              :to="`/meeting/all?date=${new Date(date.getTime() + 24 * 60 * 60 * 1000).toISOString().substr(0, 10)}`"
              v-if="countTodayMeetings(date) > 0"
              class="badge-link"
            >
              <div class="badge meeting-badge">
                <i class="zmdi zmdi-accounts"></i>
                {{ countTodayMeetings(date) }}
              </div>
            </router-link>

            <!-- Task Badge -->
            <router-link
              :to="`/tasks/date-wise-tasks/${new Date(date.getTime() + 24 * 60 * 60 * 1000).toISOString().substr(0, 10)}`"
              v-if="countTodayTasks(date) > 0"
              class="badge-link"
            >
              <div class="badge task-badge">
                <i class="zmdi zmdi-assignment"></i>
                {{ countTodayTasks(date) }}
              </div>
            </router-link>

            <!-- Date Display -->
            <div class="date-content">
              <div class="date-number">{{ date.getDate() }}</div>
              <div class="day-name">{{ formatDay(date).substring(0, 3) }}</div>
            </div>

            <!-- Date Full Name for Mobile -->
            <div class="date-full-name">
              {{
                date.toLocaleDateString("en-US", {
                  weekday: "short",
                  day: "numeric",
                  month: "short",
                })
              }}
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--Calendar section end-->
    <!--start overlay-->
    <div class="overlay"></div>
    <!--end overlay-->
  </div>
  <!-- End container-fluid-->
</template>

<script>
import { mapState } from "pinia";
import { auth_store } from "../../../../GlobalStore/auth_store";
export default {
  data: () => ({
    dashboard_data: {},
    selectedDate: new Date().toISOString().substr(0, 10),
    task_list_dates: [],
    meeting_dates: [],
    item: {},
    currentTheme: "dark",
  }),
  created: async function () {
    this.detectTheme();
    await this.get_all_dashboard_data();
    await this.get_all_tasks();
    await this.get_all_meetings();
  },
  methods: {
    detectTheme() {
      // Auto-detect system theme preference
      if (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) {
        this.currentTheme = "dark";
      } else {
        this.currentTheme = "light";
      }

      // Listen for theme changes
      if (window.matchMedia) {
        window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", (e) => {
          this.currentTheme = e.matches ? "dark" : "light";
        });
      }
    },
    getSocialMediaIcon(socialMediaName) {
      const iconMap = {
        Facebook: "zmdi zmdi-facebook",
        Instagram: "zmdi zmdi-instagram",
        Twitter: "zmdi zmdi-twitter",
        LinkedIn: "zmdi zmdi-linkedin",
        YouTube: "zmdi zmdi-youtube",
        TikTok: "zmdi zmdi-vimeo",
        Snapchat: "zmdi zmdi-accounts",
        Pinterest: "zmdi zmdi-pinterest",
        WhatsApp: "zmdi zmdi-whatsapp",
        Telegram: "zmdi zmdi-comments",
        Discord: "zmdi zmdi-comments",
        Reddit: "zmdi zmdi-reddit",
        Tumblr: "zmdi zmdi-tumblr",
        Twitch: "zmdi zmdi-twitch",
        GitHub: "zmdi zmdi-github",
        Behance: "zmdi zmdi-dribbble",
        Dribbble: "zmdi zmdi-dribbble",
        Skype: "zmdi zmdi-skype",
        Viber: "zmdi zmdi-phone",
        WeChat: "zmdi zmdi-comments",
        Other: "zmdi zmdi-link",
      };

      return iconMap[socialMediaName] || "zmdi zmdi-link";
    },
    getSocialMediaClass(socialMediaName) {
      const classMap = {
        Facebook: "bg-facebook",
        Instagram: "bg-instagram",
        Twitter: "bg-twitter",
        LinkedIn: "bg-linkedin",
        YouTube: "bg-youtube",
        TikTok: "bg-tiktok",
        Snapchat: "bg-snapchat",
        Pinterest: "bg-pinterest",
        WhatsApp: "bg-whatsapp",
        Telegram: "bg-telegram",
        Discord: "bg-discord",
        Reddit: "bg-reddit",
        Tumblr: "bg-tumblr",
        Twitch: "bg-twitch",
        GitHub: "bg-github",
        Behance: "bg-behance",
        Dribbble: "bg-dribbble",
        Skype: "bg-skype",
        Viber: "bg-viber",
        WeChat: "bg-wechat",
        Other: "bg-secondary",
      };

      return classMap[socialMediaName] || "bg-secondary";
    },
    get_all_dashboard_data: async function () {
      let response = await axios.get("get-employee-dashboard-data");
      if (response.status == 200) {
        this.dashboard_data = response.data.data;
      }
    },
    get_all_tasks: async function () {
      let response = await axios.get("task?get_all=1");
      if (response.status == 200) {
        // Only keep the start_date values in the array
        this.task_list_dates = response.data.data.filter((task) => task.start_date).map((task) => task.start_date);
      }
    },
    get_all_meetings: async function () {
      let response = await axios.get("meeting?get_all=1");
      if (response.status == 200) {
        // Only keep the start_date values in the array
        this.meeting_dates = response.data.data.filter((meeting) => meeting.date).map((meeting) => meeting.date);
      }
    },
    formatDay(date) {
      return date.toLocaleString("default", { weekday: "long" });
    },
    isHoliday(date) {
      // 5 = Friday, 6 = Saturday
      return date.getDay() === 5 || date.getDay() === 6;
    },
    isToday(date) {
      const today = new Date();
      return date.getDate() === today.getDate() && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
    },
    calendarCellClass(date) {
      // If today and holiday, use both classes
      if (this.isToday(date) && this.isHoliday(date)) {
        return ["calendar-cell", "today-success", "today-holiday-border"];
      } else if (this.isToday(date)) {
        return ["calendar-cell", "today-success"];
      } else if (this.isHoliday(date)) {
        return ["calendar-cell", "friday"];
      } else {
        return ["calendar-cell"];
      }
    },
    onDateChange() {
      // The computed will auto-update
    },
    countTodayTasks(date) {
      const currentDate = date.getFullYear() + "-" + String(date.getMonth() + 1).padStart(2, "0") + "-" + String(date.getDate()).padStart(2, "0");

      return this.task_list_dates.filter((taskDate) => {
        const taskDay = taskDate.split(" ")[0]; // Get only YYYY-MM-DD
        return taskDay === currentDate;
      }).length;
    },
    countTodayMeetings(date) {
      const currentDate = date.getFullYear() + "-" + String(date.getMonth() + 1).padStart(2, "0") + "-" + String(date.getDate()).padStart(2, "0");

      return this.meeting_dates.filter((meetingDate) => {
        const meetingDay = meetingDate.split(" ")[0]; // Get only YYYY-MM-DD
        return meetingDay === currentDate;
      }).length;
    },
    statBoxes() {
      return [
        {
          label: "Total Project",
          value: this.item.total_project ?? 0,
          icon: "fas fa-briefcase",
          bg: "bg-primary text-white",
        },
        {
          label: "Total Task",
          value: this.item.total_task ?? 0,
          icon: "fas fa-tasks",
          bg: "bg-warning text-dark",
        },
        {
          label: "Total Completed Task",
          value: this.item.completed_task ?? 0,
          icon: "fas fa-check-circle",
          bg: "bg-light text-dark",
        },
        {
          label: "Total Not Completed Task",
          value: this.item.incomplete_task ?? 0,
          icon: "fas fa-times-circle",
          bg: "bg-light text-dark",
        },
        {
          label: "Total Attendance",
          value: this.item.total_attendance ?? 0,
          icon: "far fa-calendar-check",
          bg: "bg-success text-white",
        },
        {
          label: "Total Absent",
          value: this.item.total_absent ?? 0,
          icon: "far fa-calendar-times",
          bg: "bg-danger text-white",
        },
        // two empty placeholders
        { label: "", value: "", icon: "", bg: "bg-light" },
        { label: "", value: "", icon: "", bg: "bg-light" },
      ];
    },
  },
  computed: {
    ...mapState(auth_store, {
      auth_info: "auth_info",
    }),
    currentMonthYear() {
      const date = new Date(this.selectedDate);
      return date.toLocaleString("default", {
        month: "short",
        year: "numeric",
      });
    },
    daysInMonth() {
      const date = new Date(this.selectedDate);
      const year = date.getFullYear();
      const month = date.getMonth();
      const days = [];
      const firstDay = new Date(year, month, 1);
      const lastDay = new Date(year, month + 1, 0);

      for (let d = 1; d <= lastDay.getDate(); d++) {
        days.push(new Date(year, month, d));
      }

      return days;
    },
    weekdayNames() {
      return ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    },
    startDayOfMonth() {
      const date = new Date(this.selectedDate);
      const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
      return firstDay.getDay(); // 0 (Sun) to 6 (Sat)
    },
    todayFullDate() {
      const today = new Date();
      return today.toLocaleDateString("default", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    },
  },
};
</script>

<style scoped>
/* CSS Variables for Theme Support */
.container-fluid {
  --bg-primary: #f8f9fa;
  --bg-secondary: #ffffff;
  --text-primary: #2c3e50;
  --text-secondary: #6c757d;
  --text-muted: #6c757d;
  --border-color: #e9ecef;
  --shadow-color: rgba(0, 0, 0, 0.1);
  --shadow-hover: rgba(0, 0, 0, 0.15);
  --card-bg: #ffffff;
  --table-hover: #f8f9fa;
  --detail-row-bg: #f8f9fa;
  --detail-row-hover: #e9ecef;
}

/* Dark Theme Variables */
.container-fluid[data-theme="dark"] {
  --bg-primary: #2d2d2d;
  --bg-secondary: #3a3a3a;
  --text-primary: #ffffff;
  --text-secondary: #b0b0b0;
  --text-muted: #888888;
  --border-color: #555555;
  --shadow-color: rgba(0, 0, 0, 0.3);
  --shadow-hover: rgba(0, 0, 0, 0.4);
  --card-bg: #3a3a3a;
  --table-hover: #404040;
  --detail-row-bg: #404040;
  --detail-row-hover: #4a4a4a;
}

/* Page Layout */
.container-fluid {
  background-color: var(--bg-primary) !important;
  color: var(--text-primary) !important;
  min-height: 100vh;
  transition: background-color 0.3s ease, color 0.3s ease;
}

/* Profile Card */
.profile-card {
  background: var(--card-bg);
  color: var(--text-primary);
  border-radius: 20px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
  border: 1px solid var(--border-color);
}

.profile-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px var(--shadow-hover) !important;
}

.profile-header {
  padding: 0;
  margin: 0;
}

.profile-bg {
  height: 120px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
}

.profile-bg::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.1));
}

.profile-image-container {
  position: relative;
  margin-top: -60px;
  z-index: 10;
}

.profile-avatar {
  position: relative;
  display: inline-block;
}

.avatar-img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  border: 5px solid var(--card-bg);
  object-fit: cover;
  object-position: center;
  box-shadow: 0 10px 30px var(--shadow-color);
  transition: transform 0.3s ease;
}

.profile-avatar:hover .avatar-img {
  transform: scale(1.05);
}

.rating-badge {
  position: absolute;
  bottom: 5px;
  right: 5px;
}

.rating-circle {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  box-shadow: 0 4px 15px var(--shadow-color);
  border: 3px solid var(--card-bg);
  transition: all 0.3s ease;
}

.rating-circle:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.rating-value {
  font-size: 14px;
  font-weight: 700;
  line-height: 1;
}

.rating-scale {
  font-size: 8px;
  opacity: 0.8;
  line-height: 1;
}

.profile-name h3 {
  font-size: 1.5rem;
  color: var(--text-primary);
}

.profile-name p {
  color: var(--text-secondary);
}

/* Rating Colors */
.rating-circle[data-rating="0"],
.rating-circle[data-rating="1"],
.rating-circle[data-rating="2"] {
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
}

.rating-circle[data-rating="3"],
.rating-circle[data-rating="4"] {
  background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
}

.rating-circle[data-rating="5"],
.rating-circle[data-rating="6"] {
  background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
  color: #2c3e50;
}

.rating-circle[data-rating="7"],
.rating-circle[data-rating="8"] {
  background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
}

.rating-circle[data-rating="9"],
.rating-circle[data-rating="10"] {
  background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}

/* Profile Details */
.profile-details {
  padding: 1rem 0;
}

.detail-row {
  display: flex;
  align-items: flex-start;
  margin-bottom: 1.5rem;
  padding: 0.75rem;
  background: var(--detail-row-bg);
  border-radius: 10px;
  transition: all 0.3s ease;
}

.detail-row:hover {
  background: var(--detail-row-hover);
  transform: translateX(5px);
}

.detail-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--card-bg);
  border-radius: 10px;
  margin-right: 1rem;
  box-shadow: 0 2px 10px var(--shadow-color);
}

.detail-content {
  flex: 1;
}

.detail-label {
  display: block;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  color: var(--text-secondary);
  margin-bottom: 0.25rem;
  letter-spacing: 0.5px;
}

.detail-value {
  font-size: 0.95rem;
  color: var(--text-primary);
  font-weight: 500;
  word-break: break-word;
}

/* Social Section */
.social-section {
  padding: 1.5rem;
  background: var(--detail-row-bg);
}

.social-header {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.social-title {
  color: var(--text-primary);
  font-weight: 600;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.social-content {
  text-align: center;
}

.social-links {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.social-link {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  border: 3px solid transparent;
  margin: 0.25rem;
  padding: 0;
  color: white;
}

.social-link:hover {
  transform: translateY(-6px) scale(1.1);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
  border-color: rgba(255, 255, 255, 0.3);
  color: white;
  text-decoration: none;
}

.social-link:active {
  transform: translateY(-3px) scale(1.05);
}

.social-link i {
  font-size: 1.2rem;
  line-height: 1;
  transition: all 0.3s ease;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.social-link:hover i {
  transform: scale(1.1);
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
}

/* Social Tooltip */
.social-tooltip {
  position: absolute;
  bottom: -35px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 500;
  white-space: nowrap;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
  z-index: 10;
  pointer-events: none;
}

.social-tooltip::before {
  content: "";
  position: absolute;
  top: -4px;
  left: 50%;
  transform: translateX(-50%);
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
  border-bottom: 4px solid rgba(0, 0, 0, 0.8);
}

.social-link:hover .social-tooltip {
  opacity: 1;
  visibility: visible;
  bottom: -40px;
}

/* No Social State */
.no-social-state {
  padding: 2rem 1rem;
  text-align: center;
}

.no-social-state i {
  opacity: 0.4;
  margin-bottom: 0.5rem;
  color: var(--text-muted);
}

.no-social-state p {
  margin: 0;
  font-style: italic;
  color: var(--text-muted);
}

/* Social Media Background Colors - Modern Vibrant Theme */
.bg-facebook {
  background: radial-gradient(circle at 30% 30%, #4267b2, #365899) !important;
  box-shadow: 0 6px 20px rgba(66, 103, 178, 0.4) !important;
}
.bg-instagram {
  background: radial-gradient(circle at 30% 30%, #833ab4, #fd1d1d, #fcb045) !important;
  box-shadow: 0 6px 20px rgba(131, 58, 180, 0.4) !important;
}
.bg-twitter {
  background: radial-gradient(circle at 30% 30%, #1da1f2, #0d8bd9) !important;
  box-shadow: 0 6px 20px rgba(29, 161, 242, 0.4) !important;
}
.bg-linkedin {
  background: radial-gradient(circle at 30% 30%, #0077b5, #005885) !important;
  box-shadow: 0 6px 20px rgba(0, 119, 181, 0.4) !important;
}
.bg-youtube {
  background: radial-gradient(circle at 30% 30%, #ff0000, #cc0000) !important;
  box-shadow: 0 6px 20px rgba(255, 0, 0, 0.4) !important;
}
.bg-tiktok {
  background: radial-gradient(circle at 30% 30%, #000000, #ff0050) !important;
  box-shadow: 0 6px 20px rgba(255, 0, 80, 0.4) !important;
}
.bg-snapchat {
  background: radial-gradient(circle at 30% 30%, #fffc00, #fff700) !important;
  box-shadow: 0 6px 20px rgba(255, 252, 0, 0.4) !important;
  color: #000 !important;
}
.bg-pinterest {
  background: radial-gradient(circle at 30% 30%, #e60023, #bd081c) !important;
  box-shadow: 0 6px 20px rgba(230, 0, 35, 0.4) !important;
}
.bg-whatsapp {
  background: radial-gradient(circle at 30% 30%, #25d366, #128c7e) !important;
  box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4) !important;
}
.bg-telegram {
  background: radial-gradient(circle at 30% 30%, #0088cc, #005f8a) !important;
  box-shadow: 0 6px 20px rgba(0, 136, 204, 0.4) !important;
}
.bg-discord {
  background: radial-gradient(circle at 30% 30%, #7289da, #5b6eae) !important;
  box-shadow: 0 6px 20px rgba(114, 137, 218, 0.4) !important;
}
.bg-reddit {
  background: radial-gradient(circle at 30% 30%, #ff4500, #cc3700) !important;
  box-shadow: 0 6px 20px rgba(255, 69, 0, 0.4) !important;
}
.bg-tumblr {
  background: radial-gradient(circle at 30% 30%, #00cf35, #00a62a) !important;
  box-shadow: 0 6px 20px rgba(0, 207, 53, 0.4) !important;
}
.bg-twitch {
  background: radial-gradient(circle at 30% 30%, #9146ff, #7329cc) !important;
  box-shadow: 0 6px 20px rgba(145, 70, 255, 0.4) !important;
}
.bg-github {
  background: radial-gradient(circle at 30% 30%, #333333, #1a1a1a) !important;
  box-shadow: 0 6px 20px rgba(51, 51, 51, 0.4) !important;
}
.bg-behance {
  background: radial-gradient(circle at 30% 30%, #1769ff, #0d4fd1) !important;
  box-shadow: 0 6px 20px rgba(23, 105, 255, 0.4) !important;
}
.bg-dribbble {
  background: radial-gradient(circle at 30% 30%, #ea4c89, #c73670) !important;
  box-shadow: 0 6px 20px rgba(234, 76, 137, 0.4) !important;
}
.bg-skype {
  background: radial-gradient(circle at 30% 30%, #00aff0, #0085c7) !important;
  box-shadow: 0 6px 20px rgba(0, 175, 240, 0.4) !important;
}
.bg-viber {
  background: radial-gradient(circle at 30% 30%, #665cac, #4f4788) !important;
  box-shadow: 0 6px 20px rgba(102, 92, 172, 0.4) !important;
}
.bg-wechat {
  background: radial-gradient(circle at 30% 30%, #7bb32e, #5f8f24) !important;
  box-shadow: 0 6px 20px rgba(123, 179, 46, 0.4) !important;
}

/* Enhanced Hover Effects for Social Icons */
.social-link:hover.bg-facebook {
  box-shadow: 0 12px 35px rgba(66, 103, 178, 0.6) !important;
}
.social-link:hover.bg-instagram {
  box-shadow: 0 12px 35px rgba(131, 58, 180, 0.6) !important;
}
.social-link:hover.bg-twitter {
  box-shadow: 0 12px 35px rgba(29, 161, 242, 0.6) !important;
}
.social-link:hover.bg-linkedin {
  box-shadow: 0 12px 35px rgba(0, 119, 181, 0.6) !important;
}
.social-link:hover.bg-youtube {
  box-shadow: 0 12px 35px rgba(255, 0, 0, 0.6) !important;
}
.social-link:hover.bg-tiktok {
  box-shadow: 0 12px 35px rgba(255, 0, 80, 0.6) !important;
}
.social-link:hover.bg-twitch {
  box-shadow: 0 12px 35px rgba(145, 70, 255, 0.6) !important;
}

/* Professional Statistics Cards */
.stats-row {
  margin-left: -0.75rem;
  margin-right: -0.75rem;
}

.stats-row > [class*="col-"] {
  padding-left: 0.75rem;
  padding-right: 0.75rem;
}

.stat-card {
  background: var(--card-bg);
  border-radius: 16px;
  box-shadow: 0 4px 20px var(--shadow-color);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid var(--border-color);
  overflow: hidden;
  position: relative;
  height: 100%;
  width: 100%;
}

.stat-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px var(--shadow-hover);
}

.stat-card:hover::before {
  opacity: 1;
}

.stat-card-body {
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  height: 100%;
  min-height: 120px;
}

.stat-icon-container {
  flex-shrink: 0;
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.stat-icon::before {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
  border-radius: inherit;
}

.stat-card:hover .stat-icon {
  transform: scale(1.1);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
}

.stat-content {
  flex: 1;
  min-width: 0;
}

.stat-number {
  font-size: 1.75rem;
  font-weight: 700;
  line-height: 1;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
  transition: color 0.3s ease;
}

.stat-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
  line-height: 1.2;
}

.stat-trend {
  display: flex;
  align-items: center;
}

.trend-indicator {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.2rem 0.6rem;
  border-radius: 16px;
  font-size: 0.65rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  transition: all 0.3s ease;
}

.trend-indicator i {
  font-size: 0.7rem;
}

.trend-indicator.positive {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
  border: 1px solid rgba(34, 197, 94, 0.2);
}

.trend-indicator.negative {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.trend-indicator.neutral {
  background: rgba(156, 163, 175, 0.1);
  color: #6b7280;
  border: 1px solid rgba(156, 163, 175, 0.2);
}

.trend-indicator.working {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
  border: 1px solid rgba(59, 130, 246, 0.2);
}

/* Card Type Specific Styles */
.stat-card-primary {
  --accent-color: #3b82f6;
}

.stat-card-warning {
  --accent-color: #f59e0b;
}

.stat-card-success {
  --accent-color: #10b981;
}

.stat-card-danger {
  --accent-color: #ef4444;
}

.stat-card-info {
  --accent-color: #06b6d4;
}

.stat-card-secondary {
  --accent-color: #6b7280;
}

.stat-card-attendance {
  --accent-color: #22c55e;
}

.stat-card-dark {
  --accent-color: #374151;
}

/* Icon Background Colors */
.stat-icon.bg-primary {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.stat-icon.bg-warning {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.stat-icon.bg-success {
  background: linear-gradient(135deg, #10b981, #059669);
}

.stat-icon.bg-danger {
  background: linear-gradient(135deg, #ef4444, #dc2626);
}

.stat-icon.bg-info {
  background: linear-gradient(135deg, #06b6d4, #0891b2);
}

.stat-icon.bg-secondary {
  background: linear-gradient(135deg, #6b7280, #4b5563);
}

.stat-icon.bg-dark {
  background: linear-gradient(135deg, #374151, #1f2937);
}

/* Responsive Design for Stats Cards */
@media (max-width: 1200px) {
  .stat-card-body {
    padding: 1.25rem;
    min-height: 110px;
  }

  .stat-number {
    font-size: 1.5rem;
  }

  .stat-icon {
    width: 50px;
    height: 50px;
    font-size: 1.3rem;
  }
}

@media (max-width: 992px) {
  .stat-card-body {
    padding: 1rem;
    min-height: 100px;
    gap: 0.75rem;
  }

  .stat-number {
    font-size: 1.4rem;
  }

  .stat-label {
    font-size: 0.75rem;
  }

  .stat-icon {
    width: 46px;
    height: 46px;
    font-size: 1.2rem;
  }
}

@media (max-width: 768px) {
  .stats-row {
    margin-left: -0.5rem;
    margin-right: -0.5rem;
  }

  .stats-row > [class*="col-"] {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }

  .stat-card-body {
    padding: 1rem;
    min-height: 90px;
    gap: 0.75rem;
  }

  .stat-number {
    font-size: 1.3rem;
  }

  .stat-label {
    font-size: 0.7rem;
    margin-bottom: 0.4rem;
  }

  .stat-icon {
    width: 44px;
    height: 44px;
    font-size: 1.1rem;
  }

  .trend-indicator {
    padding: 0.15rem 0.5rem;
    font-size: 0.6rem;
  }
}

@media (max-width: 576px) {
  .stat-card-body {
    padding: 0.875rem;
    min-height: 85px;
    gap: 0.625rem;
  }

  .stat-number {
    font-size: 1.2rem;
  }

  .stat-label {
    font-size: 0.65rem;
    margin-bottom: 0.3rem;
  }

  .stat-icon {
    width: 40px;
    height: 40px;
    font-size: 1rem;
    border-radius: 12px;
  }

  .trend-indicator {
    padding: 0.1rem 0.4rem;
    font-size: 0.55rem;
    gap: 0.2rem;
  }

  .trend-indicator i {
    font-size: 0.6rem;
  }
}

@media (max-width: 480px) {
  .stat-card-body {
    padding: 0.75rem;
    min-height: 80px;
    gap: 0.5rem;
  }

  .stat-number {
    font-size: 1.1rem;
  }

  .stat-label {
    font-size: 0.6rem;
  }

  .stat-icon {
    width: 36px;
    height: 36px;
    font-size: 0.9rem;
    border-radius: 10px;
  }
}

/* Dark Theme Specific Adjustments for Stats */
.container-fluid[data-theme="dark"] .stat-card {
  background: var(--card-bg);
  border-color: var(--border-color);
}

.container-fluid[data-theme="dark"] .stat-number {
  color: var(--text-primary);
}

.container-fluid[data-theme="dark"] .stat-label {
  color: var(--text-secondary);
}

.container-fluid[data-theme="dark"] .trend-indicator.positive {
  background: rgba(34, 197, 94, 0.15);
  color: #4ade80;
}

.container-fluid[data-theme="dark"] .trend-indicator.negative {
  background: rgba(239, 68, 68, 0.15);
  color: #f87171;
}

.container-fluid[data-theme="dark"] .trend-indicator.neutral {
  background: rgba(156, 163, 175, 0.15);
  color: #9ca3af;
}

.container-fluid[data-theme="dark"] .trend-indicator.working {
  background: rgba(59, 130, 246, 0.15);
  color: #60a5fa;
}

/* General Card Styling */
.card {
  background: var(--card-bg);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
  transition: background-color 0.3s ease, color 0.3s ease;
}

.card-header {
  background: var(--card-bg) !important;
  color: var(--text-primary);
  border-bottom: 1px solid var(--border-color) !important;
}

.card-title {
  color: var(--text-primary);
}

/* Override Bootstrap Text Classes for Dark Theme */
.container-fluid[data-theme="dark"] .text-dark,
.container-fluid[data-theme="dark"] .text-muted,
.container-fluid[data-theme="dark"] .fw-medium,
.container-fluid[data-theme="dark"] .fw-semibold,
.container-fluid[data-theme="dark"] h1,
.container-fluid[data-theme="dark"] h2,
.container-fluid[data-theme="dark"] h3,
.container-fluid[data-theme="dark"] h4,
.container-fluid[data-theme="dark"] h5,
.container-fluid[data-theme="dark"] h6,
.container-fluid[data-theme="dark"] p,
.container-fluid[data-theme="dark"] span {
  color: var(--text-primary) !important;
}

.container-fluid[data-theme="dark"] .text-muted {
  color: var(--text-muted) !important;
}

/* Calendar Styles */
.calendar-container {
  background: var(--card-bg);
  color: var(--text-primary);
  padding: 1.5rem;
  border-radius: 16px;
  margin: auto;
  font-family: Arial, sans-serif;
  box-shadow: 0 4px 20px var(--shadow-color);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
  overflow: hidden;
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.calendar-header h2 {
  font-size: 1.5rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-primary);
  margin: 0;
}

.calendar-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.calendar-controls input {
  background-color: var(--card-bg);
  color: var(--text-primary);
  border: 2px solid var(--border-color);
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  min-width: 150px;
}

.calendar-controls input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.month-label {
  font-weight: 600;
  color: var(--text-secondary);
  font-size: 1rem;
  white-space: nowrap;
}

/* Calendar Wrapper for Mobile Scroll */
.calendar-wrapper {
  width: 100%;
  overflow: hidden;
}

.weekday-headers {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  min-width: 100%;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.75rem;
  width: 100%;
  min-width: 100%;
}

.calendar-cell,
.calendar-cell-item {
  background-color: var(--card-bg);
  border: 2px solid var(--border-color);
  padding: 0.75rem 0.5rem;
  border-radius: 12px;
  text-align: center;
  transition: all 0.3s ease;
  position: relative;
  min-height: 80px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  cursor: pointer;
  overflow: hidden;
}

.calendar-cell-item:hover {
  background-color: var(--detail-row-bg);
  border-color: #3b82f6;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.calendar-title {
  font-size: 1.5rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: var(--text-primary);
}

.calendar-icon {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: #ffffff;
  padding: 0.75rem;
  border-radius: 12px;
  font-size: 1.25rem;
  display: inline-block;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.friday {
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
  border-color: rgba(239, 68, 68, 0.3);
  color: var(--text-primary);
}

.today-success {
  background: linear-gradient(135deg, #22c55e, #16a34a) !important;
  border-color: #16a34a !important;
  color: #fff !important;
  box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3) !important;
}

.today-holiday-border {
  border: 3px solid #ef4444 !important;
  box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2) !important;
}

.date-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 2;
}

.date-number {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
  line-height: 1;
}

.day-name {
  font-size: 0.7rem;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 500;
  line-height: 1;
}

.date-full-name {
  display: none;
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-primary);
  text-align: center;
  padding: 0.25rem;
  background: rgba(59, 130, 246, 0.1);
  border-radius: 6px;
  margin-top: 0.5rem;
}

/* Badge Styles */
.badge-link {
  position: absolute;
  z-index: 3;
  text-decoration: none;
}

.meeting-badge {
  top: 4px;
  left: 4px;
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  color: white;
  font-size: 0.6rem;
  padding: 0.2rem 0.4rem;
  border-radius: 8px;
  font-weight: 600;
  box-shadow: 0 2px 4px rgba(139, 92, 246, 0.3);
  display: flex;
  align-items: center;
  gap: 0.2rem;
  min-width: 24px;
  justify-content: center;
}

.task-badge {
  top: 4px;
  right: 4px;
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: white;
  font-size: 0.6rem;
  padding: 0.2rem 0.4rem;
  border-radius: 8px;
  font-weight: 600;
  box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
  display: flex;
  align-items: center;
  gap: 0.2rem;
  min-width: 24px;
  justify-content: center;
}

.badge i {
  font-size: 0.5rem;
}

.weekday-header {
  font-weight: 700;
  text-align: center;
  color: #fff;
  background: linear-gradient(135deg, #22c55e, #16a34a);
  padding: 0.75rem 0.5rem;
  border-radius: 8px;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
}

.empty-cell {
  background: transparent !important;
  border: none !important;
  cursor: default;
}

.empty-cell:hover {
  transform: none !important;
  box-shadow: none !important;
}

.bd-offday {
  background: linear-gradient(135deg, rgba(127, 29, 29, 0.8), rgba(153, 27, 27, 0.8)) !important;
  color: #fff !important;
}

/* Responsive Design for Calendar */
@media (max-width: 1200px) {
  .calendar-container {
    padding: 1.25rem;
  }

  .calendar-grid,
  .weekday-headers {
    gap: 0.5rem;
  }

  .calendar-cell,
  .calendar-cell-item {
    padding: 0.6rem 0.4rem;
    min-height: 70px;
  }

  .date-number {
    font-size: 1.3rem;
  }
}

@media (max-width: 992px) {
  .calendar-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }

  .calendar-controls {
    align-self: flex-end;
    width: 100%;
    justify-content: flex-end;
  }

  .calendar-grid,
  .weekday-headers {
    grid-template-columns: repeat(5, 1fr);
    gap: 0.5rem;
  }

  .calendar-cell,
  .calendar-cell-item {
    min-height: 65px;
    padding: 0.5rem 0.3rem;
  }

  .date-number {
    font-size: 1.2rem;
  }

  .day-name {
    font-size: 0.65rem;
  }
}

@media (max-width: 768px) {
  .calendar-container {
    padding: 1rem;
    margin: 0 -0.5rem;
  }

  .calendar-header {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }

  .calendar-header h2 {
    font-size: 1.25rem;
    text-align: center;
  }

  .calendar-controls {
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }

  .calendar-controls input {
    min-width: 120px;
    padding: 0.4rem 0.6rem;
    font-size: 0.85rem;
  }

  .month-label {
    font-size: 0.9rem;
  }

  /* Make calendar horizontally scrollable on mobile */
  .calendar-wrapper {
    overflow-x: auto;
    padding-bottom: 0.5rem;
  }

  .weekday-headers,
  .calendar-grid {
    grid-template-columns: repeat(7, minmax(80px, 1fr));
    min-width: 560px; /* 7 * 80px */
    gap: 0.4rem;
  }

  .calendar-cell,
  .calendar-cell-item {
    min-height: 75px;
    padding: 0.5rem 0.3rem;
    border-radius: 8px;
    min-width: 75px;
  }

  .date-number {
    font-size: 1.1rem;
    margin-bottom: 0.15rem;
  }

  .day-name {
    font-size: 0.6rem;
  }

  .weekday-header {
    padding: 0.6rem 0.3rem;
    font-size: 0.75rem;
    min-width: 75px;
  }

  .date-full-name {
    display: block;
    font-size: 0.65rem;
  }

  .date-content {
    display: none;
  }
}

@media (max-width: 576px) {
  .calendar-container {
    padding: 0.75rem;
    margin: 0 -0.75rem;
    border-radius: 12px;
  }

  .calendar-header {
    gap: 0.75rem;
  }

  .calendar-header h2 {
    font-size: 1.1rem;
  }

  .calendar-controls {
    flex-direction: column;
    align-items: stretch;
    gap: 0.5rem;
  }

  .calendar-controls input {
    width: 100%;
    min-width: unset;
  }

  .month-label {
    text-align: center;
    font-size: 0.85rem;
  }

  .weekday-headers,
  .calendar-grid {
    grid-template-columns: repeat(7, minmax(75px, 1fr));
    min-width: 525px; /* 7 * 75px */
    gap: 0.3rem;
  }

  .calendar-cell,
  .calendar-cell-item {
    min-height: 70px;
    padding: 0.4rem 0.2rem;
    border-radius: 6px;
    min-width: 70px;
  }

  .date-full-name {
    font-size: 0.6rem;
    padding: 0.2rem;
  }

  .weekday-header {
    padding: 0.5rem 0.2rem;
    font-size: 0.7rem;
    min-width: 70px;
  }

  .meeting-badge,
  .task-badge {
    font-size: 0.55rem;
    padding: 0.15rem 0.3rem;
    min-width: 20px;
  }

  .badge i {
    font-size: 0.45rem;
  }
}

@media (max-width: 480px) {
  .calendar-container {
    padding: 0.5rem;
    margin: 0 -1rem;
  }

  .calendar-header h2 {
    font-size: 1rem;
  }

  .weekday-headers,
  .calendar-grid {
    grid-template-columns: repeat(7, minmax(65px, 1fr));
    min-width: 455px; /* 7 * 65px */
    gap: 0.25rem;
  }

  .calendar-cell,
  .calendar-cell-item {
    min-height: 65px;
    padding: 0.3rem 0.15rem;
    min-width: 65px;
  }

  .date-full-name {
    font-size: 0.55rem;
    padding: 0.15rem;
  }

  .weekday-header {
    padding: 0.4rem 0.15rem;
    font-size: 0.65rem;
    min-width: 65px;
  }

  .meeting-badge,
  .task-badge {
    font-size: 0.5rem;
    padding: 0.1rem 0.25rem;
    min-width: 18px;
  }
}

@media (max-width: 360px) {
  .calendar-container {
    padding: 0.4rem;
  }

  .calendar-header h2 {
    font-size: 0.95rem;
  }

  .weekday-headers,
  .calendar-grid {
    grid-template-columns: repeat(7, minmax(55px, 1fr));
    min-width: 385px; /* 7 * 55px */
    gap: 0.2rem;
  }

  .calendar-cell,
  .calendar-cell-item {
    min-height: 60px;
    padding: 0.25rem 0.1rem;
    min-width: 55px;
  }

  .date-full-name {
    font-size: 0.5rem;
    padding: 0.1rem;
    line-height: 1.2;
  }

  .weekday-header {
    padding: 0.3rem 0.1rem;
    font-size: 0.6rem;
    min-width: 55px;
  }

  .meeting-badge,
  .task-badge {
    font-size: 0.45rem;
    padding: 0.08rem 0.2rem;
    min-width: 16px;
  }

  .badge i {
    font-size: 0.4rem;
  }
}

/* Scrollbar Styling for Mobile */
@media (max-width: 768px) {
  .calendar-wrapper::-webkit-scrollbar {
    height: 4px;
  }

  .calendar-wrapper::-webkit-scrollbar-track {
    background: var(--border-color);
    border-radius: 2px;
  }

  .calendar-wrapper::-webkit-scrollbar-thumb {
    background: #3b82f6;
    border-radius: 2px;
  }

  .calendar-wrapper::-webkit-scrollbar-thumb:hover {
    background: #1d4ed8;
  }
}

/* Dark Theme Adjustments for Calendar */
.container-fluid[data-theme="dark"] .calendar-container {
  background: var(--card-bg);
  color: var(--text-primary);
  border-color: var(--border-color);
}

.container-fluid[data-theme="dark"] .calendar-controls input {
  background-color: var(--card-bg);
  color: var(--text-primary);
  border-color: var(--border-color);
}

.container-fluid[data-theme="dark"] .calendar-cell,
.container-fluid[data-theme="dark"] .calendar-cell-item {
  background-color: var(--card-bg);
  border-color: var(--border-color);
}

.container-fluid[data-theme="dark"] .calendar-cell-item:hover {
  background-color: var(--detail-row-hover);
}

.container-fluid[data-theme="dark"] .date-number {
  color: var(--text-primary);
}

.container-fluid[data-theme="dark"] .day-name {
  color: var(--text-secondary);
}

.container-fluid[data-theme="dark"] .date-full-name {
  color: var(--text-primary);
  background: rgba(59, 130, 246, 0.15);
}

/* Responsive Design for Calendar */
@media (max-width: 1200px) {
  .calendar-container {
    padding: 1.25rem;
  }

  .calendar-grid {
    gap: 0.5rem;
  }

  .calendar-cell {
    padding: 0.75rem 0.4rem;
    min-height: 70px;
  }

  .date-number {
    font-size: 1.1rem;
  }
}

@media (max-width: 992px) {
  .calendar-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }

  .calendar-controls {
    align-self: flex-end;
    width: 100%;
    justify-content: flex-end;
  }

  .calendar-grid {
    grid-template-columns: repeat(5, 1fr);
    gap: 0.5rem;
  }

  .calendar-cell {
    min-height: 65px;
    padding: 0.6rem 0.3rem;
  }

  .date-number {
    font-size: 1rem;
  }

  .day-name {
    font-size: 0.7rem;
  }
}

@media (max-width: 768px) {
  .calendar-container {
    padding: 1rem;
    margin: 0 -0.5rem;
  }

  .calendar-header {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }

  .calendar-header h2 {
    font-size: 1.25rem;
    text-align: center;
  }

  .calendar-controls {
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }

  .calendar-controls input {
    min-width: 120px;
    padding: 0.4rem 0.6rem;
    font-size: 0.85rem;
  }

  .month-label {
    font-size: 0.9rem;
  }

  .calendar-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 0.4rem;
  }

  .calendar-cell {
    min-height: 60px;
    padding: 0.5rem 0.25rem;
    border-radius: 8px;
  }

  .date-number {
    font-size: 0.95rem;
    margin-bottom: 0.15rem;
  }

  .day-name {
    font-size: 0.65rem;
  }

  .weekday-header {
    padding: 0.6rem 0.4rem;
    font-size: 0.8rem;
  }

  .badge {
    font-size: 0.6rem;
    padding: 0.15rem 0.4rem;
  }
}

@media (max-width: 576px) {
  .calendar-container {
    padding: 0.75rem;
    margin: 0 -0.75rem;
    border-radius: 12px;
  }

  .calendar-header {
    gap: 0.75rem;
  }

  .calendar-header h2 {
    font-size: 1.1rem;
  }

  .calendar-controls {
    flex-direction: column;
    align-items: stretch;
    gap: 0.5rem;
  }

  .calendar-controls input {
    width: 100%;
    min-width: unset;
  }

  .month-label {
    text-align: center;
    font-size: 0.85rem;
  }

  .calendar-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 0.3rem;
  }

  .calendar-cell {
    min-height: 55px;
    padding: 0.4rem 0.2rem;
    border-radius: 6px;
  }

  .date-number {
    font-size: 0.9rem;
    margin-bottom: 0.1rem;
  }

  .day-name {
    font-size: 0.6rem;
  }

  .weekday-header {
    padding: 0.5rem 0.3rem;
    font-size: 0.75rem;
  }
}

@media (max-width: 480px) {
  .calendar-container {
    padding: 0.5rem;
    margin: 0 -1rem;
  }

  .calendar-header h2 {
    font-size: 1rem;
  }

  .calendar-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.25rem;
  }

  .calendar-cell {
    min-height: 50px;
    padding: 0.3rem 0.15rem;
  }

  .date-number {
    font-size: 0.85rem;
  }

  .day-name {
    font-size: 0.55rem;
  }

  .weekday-header {
    padding: 0.4rem 0.2rem;
    font-size: 0.7rem;
  }

  .badge {
    font-size: 0.55rem;
    padding: 0.1rem 0.3rem;
  }
}

@media (max-width: 360px) {
  .calendar-container {
    padding: 0.4rem;
  }

  .calendar-header h2 {
    font-size: 0.95rem;
  }

  .calendar-grid {
    grid-template-columns: 1fr;
    gap: 0.2rem;
  }

  .calendar-cell {
    min-height: 45px;
    padding: 0.25rem;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    text-align: left;
  }

  .date-number {
    font-size: 0.8rem;
    margin-bottom: 0;
    margin-right: 0.5rem;
  }

  .day-name {
    font-size: 0.5rem;
    flex: 1;
  }

  .weekday-header {
    display: none;
  }
}

/* Dark Theme Adjustments for Calendar */
.container-fluid[data-theme="dark"] .calendar-container {
  background: var(--card-bg);
  color: var(--text-primary);
  border-color: var(--border-color);
}

.container-fluid[data-theme="dark"] .calendar-controls input {
  background-color: var(--card-bg);
  color: var(--text-primary);
  border-color: var(--border-color);
}

.container-fluid[data-theme="dark"] .calendar-cell {
  background-color: var(--card-bg);
  border-color: var(--border-color);
}

.container-fluid[data-theme="dark"] .calendar-cell:hover {
  background-color: var(--detail-row-hover);
}

.container-fluid[data-theme="dark"] .date-number {
  color: var(--text-primary);
}

.container-fluid[data-theme="dark"] .day-name {
  color: var(--text-secondary);
}

/* Responsive Design for Profile */
@media (max-width: 768px) {
  .profile-card {
    margin-bottom: 20px;
  }

  .profile-bg {
    height: 100px;
  }

  .avatar-img {
    width: 100px;
    height: 100px;
  }

  .profile-image-container {
    margin-top: -50px;
  }

  .profile-name h3 {
    font-size: 1.3rem;
  }

  .detail-row {
    padding: 10px;
  }

  .social-links {
    justify-content: center;
  }
}
</style>
