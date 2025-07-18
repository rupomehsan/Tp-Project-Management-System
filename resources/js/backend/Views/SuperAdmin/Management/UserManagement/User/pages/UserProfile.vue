<template>
  <div class="container-fluid">
    <!-- System Info Section -->

    <div class="row">
      <div class="col-md-3">
        <div class="card profile-card-2">
          <div>
            <img
              style="padding: 5px; max-height: 325px; object-fit: cover; object-position: top center;border-radius:   8px;"
              class="img-fluid w-100"
              :src="`${item.image ?? 'avatar.png'}`"
              alt="Card image cap"
            />
          </div>
          <!-- Professional Circular Rating Display -->
          <div class="circular-rating-overlay">
            <div class="rating-wrapper">
              <div class="rating-circle" :data-rating="item.rating || 0">
                <div class="rating-inner">
                  <div class="rating-number">{{ item.rating || 0 }}</div>
                  <div class="rating-separator">/</div>
                  <div class="rating-max">10</div>
                </div>
               
              </div>
              <div class="rating-glow"></div>
            </div>
          </div>
          <div class="card-body" style="padding-top: 80px">
            <h5 class="card-title text-capitalize">Name : {{ item.name }}</h5>
            <p class="card-text">Eamil : {{ item.email }}</p>
            <p class="card-text">Phone : {{ item.phone_number }}</p>
            <p class="card-text">Address : {{ item.address ?? "N/A" }}</p>
          </div>
          <div class="card-body border-light">
            <div class="media align-items-center">
              <div class="icon-block">
                <template v-if="item.social_media && item.social_media.length > 0">
                  <a 
                    v-for="(social, index) in item.social_media" 
                    :key="index"
                    :href="social.link" 
                    target="_blank" 
                    :title="social.name"
                    class="social-link"
                  >
                    <i :class="getSocialMediaIcon(social.name)"></i>
                  </a>
                </template>
                <template v-else>
                  <span class="text-muted">No social media links available</span>
                </template>
              </div>
            </div>
          </div>
        </div>
        <!-- Stats Cards Section -->
        <!-- TOTAL PROJECT -->
      </div>
      <div class="col-lg-8 col-md-8">
        <div class="row mb-4" style="gap: 20px 0">
          <!-- Add gap at the bottom using mb-4 and vertical gap with style -->
          <div class="col-lg-3 col-md-4">
            <div class="card bg-primary text-white h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="font-weight-light">TOTAL PROJECT</h6>
                  <i class="fas fa-project-diagram fa-2x"></i>
                </div>
                <h2 class="mb-0">{{ item.project?.length }}</h2>
              </div>
            </div>
          </div>

          <!-- TOTAL TASK -->
          <div class="col-lg-3 col-md-4">
            <div class="card bg-warning text-white h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="font-weight-light">TOTAL Task</h6>
                  <i class="fas fa-project-diagram fa-2x"></i>
                </div>
                <h2 class="mb-0">{{ item.tasks?.length }}</h2>
              </div>
            </div>
          </div>

          <!-- TOTAL COMPLETED TASK -->
          <div class="col-lg-3 col-md-4">
            <div class="card bg-secondary text-white h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="font-weight-light">TOTAL COMPLETED TASK</h6>
                  <i class="fas fa-project-diagram fa-2x"></i>
                </div>
                <h2 class="mb-0">
                  {{ item.tasks?.filter((task) => task.task_status === "Completed").length }}
                </h2>
              </div>
            </div>
          </div>
          <!-- TOTAL NOT COMPLETED TASK -->
          <div class="col-lg-3 col-md-4">
            <div class="card bg-danger text-white h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="font-weight-light">TOTAL NOT COMPLETED TASK</h6>
                  <!-- <i class="fas fa-users fa-2x"></i> -->
                </div>
                <h2 class="mb-0">
                  {{ item.tasks?.filter((task) => task.task_status !== "Completed").length }}
                </h2>
              </div>
            </div>
          </div>

          <!-- TOTAL Attendance -->
          <div class="col-lg-3 col-md-4">
            <div class="card bg-success text-white h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="font-weight-light">TOTAL PRESENT ATTENDANCE</h6>
                </div>
                <h2 class="mb-0">{{ item.attendance?.filter((att) => att.attendance_status === "Present").length }}</h2>
              </div>
            </div>
          </div>
          <!-- TOTAL Absence -->
          <div class="col-lg-3 col-md-4">
            <div class="card bg-dark text-white h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="font-weight-light">TOTAL ABSENCE</h6>
                </div>
                <h2 class="mb-0">
                  {{ item.attendance?.filter((att) => att.attendance_status === "Absent").length }}
                </h2>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- End System Info Section -->
    <div class="card-body">
      <div class="table-responsive table_responsive card_body_fixed_height">
        <table class="table table-bordered mt-3">
          <thead>
            <tr>
              <th>List</th>
              <th>Project Title</th>
              <th>Project Status</th>
              <th>Start Date</th>
              <th>End Date</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-if="Array.isArray(item.project) && item.project?.length"
              v-for="(projects, index) in item.project"
              :key="index"
              :style="projects.project_status === 'Completed' ? 'background-color: green;' : ''"
            >
              <td>{{ index + 1 }}</td>
              <td>{{ projects.name }}</td>
              <td>{{ projects.project_status }}</td>
              <td>{{ projects.start_date }}</td>
              <td>{{ projects.end_date }}</td>
            </tr>
            <tr v-else>
              <td colspan="5" class="text-center">No project available.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- End System Info Section -->
  </div>
  <!-- End container-fluid-->
