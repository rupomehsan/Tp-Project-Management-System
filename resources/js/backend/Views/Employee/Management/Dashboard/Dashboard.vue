<template>
  <div class="container-fluid">
    <!--Start Dashboard Content-->
    <div class="row">
      <div class="col-lg-4 col-md-4">
        <div class="card profile-card-2">
          <div class="card-img-block">
            <img class="img-fluid bg-dark" src="avatar.png" alt="Card image cap" />
          </div>
          <div class="card-body pt-5">
            <img :src="`${item.image ?? 'avatar.png'}`" alt="profile-image" class="profile" style="width: 150px; height: 150px; object-fit: cover" />
            <h5 class="card-title text-capitalize">Name : {{ item.name }}</h5>
            <p class="card-text">Eamil : {{ item.email }}</p>
            <p class="card-text">Phone : {{ item.phone_number }}</p>
            <p class="card-text">Address : {{ item.address ?? "N/A" }}</p>
          </div>
          <div class="card-body border-light">
            <div class="media align-items-center">
              <div class="icon-block">
                <a href="javascript:void();"><i class="fa fa-facebook bg-facebook text-white"></i></a>
                <a href="javascript:void();"> <i class="fa fa-twitter bg-twitter text-white"></i></a>
                <a href="javascript:void();"> <i class="fa fa-google-plus bg-google-plus text-white"></i></a>
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
   
    <!--End Row-->

    <!--Caleder section start-->
    <div class="calendar-container mb-5">
      <!-- Header -->
      <div class="calendar-header">
        <h2><span>📅</span> Calendar</h2>
        <div class="calendar-controls">
          <span class="month-label">{{ currentMonthYear }}</span>
          <input type="date" v-model="selectedDate" @change="onDateChange" />
        </div>
      </div>

      <!-- Calendar Grid -->
      <div class="calendar-grid">
        <div v-for="(date, index) in daysInMonth" :key="index" :class="calendarCellClass(date)" class="position-relative">
          <router-link :to="`/meeting/all?date=${new Date(date.getTime() + 24 * 60 * 60 * 1000).toISOString().substr(0, 10)}`">
            <div class="badge-container" v-if="countTodayMeetings(date) > 0">
              <span class="badge py-1" style="position: absolute; top: 5px; left: 5px">M ({{ countTodayMeetings(date) }}) </span>
            </div>
          </router-link>
          <router-link :to="`/tasks/date-wise-tasks/${new Date(date.getTime() + 24 * 60 * 60 * 1000).toISOString().substr(0, 10)}`">
            <div class="badge-container" v-if="countTodayTasks(date) > 0">
              <span class="badge py-1" style="position: absolute; top: 5px; right: 5px">T ({{ countTodayTasks(date) }}) </span>
            </div>
          </router-link>
          <div class="date-number">{{ date.getDate() }}</div>
          <div class="day-name">{{ formatDay(date) }}</div>
        </div>
      </div>
    </div>
    <!--Caleder section end-->
    <!--start overlay-->
    <div class="overlay"></div>
    <!--end overlay-->
  </div>
  <!-- End container-fluid-->
</template>

<script>
export default {
  data: () => ({
    data: {},
    selectedDate: new Date().toISOString().substr(0, 10),
    task_list_dates: [],
    meeting_dates: [],
    item : {},
  }),
  created: async function () {
    await this.get_all_dashboard_data();
    await this.get_all_tasks();
    await this.get_all_meetings();
  },
  methods: {
    get_all_dashboard_data: async function () {
      let response = await axios.get("get-all-dashboard-data");
      if (response.status == 200) {
        this.data = response.data.data;
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
    currentMonthYear() {
      const date = new Date(this.selectedDate);
      return date.toLocaleString("default", { month: "short", year: "numeric" });
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
  },
};
</script>

<style scoped>
.calendar-container {
  color: #ffffff;
  padding: 20px;
  border-radius: 10px;
  margin: auto;
  font-family: Arial, sans-serif;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.calendar-header h2 {
  font-size: 20px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.calendar-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.calendar-controls input {
  background-color: #1f2e47;
  color: #ffffff;
  border: 1px solid #33415c;
  padding: 5px 10px;
  border-radius: 5px;
}

.month-label {
  font-weight: bold;
  color: #ccc;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 10px;
}

@media (max-width: 900px) {
  .calendar-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 600px) {
  .calendar-container {
    padding: 8px;
    font-size: 13px;
  }
  .calendar-header h2 {
    font-size: 15px;
  }
  .calendar-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
  }
  .calendar-cell {
    padding: 18px 4px;
    font-size: 12px;
  }
  .date-number {
    font-size: 14px;
  }
  .day-name {
    font-size: 10px;
  }
}

.calendar-cell {
  background-color: #1f2e47;
  border: 1px solid #2a3a59;
  padding: 40px 10px;
  border-radius: 8px;
  text-align: center;
  transition: background-color 0.3s;
}

.calendar-cell:hover {
  background-color: #2a3a59;
}

.friday {
  background-color: #3b2a4a;
  border: 1px solid #b4004e;
}

.today-success {
  background-color: #198754 !important;
  border: 1px solid #157347 !important;
  color: #fff !important;
}

/* Red border if today is a holiday */
.today-holiday-border {
  border: 2px solid #ff2d2d !important;
}

.date-number {
  font-size: 18px;
  font-weight: bold;
}

.day-name {
  font-size: 12px;
  color: #ccc;
}
.badge {
  background-color: #dc3545;
  color: white;
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 12px;
  font-weight: bold;
}
</style>
