<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive table_responsive card_body_fixed_height">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Task Status Filter -->
                <div class="d-flex align-items-center flex-wrap" style="gap: 0.5rem">
                  <span class="font-weight-bold mr-2">Status</span>
                  <button class="btn btn-outline-secondary btn-sm" :class="{ active: !taskStatusFilterValue }" @click="cleartaskStatusFilter">
                    All
                  </button>
                  <button
                    class="btn btn-outline-primary btn-sm"
                    :class="{ active: taskStatusFilterValue === 'Pending' }"
                    @click="taskStatusFilter('Pending')"
                  >
                    Pending
                  </button>
                  <button
                    class="btn btn-outline-info btn-sm"
                    :class="{ active: taskStatusFilterValue === 'In Progress' }"
                    @click="taskStatusFilter('In Progress')"
                  >
                    In Progress
                  </button>
                  <button
                    class="btn btn-outline-success btn-sm"
                    :class="{ active: taskStatusFilterValue === 'Completed' }"
                    @click="taskStatusFilter('Completed')"
                  >
                    Completed
                  </button>
                  <button
                    class="btn btn-outline-danger btn-sm"
                    :class="{ active: taskStatusFilterValue === 'Not Completed' }"
                    @click="taskStatusFilter('Not Completed')"
                  >
                    Not Completed
                  </button>
                </div>
                <div class="w-25">
                  <input v-model="date" class="form-control" type="date" @change="onDateChange" />
                </div>
                <!-- Priority Filter -->
                <div class="d-flex align-items-center flex-wrap" style="gap: 0.5rem">
                  <span class="font-weight-bold mr-2">Priority</span>
                  <button
                    class="btn btn-outline-secondary btn-sm"
                    :class="{ active: !priorityFilterValue }"
                    @click="clearPriorityFilter"
                    type="button"
                  >
                    All
                  </button>
                  <button
                    class="btn btn-outline-danger btn-sm"
                    :class="{ active: priorityFilterValue === 'urgent' }"
                    @click="priorityFilter('urgent')"
                    type="button"
                  >
                    Urgent
                  </button>
                  <button
                    class="btn btn-outline-warning btn-sm"
                    :class="{ active: priorityFilterValue === 'high' }"
                    @click="priorityFilter('high')"
                    type="button"
                  >
                    High
                  </button>
                  <button
                    class="btn btn-outline-info btn-sm"
                    :class="{ active: priorityFilterValue === 'normal' }"
                    @click="priorityFilter('normal')"
                    type="button"
                  >
                    Normal
                  </button>
                </div>
              </div>
              <table class="table table-hover text-center table-bordered">
                <thead>
                  <tr>
                    <th style="padding-left: 12px">
                      <i class="zmdi zmdi-settings zmdi-hc-2x" title="Actions"></i>
                    </th>
                    <th class="w-10 text-center">
                      <input
                        class="form-check-input ml-0 select_all_checkbox"
                        @change="($event) => set_all_item_selected($event)"
                        type="checkbox"
                        :checked="isAllSelected"
                      />
                    </th>
                    <th class="w-10">ID</th>
                    <th>Project</th>
                    <th>Title</th>
                    <th>Assigned To</th>
                    <th>Priority</th>
                    <th>Developer Status</th>
                    <th>Task Status</th>
                    <th>Rating</th>
                    <th>Actual Time</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Created at</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in all?.data" :key="item.id" :class="`table_rows table_row_${item.id}`">
                    <td>
                      <span class="icon" @click.prevent="active_row($event)"></span>
                      <div class="table_action_btns">
                        <ul>
                          <li>
                            <router-link
                              :to="{
                                name: `Details${setup.route_prefix}`,
                                params: {
                                  id: item.slug,
                                },
                              }"
                              class="border-secondary"
                            >
                              <i class="fa fa-eye text-secondary"></i>
                              Show
                            </router-link>
                          </li>
                          <li>
                            <router-link
                              :to="{
                                name: `Edit${setup.route_prefix}`,
                                params: {
                                  id: item.slug,
                                },
                              }"
                              class="border-secondary"
                            >
                              <i class="fa fa-pencil-square-o text-info"></i>
                              Edit
                            </router-link>
                          </li>
                          <li>
                            <a v-if="item.status == 'active'" href="" @click.prevent="updateStatus(item)" class="border-warning">
                              <i class="fa fa-eye-slash text-warning"></i>
                              Inactive
                            </a>
                            <a v-if="item.status == 'inactive'" href="" @click.prevent="updateStatus(item)" class="border-warning">
                              <i class="fa fa-eye text-warning"></i>
                              Active
                            </a>
                          </li>
                          <li v-if="!is_trashed_data">
                            <a @click.prevent="softDelete(item)" href="" class="border-danger">
                              <i class="fa fa-ban text-warning"></i>
                              Soft Delete
                            </a>
                          </li>
                          <li v-if="is_trashed_data">
                            <a @click.prevent="restore_data(item)" href="" class="border-danger">
                              <i class="fa fa-refresh text-warning"></i>
                              Restore data
                            </a>
                          </li>
                          <li>
                            <a @click.prevent="destroy_data(item)" href="" class="border-danger">
                              <i class="fa fa-trash text-danger"></i>
                              Destroy
                            </a>
                          </li>
                          <!-- Your existing list item -->
                          <li>
                            <a href="" @click.prevent="showOverviewModal(item)" class="border-info">
                              <i class="fa fa-info-circle text-info"></i>
                              Overview
                            </a>
                          </li>
                          <!-- Overview Modal -->
                          <!-- Overview Modal -->
                          <div
                            v-if="isOverviewModalVisible"
                            class="modal fade show d-block"
                            tabindex="-1"
                            role="dialog"
                            style="background: rgba(0, 0, 0, 0.5)"
                          >
                            <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content shadow">
                                <div class="modal-header bg-info text-white">
                                  <h5 class="modal-title"><i class="fa fa-tasks mr-2"></i>Update Task Status</h5>
                                  <button type="button" class="close text-white" @click="closeOverviewModal">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <form @submit.prevent="submitTaskOverviewForm(item)">
                                  <div class="modal-body">
                                    <div class="form-group">
                                      <label for="taskStatusSelect" class="font-weight-bold">Task Status</label>
                                      <select id="taskStatusSelect" class="form-control" v-model="item.task_status" name="task_status">
                                        <option disabled value="">Select Status</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Not Completed">Not Completed</option>
                                      </select>
                                    </div>
                                    <div class="form-group">
                                      <label for="taskRating" class="font-weight-bold">Task Rating</label>
                                      <div>
                                        <span
                                          v-for="n in 5"
                                          :key="'star-' + n"
                                          class="fa"
                                          :class="['fa-star', n <= (item.rating || 0) ? 'text-warning' : 'text-secondary']"
                                          style="font-size: 1.3em"
                                        ></span>
                                      </div>
                                      <div class="d-flex align-items-center" style="gap: 10px">
                                        <select id="taskRating" class="form-control" name="rating" v-model="item.rating">
                                          <option disabled value="">Select Rating</option>
                                          <option v-for="n in 5" :key="n" :value="n">
                                            {{ n }}
                                          </option>
                                        </select>
                                      </div>
                                    </div>
                                    <div v-if="item.task_status === 'Not Completed'" class="form-group">
                                      <label for="nextDateInput" class="font-weight-bold">Next Date</label>
                                      <input
                                        id="nextDateInput"
                                        type="date"
                                        v-model="item.start_date"
                                        name="start_date"
                                        class="form-control mb-1"
                                        placeholder="Next Date"
                                        required
                                      />
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" @click="closeOverviewModal">
                                      <i class="fa fa-times mr-1"></i>Close
                                    </button>
                                    <button
                                      v-if="selectedTask && selectedTask.task_status !== 'Pending'"
                                      type="submit"
                                      class="btn btn-info"
                                      :disabled="!item.task_status"
                                    >
                                      <i class="fa fa-save mr-1"></i>Save
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        </ul>
                      </div>
                    </td>
                    <td>
                      <input @change="set_item_selected(item, $event)" :checked="isSelected(item)" class="form-check-input ml-0" type="checkbox" />
                    </td>
                    <td>{{ index + 1 }}</td>
                    <td>{{ item.task_group_id?.name }}</td>
                    <td>{{ item.title }}</td>
                    <td>{{ item.user?.name || "Not Assigned" }}</td>
                    <td>{{ item.priority }}</td>
                    <td>{{ item.task_user_status ?? "N/A" }}</td>
                    <td :class="getTaskStatusClass(item.task_status)">
                      {{ item.task_status }}
                    </td>
                    <td>
                      <span>
                        <span
                          v-for="n in 5"
                          :key="'star-' + n"
                          class="fa"
                          :class="['fa-star', n <= (item.rating || 0) ? 'text-warning' : 'text-secondary']"
                          style="font-size: 1.3em"
                        ></span>
                        <span class="ml-2 font-weight-bold">({{ item.rating }})</span>
                      </span>
                    </td>
                    <td>{{ FindActualTime(item.start_date, item.end_date) }}</td>
                    <td>{{ formatDateTime(item.start_date) }}</td>
                    <td>{{ formatDateTime(item.end_date) }}</td>
                    <td>{{ formatDateTime(item.created_at) }}</td>

                    <!-- <td>
                      <img :src="item.image" alt="" height="50" width="50" />
                    </td> -->
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/** plugins */
import { mapActions, mapWritableState } from "pinia";
import setup from "../setup";
import { store as data_store } from "../store";
import export_all_csv from "../helpers/export_all_csv";
import export_selected_csv from "../helpers/export_selected_csv";
import export_demo_csv from "../helpers/export_demo_csv";
import debounce from "../helpers/debounce";