</template>
<script>
import { mapActions, mapState, mapWritableState } from "pinia";
import { store } from "../store";
import setup from "../setup";

export default {
  data: () => ({
    setup,
  }),
  created: async function () {
    let id = (this.param_id = this.$route.params.id);
    await this.get_data(id);
  },
  methods: {
    ...mapActions(store, {
      details: "details",
    }),
    get_data: async function (slug) {
      this.item = {};
      await this.details(slug);
    },
    getSocialMediaIcon(socialMediaName) {
      const iconMap = {
        'Facebook': 'fa fa-facebook bg-facebook text-white',
        'Instagram': 'fa fa-instagram bg-instagram text-white',
        'Twitter': 'fa fa-twitter bg-twitter text-white',
        'LinkedIn': 'fa fa-linkedin bg-linkedin text-white',
        'YouTube': 'fa fa-youtube bg-youtube text-white',
        'TikTok': 'fa fa-tiktok bg-tiktok text-white',
        'Snapchat': 'fa fa-snapchat bg-snapchat text-white',
        'Pinterest': 'fa fa-pinterest bg-pinterest text-white',
        'WhatsApp': 'fa fa-whatsapp bg-whatsapp text-white',
        'Telegram': 'fa fa-telegram bg-telegram text-white',
        'Discord': 'fa fa-discord bg-discord text-white',
        'Reddit': 'fa fa-reddit bg-reddit text-white',
        'Tumblr': 'fa fa-tumblr bg-tumblr text-white',
        'Twitch': 'fa fa-twitch bg-twitch text-white',
        'GitHub': 'fa fa-github bg-github text-white',
        'Behance': 'fa fa-behance bg-behance text-white',
        'Dribbble': 'fa fa-dribbble bg-dribbble text-white',
        'Skype': 'fa fa-skype bg-skype text-white',
        'Viber': 'fa fa-viber bg-viber text-white',
        'WeChat': 'fa fa-wechat bg-wechat text-white',
        'Other': 'fa fa-link bg-secondary text-white'
      };
      
      return iconMap[socialMediaName] || 'fa fa-link bg-secondary text-white';
    },
  },
  computed: {
    ...mapWritableState(store, {
      item: "item",
    }),

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
};
</script>

<style>
tr th {
  text-align: left !important;
}
.gap-20 {
  gap: 20px;
}

/* Professional Circular Rating Styles */
.circular-rating {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.circular-rating-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 10;
  margin-top: -3px;
}

.profile-card-2 {
  position: relative;
}

.rating-wrapper {
  position: relative;
  display: inline-block;
}

.rating-circle {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  box-shadow: 
    0 10px 30px rgba(0, 0, 0, 0.2),
    0 0 0 8px rgba(255, 255, 255, 0.9),
    0 0 0 12px rgba(0, 0, 0, 0.1);
  position: relative;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(10px);
}

.rating-circle:hover {
  transform: scale(1.08);
  box-shadow: 
    0 15px 40px rgba(0, 0, 0, 0.25),
    0 0 0 8px rgba(255, 255, 255, 0.95),
    0 0 0 12px rgba(0, 0, 0, 0.15);
}

.rating-inner {
  display: flex;
  align-items: baseline;
  justify-content: center;
  margin-bottom: 2px;
}

.rating-number {
  font-size: 28px;
  line-height: 1;
  font-weight: 700;
}

.rating-separator {
  font-size: 16px;
  margin: 0 2px;
  opacity: 0.8;
}

.rating-max {
  font-size: 14px;
  font-weight: 500;
  opacity: 0.9;
}

.rating-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.9;
  font-weight: 500;
}

