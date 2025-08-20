<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <div class="row align-items-center justify-content-between">
              <!-- Title Section -->
              <div class="col-12 col-md-3 mb-2 mb-md-0">
                <h5 class="text-capitalize mb-0">
                  {{ setup.all_page_title }}
                </h5>
              </div>

              <!-- Sorting Button -->
              <div class="col-12 col-md-3 text-md-right text-sm-left">
                <button class="btn btn-outline-success btn-sm" @click="set_show_filter_canvas"><i class="fa fa-gear mx-2"></i>Filter</button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="table-responsive table_responsive card_body_fixed_height">
              <table class="table table-hover text-center table-bordered">
                <thead>
                  <tr>
                    <th style="padding-left: 12px">
                      <i class="zmdi zmdi-settings zmdi-hc-2x" title="Actions"></i>
                    </th>

                    <th class="w-10">ID</th>
                    <th>Date</th>
                    <th>Check_in</th>
                    <th>Check_out</th>
                    <th>Late Status</th>
                    <th>Late Minutes</th>
                    <th>Attendance Status</th>
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
                                name: `Edit${setup.route_prefix}`,
                                params: {
                                  id: item.slug,
                                },
                              }"
                              class="border-secondary"
                            >
                              <i class="fa fa-check-square text-info"></i>
                              Checkout
                            </router-link>
                          </li>
                        </ul>
                      </div>
                    </td>

                    <td>{{ index + 1 }}</td>

                    <td>{{ item.date }}</td>

                    <td
                      :style="{
                        backgroundColor: (() => {
                          if (!item.check_in) return '';
                          const time = new Date(item.check_in);
                          const hours = time.getHours();
                          const minutes = time.getMinutes();
                          const totalMinutes = hours * 60 + minutes;
                          
                          // Office timing: 9:00 AM to 7:00 PM
                          const nineFifteenAM = 9 * 60 + 15; // 555 minutes (9:15 AM)
                          const elevenAM = 11 * 60; // 660 minutes (11:00 AM)
                          
                          if (totalMinutes <= nineFifteenAM) {
                            return '#28a745'; // On Time - Green
                          } else if (totalMinutes > nineFifteenAM && totalMinutes < elevenAM) {
                            return '#ffc107'; // Late - Warning (yellow)
                          } else {
                            return '#dc3545'; // Very Late - Danger (red)
                          }
                        })(),
                        color: 'white'
                      }"
                    >
                      {{ formatDateTime(item.check_in) }}
                    </td>
                    <td
                      :style="{
                        backgroundColor: (() => {
                          if (!item.check_out) return '';
                          const time = new Date(item.check_out);
                          const hours = time.getHours();
                          const minutes = time.getMinutes();
                          const totalMinutes = hours * 60 + minutes;
                          if (totalMinutes < 1140)
                            // Before 7:00 PM (19:00)
                            return '#ffc107'; // Warning (yellow)
                          return '#28a745'; // After 7:00 PM - Green
                        })(),
                        color: (() => {
                          if (!item.check_out) return '';
                          return 'white'; // White text for better contrast
                        })(),
                      }"
                    >
                      {{ formatDateTime(item.check_out) }}
                    </td>
                    <td
                      :class="{
                        'text-success': item.attendance_status !== 'Absent' && getLateStatus(item.check_in) === 'On Time',
                        'text-warning': item.attendance_status !== 'Absent' && getLateStatus(item.check_in) === 'Late',
                        'text-danger': item.attendance_status !== 'Absent' && getLateStatus(item.check_in) === 'Very Late',
                      }"
                    >
                      {{ item.attendance_status === 'Absent' ? 'N/A' : getLateStatus(item.check_in) }}
                    </td>
                    <td
                      :class="{
                        'text-success': item.attendance_status !== 'Absent' && getLateMinutes(item.check_in) === 0,
                        'text-warning': item.attendance_status !== 'Absent' && getLateMinutes(item.check_in) > 0 && getLateMinutes(item.check_in) < 105,
                        'text-danger': item.attendance_status !== 'Absent' && getLateMinutes(item.check_in) >= 105,
                      }"
                    >
                  
                      {{ item.attendance_status === 'Absent' ? 'N/A' : formatLateMinutes(item.check_in) }}
                    </td>
                    <td>{{ item.attendance_status }}</td>

                    <!-- <td>
                      <img :src="item.image" alt="" height="50" width="50" />
                    </td> -->
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="mx-3">
            <nav aria-label="" class="d-flex gap-2 align-items-center" style="gap: 10px">
              <ul class="pagination my-2" style="font-size: 11px">
                <template v-for="(link, index) in all?.links" :key="index">
                  <li class="page-item" :class="{ active: link.active }">
                    <a
                      class="page-link"
                      :class="all?.current_page == all?.last_page && all?.links.length - 1 == index ? 'disabled' : ''"
                      @click.prevent="set_page_data(link)"
                      :href="link.url"
                      v-html="`<span>${link.label}</span>`"
                    >
                    </a>
                  </li>
                </template>
              </ul>
              <div class="d-flex" style="gap: 5px">
                <span></span>
                <span>{{ all?.from }}</span>
                <span>-</span>
                <span>{{ all?.to }}</span>
                <span>of</span>
                <span>{{ all?.total }}</span>
              </div>
              <div class="d-flex" style="gap: 5px">
                <span></span>
                <span> Limit </span>
                <select v-model="paginate" @change="set_per_page_limit" class="bg-transparent text-white rounded-1">
                  <option value="5">05</option>
                  <option value="10">10</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                  <option value="200">200</option>
                  <option value="300">300</option>
                  <option value="400">400</option>
                  <option value="500">500</option>
                </select>
              </div>
            </nav>
          </div>
          <div class="card-footer py-2">
            <div class="d-flex flex-wrap align-items-center">
              <div class="mr-2 mb-2">
                <router-link
                  :to="{
                    name: `Create${setup.route_prefix}`,
                  }"
                  class="btn action_btn mr-2 btn-sm btn-info d-flex align-content-center align-items-center"
                >
                  <i class="fa fa-edit mr-2"></i>
                  Create
                </router-link>
              </div>
              <div class="mr-2 mb-2">
                <a
                  href=""
                  @click.prevent="() => export_all_csv()"
                  class="btn action_btn mr-2 btn-sm btn-primary d-flex align-content-center align-items-center"
                >
                  <i class="fa fa-print mr-2"></i>
                  Export All
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="loader export_loader">
      <div class="loader_body">
        <div class="progress"></div>
        <div class="load_amount">
          <h4>0</h4>
          <h5>%</h5>
        </div>
      </div>
    </div>
    <div class="off_canvas quick_view d-none">
      <div class="off_canvas_body">
        <div class="header">
          <h3 class="heading_text">Quick View</h3>
          <button class="btn btn-sm btn-outline-danger">
            <i class="fa fa-close"></i>
          </button>
        </div>
        <div class="data_content">
          <table class="table quick_modal_table">
            <tbody>
              {$tableRows}
            </tbody>
          </table>
        </div>
      </div>
      <div class="off_canvas_overlay"></div>
    </div>
    <div class="off_canvas data_filter" :class="`${show_filter_canvas ? 'active' : ''}`">
      <div class="off_canvas_body">
        <div class="header">
          <h3 class="heading_text">Filter</h3>
          <button class="close_button" @click="set_show_filter_canvas(false)">
            <span class="fa fa-close border p-2"></span>
          </button>
        </div>
        <div class="data_content">
          <div class="filter_item">
            <label for="start_date">Start Date</label>
            <label for="start_date" class="text-capitalize d-block date_custom_control">
              <input v-model="start_date" type="date" id="start_date" name="start_date" class="form-control" />
              <!-- <div class="form-control preview"></div> -->
            </label>
          </div>
          <div class="filter_item">
            <label for="end_date">End Date</label>
            <label for="end_date" class="text-capitalize d-block date_custom_control">
              <input v-model="end_date" type="date" id="end_date" name="end_date" class="form-control" />
              <!-- <div class="form-control preview"></div> -->
            </label>
          </div>
          <div class="filter_item">
            <label for="sort_by_col">Sort By Col</label
            ><label for="sort_by_col" class="text-capitalize d-block date_custom_control">
              <select v-model="sort_by_col" class="form-control">
                <option v-for="col in sort_by_cols" :key="col">
                  {{ col }}
                </option>
              </select>
            </label>
          </div>
          <div class="filter_item">
            <label for="sort_by_col">Sort Type</label
            ><label for="sort_by_col" class="text-capitalize d-block date_custom_control">
              <select v-model="sort_type" class="form-control">
                <option v-for="col in ['ASC', 'DESC']" :key="col">
                  {{ col }}
                </option>
              </select>
            </label>
          </div>
          <div class="filter_item d-flex justify-content-between align-items-center">
            <button @click.prevent="get_all()" type="button" class="btn btn-sm btn-outline-info">Submit</button>
            <button class="btn btn-outline-danger btn-sm" @click="reset_filters">Reset</button>
          </div>
        </div>
      </div>
      <div class="off_canvas_overlay"></div>
    </div>
    <div class="modal fade" :class="`${import_csv_modal_show ? 'show d-block' : 'd-none'}`" id="primarymodal" aria-modal="true">
      <div class="modal-dialog modal-dialog-centered">
        <form @submit.prevent="FileUploadHandler">
          <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
              <h5 class="modal-title text-white">Import {{ setup.prefix }}</h5>
              <button @click="import_csv_modal_show = false" type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="">
                <label for="">Upload file</label>
                <input type="file" name="file" class="form-control" required />
              </div>
              <p class="mt-3">Please check the sample CSV file below to ensure compatibility with the demo data import.</p>
              <a href="" @click.prevent="export_demo_csv" class="btn btn-sm btn-primary">Download Demo CSV</a>
            </div>
            <div class="modal-footer">
              <button @click="import_csv_modal_show = false" type="button" class="btn btn-light" data-dismiss="modal">
                <i class="fa fa-times"></i> Close
              </button>
              <button type="submit" class="btn btn-primary"><i class="fa fa-download"></i> Import</button>
            </div>
          </div>
        </form>
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
    filePath: "resources/js/backend/Views/SuperAdmin/Management/TestModule/helpers/demo.csv",
  }),
  created: async function () {
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
      "reset_filter_criteria",
    ]),

    async reset_filters() {
      this.reset_filter_criteria();
      await this.get_all();
    },

    isCheckoutAllowed(item) {
      if (!item.check_in) return false;

      const today = new Date();
      const checkInDate = new Date(item.check_in);

      // Set both dates to start of day for comparison
      today.setHours(0, 0, 0, 0);
      checkInDate.setHours(0, 0, 0, 0);

      // Only allow checkout if check-in date is exactly today (same date)
      return checkInDate.getTime() === today.getTime();
    },
    formatDateTime(dateTime) {
      if (!dateTime) return "N/A";
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

    getLateStatus(checkInTime) {
      if (!checkInTime) return "N/A";
      
      try {
        const time = new Date(checkInTime);
        const hours = time.getHours();
        const minutes = time.getMinutes();
        const totalMinutes = hours * 60 + minutes;
        
        // Office timing: 9:00 AM to 7:00 PM
        const nineAM = 9 * 60; // 540 minutes (9:00 AM)
        const nineFifteenAM = 9 * 60 + 15; // 555 minutes (9:15 AM)
        const elevenAM = 11 * 60; // 660 minutes (11:00 AM)
        
        if (totalMinutes <= nineFifteenAM) {
          return "On Time"; // 9:00 AM to 9:15 AM
        } else if (totalMinutes > nineFifteenAM && totalMinutes < elevenAM) {
          return "Late"; // After 9:15 AM but before 11:00 AM
        } else {
          return "Very Late"; // After 11:00 AM
        }
      } catch (error) {
        return "N/A";
      }
    },

    getLateMinutes(checkInTime) {
      if (!checkInTime) return 0;
      
      try {
        const time = new Date(checkInTime);
        const hours = time.getHours();
        const minutes = time.getMinutes();
        const totalMinutes = hours * 60 + minutes;
        
        // Office start time: 9:15 AM (grace period)
        const nineFifteenAM = 9 * 60 + 15; // 555 minutes (9:15 AM)
        
        if (totalMinutes <= nineFifteenAM) {
          return 0; // On time or early
        } else {
          return totalMinutes - nineFifteenAM; // Minutes late
        }
      } catch (error) {
        return 0;
      }
    },

    formatLateMinutes(checkInTime) {
      const lateMinutes = this.getLateMinutes(checkInTime);
      
      if (lateMinutes === 0) {
        return "On Time";
      } else if (lateMinutes < 60) {
        return `${lateMinutes} min late`;
      } else {
        const hours = Math.floor(lateMinutes / 60);
        const remainingMinutes = lateMinutes % 60;
        if (remainingMinutes === 0) {
          return `${hours}h late`;
        } else {
          return `${hours}h ${remainingMinutes}m late`;
        }
      }
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
      "search_key",
      "page",
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
  },
};
</script>
