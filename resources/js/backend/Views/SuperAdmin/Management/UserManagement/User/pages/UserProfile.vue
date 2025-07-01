<template>
  <div class="container-fluid">
    <!-- System Info Section -->

    <div class="row">
      <div class="col-lg-4 col-md-4">
        <div class="card profile-card-2">
          <div class="card-img-block">
            <img
              class="img-fluid bg-dark"
              src="avatar.png"
              alt="Card image cap"
            />
          </div>
          <div class="card-body pt-5">
            <img
              :src="`${item.image ?? 'avatar.png'}`"
              alt="profile-image"
              class="profile"
              style="width: 150px; height: 150px; object-fit: cover"
            />
            <h5 class="card-title text-capitalize">Name : {{ item.name }}</h5>
            <p class="card-text">Eamil : {{ item.email }}</p>
            <p class="card-text">Phone : {{ item.phone ?? "N/A" }}</p>
            <p class="card-text">Address : {{ item.address ?? "N/A" }}</p>
          </div>
          <div class="card-body border-light">
            <div class="media align-items-center">
              <div class="icon-block">
                <a href="javascript:void();"
                  ><i class="fa fa-facebook bg-facebook text-white"></i
                ></a>
                <a href="javascript:void();">
                  <i class="fa fa-twitter bg-twitter text-white"></i
                ></a>
                <a href="javascript:void();">
                  <i class="fa fa-google-plus bg-google-plus text-white"></i
                ></a>
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
              <div
                class="card-footer d-flex align-items-center justify-content-between"
              >
                <a class="small text-white stretched-link" href="#"
                  >View Details</a
                >
                <div class="small text-white">
                  <i class="fas fa-angle-right"></i>
                </div>
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
              <div
                class="card-footer d-flex align-items-center justify-content-between"
              >
                <a class="small text-white stretched-link" href="#"
                  >View Details</a
                >
                <div class="small text-white">
                  <i class="fas fa-angle-right"></i>
                </div>
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
                  {{
                    item.tasks?.filter(
                      (task) => task.task_status === "Completed"
                    ).length
                  }}
                </h2>
              </div>
              <div
                class="card-footer d-flex align-items-center justify-content-between"
              >
                <a class="small text-white stretched-link" href="#"
                  >View Details</a
                >
                <div class="small text-white">
                  <i class="fas fa-angle-right"></i>
                </div>
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
                  {{
                    item.tasks?.filter(
                      (task) => task.task_status !== "Completed"
                    ).length
                  }}
                </h2>
              </div>
              <div
                class="card-footer d-flex align-items-center justify-content-between"
              >
                <a class="small text-white stretched-link" href="#"
                  >View Details</a
                >
                <div class="small text-white">
                  <i class="fas fa-angle-right"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- TOTAL Attendance -->
          <div class="col-lg-3 col-md-4">
            <div class="card bg-success text-white h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="font-weight-light">TOTAL ATTENDANCE</h6>
                  <i class="fas fa-server fa-2x"></i>
                </div>
                <h2 class="mb-0">48</h2>
              </div>
              <div
                class="card-footer d-flex align-items-center justify-content-between"
              >
                <a class="small text-white stretched-link" href="#"
                  >View Details</a
                >
                <div class="small text-white">
                  <i class="fas fa-angle-right"></i>
                </div>
              </div>
            </div>
          </div>
          <!-- TOTAL Absence -->
          <div class="col-lg-3 col-md-4">
            <div class="card bg-dark text-white h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="font-weight-light">TOTAL ABSENCE</h6>
                  <i class="fas fa-server fa-2x"></i>
                </div>
                <h2 class="mb-0">05</h2>
              </div>
              <div
                class="card-footer d-flex align-items-center justify-content-between"
              >
                <a class="small text-white stretched-link" href="#"
                  >View Details</a
                >
                <div class="small text-white">
                  <i class="fas fa-angle-right"></i>
                </div>
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
                    <th>#</th>
                    <th>Task Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-if="Array.isArray(item.project) && item.project?.length"
                    v-for="(projects, index) in item.project"
                    :key="index"
                  >
                    <td>{{ index + 1 }}</td>
                    <td>{{ projects.name }}</td>
                    <td>{{ projects.start_date }}</td>
                    <td>{{ projects.end_date }}</td>
                  </tr>
                  <tr v-else>
                    <td colspan="4" class="text-center">No project available.</td>
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
</style>