.rating-glow {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 140px;
  height: 140px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(102, 126, 234, 0.3) 0%, transparent 70%);
  animation: pulse 3s ease-in-out infinite;
  z-index: -1;
}

@keyframes pulse {
  0%, 100% { 
    opacity: 0.6;
    transform: translate(-50%, -50%) scale(1);
  }
  50% { 
    opacity: 0.8;
    transform: translate(-50%, -50%) scale(1.1);
  }
}

/* Dynamic color based on rating */
.rating-circle[data-rating="0"],
.rating-circle[data-rating="1"],
.rating-circle[data-rating="2"] {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
}

.rating-circle[data-rating="3"],
.rating-circle[data-rating="4"] {
  background: linear-gradient(135deg, #ffa726 0%, #ff9800 100%);
}

.rating-circle[data-rating="5"],
.rating-circle[data-rating="6"] {
  background: linear-gradient(135deg, #ffeb3b 0%, #ffc107 100%);
  color: #333;
}

.rating-circle[data-rating="7"],
.rating-circle[data-rating="8"] {
  background: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%);
}

.rating-circle[data-rating="9"],
.rating-circle[data-rating="10"] {
  background: linear-gradient(135deg, #42a5f5 0%, #2196f3 100%);
}

/* Social Media Icon Styles */
.social-link {
  display: inline-block;
  margin-right: 8px;
  text-decoration: none;
  transition: transform 0.3s ease;
}

.social-link:hover {
  transform: scale(1.1);
}

.social-link i {
  width: 35px;
  height: 35px;
  line-height: 35px;
  text-align: center;
  border-radius: 50%;
  font-size: 16px;
}

/* Social Media Background Colors */
.bg-facebook { background-color: #3b5998 !important; }
.bg-instagram { background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%) !important; }
.bg-twitter { background-color: #1da1f2 !important; }
.bg-linkedin { background-color: #0077b5 !important; }
.bg-youtube { background-color: #ff0000 !important; }
.bg-tiktok { background-color: #000000 !important; }
.bg-snapchat { background-color: #fffc00 !important; color: #000 !important; }
.bg-pinterest { background-color: #bd081c !important; }
.bg-whatsapp { background-color: #25d366 !important; }
.bg-telegram { background-color: #0088cc !important; }
.bg-discord { background-color: #7289da !important; }
.bg-reddit { background-color: #ff4500 !important; }
.bg-tumblr { background-color: #00cf35 !important; }
.bg-twitch { background-color: #9146ff !important; }
.bg-github { background-color: #333333 !important; }
.bg-behance { background-color: #1769ff !important; }
.bg-dribbble { background-color: #ea4c89 !important; }
.bg-skype { background-color: #00aff0 !important; }
.bg-viber { background-color: #665cac !important; }
.bg-wechat { background-color: #7bb32e !important; }
</style>