export default {
  data: () => ({
    setup,
    is_trashed_data: false,
    import_csv_modal_show: false,
    isOverviewModalVisible: false,
    // selectedTask: null,
    // selectedStatus: "",
    filePath: "resources/js/backend/Views/SuperAdmin/Management/TestModule/helpers/demo.csv",
  }),
  created: async function () {
    // Support both /tasks/date-wise-tasks/:date and ?date=...
    const date = this.$route.params.date || this.$route.query.date;
    if (date) {
      this.date = date;
      this.start_date = date;
      this.end_date = date;
      this.set_filter_criteria({ start_date: date, end_date: date });
    } else {
      this.date = null;
      this.start_date = null;
      this.end_date = null;
      this.set_filter_criteria({});
    }
    await this.get_all();
  },
  methods: {
    export_all_csv,
    export_selected_csv,
    export_demo_csv,
    ...mapActions(data_store, [
      "get_all",
      `restore`,
      `soft_delete`,
      `update_status`,
      "destroy",
      "bulk_action",
      "clear_selected",
      "import_data",
      "set_show_filter_canvas",
      `set_only_latest_data`,
      `set_item`,
      "set_filter_criteria",
      "set_page",
      "set_status",
      "set_paginate",
      "task_overview",
    ]),

    // taskStatusFilterValue
    showOverviewModal(item) {
      this.selectedTask = item; // keep a live reference
      this.selectedStatus = item.task_status;
      this.isOverviewModalVisible = true;
    },
    closeOverviewModal() {
      this.isOverviewModalVisible = false;
    },
    async updateTaskStatus(item) {
      try {
        let payload = {
          slug: item.slug,
          task_status: item.task_status,
        };
        // If task is completed, include rating
        if (item.task_status === "Completed" && item.rating) {
          payload.rating = item.rating;
        }
        let response = await axios.post(`/task/update/${item.slug}`, payload);
        if (response.data.status === "success") {
          window.s_alert("Task status updated!");
          await this.get_all();
        } else {
          window.s_warning(response.data?.message || "Update failed");
        }
      } catch (error) {
        window.s_warning("Error updating task status");
      }
    },

    formatDateTime(dateTime) {
      if (!dateTime) return "";
      const options = {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
      };
      return new Date(dateTime).toLocaleString("en-US", options);
    },

    active_row(event) {
      const targetRow = event.target.closest(".table_rows");
      if (!targetRow) return;
      document.querySelectorAll(".table_rows.active").forEach((row) => {
        if (row !== targetRow) row.classList.remove("active");
      });
      targetRow.classList.toggle("active");
    },

    updateStatus: async function (item) {
      let action = item.status == "active" ? "deactive" : "active";
      let con = await window.s_confirm("Are you sure want to " + action + " ?");
      if (con) {
        this.set_item(item);
        this.set_only_latest_data(true);
        let response = await this.update_status();
        if (response.data.status === "success") {
          await this.get_all();
          window.s_alert(response.data?.message);
          this.set_only_latest_data(true);
        } else {
          window.s_warning(response.data?.message);
        }
      }
    },

    softDelete: async function (item) {
      let con = await window.s_confirm("Are you sure want to delete ?");
      if (con) {
        this.set_item(item);
        this.set_only_latest_data(true);

        let response = await this.soft_delete();
        if (response.data.status === "success") {
          await this.get_all();
          window.s_alert(response.data?.message);
          this.set_only_latest_data(true);
        } else {
          window.s_warning(response.data?.message);
        }
      }
    },
    restore_data: async function (item) {
      let con = await window.s_confirm("Restore");
      if (con) {
        this.set_item(item);
        this.set_only_latest_data(true);
        let response = await this.restore();
        if (response.data.status === "success") {
          await this.get_all();
          window.s_alert("Permanently deleted.");
          this.set_only_latest_data(true);
        } else {
          window.s_warning(response.data?.message);
        }
      }
    },

    destroy_data: async function (item) {
      let con = await window.s_confirm("Permanently delete");
      if (con) {
        this.set_item(item);
        this.set_only_latest_data(true);
        let response = await this.destroy();
        if (response.data.status === "success") {
          await this.get_all();
          window.s_alert("Permanently deleted.");
          this.set_only_latest_data(true);
        } else {
          window.s_warning(response.data?.message);
        }
      }
    },
    change_status: function (status = "active") {
      if (status == "trased") {
        this.is_trashed_data = true;
      } else {
        this.is_trashed_data = false;
      }
      this.set_only_latest_data(true);
      this.set_status(status);
      this.set_page(1);
      this.get_all();
      this.set_only_latest_data(true);
    },
    set_page_data: function (link) {
      try {
        let url = new URL(link.url);
        let page = url.searchParams.get("page");
        link.url ? this.set_page(parseInt(page)) : "";
        this.get_all();
      } catch (error) {}
    },
    set_per_page_limit: function () {
      this.set_paginate(event.target.value);
      this.get_all();
    },

    set_all_item_selected(event) {
      this.selected = event.target.checked ? [...this.all.data] : [];
    },

    set_item_selected(item, event) {
      const isChecked = event.target.checked;
      const selectedItems = new Set(this.selected);
      if (isChecked) {
        selectedItems.add(item);
      } else {
        selectedItems.delete(item);
      }
      this.selected = [...selectedItems];
    },
    isSelected(item) {
      return this.selected.some((selectedItem) => selectedItem.id === item.id);
    },

    bulkActions: async function () {
      let action = event.target.value;
      let con = await window.s_confirm("Are you sure want to " + action + " items ?");
      if (con) {
        let selected_data = this.selected;
        selected_data = selected_data.map((item) => item.id);
        this.set_only_latest_data(true);
        let response = await this.bulk_action(action, selected_data);
        if (response.data.status === "success") {
          await this.get_all();
          document.querySelector(".select_all_checkbox").checked = false;
          this.clear_selected();
          this.set_only_latest_data(false);
          window.s_alert("You have " + action + " items ?");
        } else {
          window.s_warning(response.data?.message);
        }
      }
    },
    taskStatusFilter(task_status) {
      this.set_filter_criteria({ task_status }); // Update filter criteria
      this.set_only_latest_data(true); // Reset to first page
      this.get_all(); // Fetch filtered data
    },

    cleartaskStatusFilter() {
      this.set_filter_criteria({ task_status: null }); // Clear task status filter
      this.set_only_latest_data(true); // Reset to first page
      this.get_all(); // Fetch unfiltered data
    },
    priorityFilter(priority) {
      this.set_filter_criteria({ priority }); // Update filter criteria
      this.set_only_latest_data(true); // Reset to first page
      this.get_all(); // Fetch filtered data
    },

    clearPriorityFilter() {
      this.set_filter_criteria({ priority: null }); // Clear priority filter
      this.set_only_latest_data(true); // Reset to first page
      this.get_all(); // Fetch unfiltered data
    },
    FileUploadHandler: async function ($event) {
      let response = await this.import_data($event);
      if (response.data.status === "success") {
        await this.get_all();
        window.s_alert(response.data.message);
        this.set_only_latest_data(true);
        this.import_csv_modal_show = false;
      } else {
        window.s_warning(response.data?.message);
      }
    },

    set_search_key: debounce(async function (event) {
      let value = event.target.value;
      this.search_key = value;
      this.page = 1;

      this.only_latest_data = true;
      await this.get_all();
      this.only_latest_data = false;
    }, 500),

    submitTaskOverviewForm: async function (item) {
      console.log("Submitting task overview form for item:", event);
      // this.set_only_latest_data(true);
      this.set_item(item);
      let response = await this.task_overview(event);
      // await this.get_all();
      if ([200, 201].includes(response.status)) {
        this.closeOverviewModal();
        this.get_all();
        window.s_alert("Data Successfully Created");
      }
    },
    getTaskStatusClass(status) {
      switch (status) {
        case "Pending":
          return "bg-primary text-white";
        case "In Progress":
          return "bg-info text-white";
        case "Completed":
          return "bg-success text-white";
        case "Not Completed":
          return "bg-danger text-white";
        case "On Hold":
          return "bg-warning text-dark";
        default:
          return "";
      }
    },
    // Calculate actual working time between start and end dates (9 AM to 7 PM, Monday to Friday)
    FindActualTime(start_date, end_date) {
      if (!start_date || !end_date) return "N/A";
      
      try {
        const start = new Date(start_date);
        const end = new Date(end_date);

        // Validate dates
        if (isNaN(start.getTime()) || isNaN(end.getTime())) {
          return "Invalid date";
        }

        // If end date is before start date
        if (end.getTime() < start.getTime()) {
          return "Invalid range";
        }

        // Working hours: 9 AM to 7 PM (10 hours per day)
        const WORK_START_HOUR = 9;
        const WORK_END_HOUR = 19; // 7 PM in 24-hour format
        const WORK_HOURS_PER_DAY = WORK_END_HOUR - WORK_START_HOUR; // 10 hours

        let totalWorkingMinutes = 0;
        
        // Create a new date for iteration, starting from the start date
        let currentDate = new Date(start);
        
        // If start time is before 9 AM, adjust to 9 AM
        if (currentDate.getHours() < WORK_START_HOUR) {
          currentDate.setHours(WORK_START_HOUR, 0, 0, 0);
        }
        
        // If start time is after 7 PM, move to next day 9 AM
        if (currentDate.getHours() >= WORK_END_HOUR) {
          currentDate.setDate(currentDate.getDate() + 1);
          currentDate.setHours(WORK_START_HOUR, 0, 0, 0);
        }

        while (currentDate < end) {
          // Check if current date is a weekday (Monday = 1, Friday = 5)
          const dayOfWeek = currentDate.getDay();
          if (dayOfWeek >= 1 && dayOfWeek <= 5) { // Monday to Friday
            
            // Calculate work end time for current day
            let workEndTime = new Date(currentDate);
            workEndTime.setHours(WORK_END_HOUR, 0, 0, 0);
            
            // If the task ends before the work day ends, use task end time
            let effectiveEndTime = end < workEndTime ? end : workEndTime;
            
            // Calculate working minutes for this day
            let dayWorkingMs = effectiveEndTime.getTime() - currentDate.getTime();
            if (dayWorkingMs > 0) {
              totalWorkingMinutes += Math.floor(dayWorkingMs / (1000 * 60));
            }
            
            // If we've reached the end date, break
            if (end <= workEndTime) {
              break;
            }
          }
          
          // Move to next day at 9 AM
          currentDate.setDate(currentDate.getDate() + 1);
          currentDate.setHours(WORK_START_HOUR, 0, 0, 0);
        }

        // Convert total working minutes to readable format
        if (totalWorkingMinutes <= 0) {
          return "0h 0m";
        }

        const totalHours = Math.floor(totalWorkingMinutes / 60);
        const remainingMinutes = totalWorkingMinutes % 60;

        return `${totalHours}(h) ${remainingMinutes}(m)`;
      } catch (error) {
        console.error("Error calculating actual working time:", error);
        return "Error";
      }
    },
    onDateChange() {
      if (this.date) {
        this.start_date = this.date;
        this.end_date = this.date;
        this.set_filter_criteria({ start_date: this.date, end_date: this.date });
      } else {
        this.start_date = null;
        this.end_date = null;
        this.set_filter_criteria({});
      }
      this.get_all();
    },
  },
  computed: {
    ...mapWritableState(data_store, [
      "all",
      "show_filter_canvas",
      "active_data_count",
      "inactive_data_count",
      "trased_data_count",
      "status",
      "selected",
      "paginate",
      "sort_type",
      "sort_by_cols",
      "sort_by_col",
      "start_date",
      "end_date",
      "priority",
      "search_key",
      "page",
      "date",
    ]),
    isAllSelected() {
      return this.all?.data?.length > 0 && this.all.data?.every((item) => this.selected.some((s) => s.id === item.id));
    },
  },

  watch: {
    is_trashed_data: {
      handler: function (newValue, oldValue) {
        this.is_trashed_data = newValue;
      },
      immediate: true,
    },

    start_date: {
      handler: function (v) {
        let data = {
          start_date: v,
        };
        this.set_filter_criteria(data);
      },
      deep: true,
    },
    end_date: {
      handler: function (v) {
        let data = {
          end_date: v,
        };
        this.set_filter_criteria(data);
      },
      deep: true,
    },
    priority: {
      handler: function (v) {
        let data = {
          priority: v,
        };
        this.set_filter_criteria(data);
      },
      deep: true,
    },
    date: {
      handler: function (v) {
        if (v) {
          this.start_date = v;
          this.end_date = v;
          this.set_filter_criteria({ start_date: v, end_date: v });
        } else {
          this.start_date = null;
          this.end_date = null;
          this.set_filter_criteria({});
        }
      },
      immediate: true,
    },
  },
};
</script>
