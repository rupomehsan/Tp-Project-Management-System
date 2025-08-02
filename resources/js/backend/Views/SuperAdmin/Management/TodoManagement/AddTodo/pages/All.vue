<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <div class="row align-items-center">
              <!-- Title Section -->
              <div class="col-12 col-md-3 mb-2 mb-md-0">
                <h5 class="text-capitalize mb-0">
                  {{ setup.all_page_title }}
                </h5>
              </div>

              <!-- Search Input -->
              <div class="col-12 col-md-6 mb-2 mb-md-0">
                <input class="form-control" @keyup="(e) => set_search_key(e)" placeholder="Search" />
              </div>

              <!-- Sorting Button -->
              <div class="col-12 col-md-3 text-md-right text-sm-left">
                <button class="btn btn-outline-success btn-sm" @click="set_show_filter_canvas"><i class="fa fa-gear mx-2"></i>Filter</button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="table-responsive table_responsive card_body_fixed_height">
              <div class="filter-section mb-3 p-3 rounded">
                <div class="container-fluid">
                  <div class="row g-3">
                    <!-- Task Status Filter -->
                    <div class="col-12 col-lg-7">
                      <div class="filter-group">
                        <div class="filter-label">
                          <i class="fa fa-tasks mr-2 text-primary"></i>
                          <span class="font-weight-bold">Task Status</span>
                        </div>
                        <div class="filter-buttons">
                          <button
                            class="btn btn-filter btn-outline-secondary"
                            :class="{ 'active': taskStatusFilterValue === null || taskStatusFilterValue === undefined }"
                            @click="cleartaskStatusFilter"
                            type="button"
                          >
                            <i class="fa fa-list mr-1"></i>
                            <span class="d-none d-sm-inline">All Tasks</span>
                            <span class="d-sm-none">All</span>
                            <small v-if="taskStatusFilterValue === null || taskStatusFilterValue === undefined" class="text-success">(Active)</small>
                          </button>
                          <button
                            class="btn btn-filter btn-outline-primary"
                            :class="{ 'active': taskStatusFilterValue === 'pending' }"
                            @click="taskStatusFilter('pending')"
                            type="button"
                          >
                            <i class="fa fa-clock-o mr-1"></i>
                            Pending
                            <small v-if="taskStatusFilterValue === 'pending'" class="text-success">(Active)</small>
                          </button>
                          <button
                            class="btn btn-filter btn-outline-success"
                            :class="{ 'active': taskStatusFilterValue === 'completed' }"
                            @click="taskStatusFilter('completed')"
                            type="button"
                          >
                            <i class="fa fa-check mr-1"></i>
                            <span class="d-none d-sm-inline">Completed</span>
                            <span class="d-sm-none">Done</span>
                            <small v-if="taskStatusFilterValue === 'completed'" class="text-success">(Active)</small>
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Priority Filter -->
                    <div class="col-12 col-lg-5">
                      <div class="filter-group">
                        <div class="filter-label">
                          <i class="fa fa-flag mr-2 text-warning"></i>
                          <span class="font-weight-bold">Priority Level</span>
                        </div>
                        <div class="filter-buttons">
                          <button
                            class="btn btn-filter btn-outline-secondary"
                            :class="{ 'active': priorityFilterValue === null || priorityFilterValue === undefined }"
                            @click="clearPriorityFilter"
                            type="button"
                          >
                            <i class="fa fa-filter mr-1"></i>
                            All
                            <small v-if="priorityFilterValue === null || priorityFilterValue === undefined" class="text-success">(Active)</small>
                          </button>
                          <button
                            class="btn btn-filter btn-outline-danger"
                            :class="{ 'active': priorityFilterValue === 'urgent' }"
                            @click="priorityFilter('urgent')"
                            type="button"
                          >
                            <i class="fa fa-exclamation-triangle mr-1"></i>
                            Urgent
                            <small v-if="priorityFilterValue === 'urgent'" class="text-success">(Active)</small>
                          </button>
                          <button
                            class="btn btn-filter btn-outline-warning"
                            :class="{ 'active': priorityFilterValue === 'high' }"
                            @click="priorityFilter('high')"
                            type="button"
                          >
                            <i class="fa fa-arrow-up mr-1"></i>
                            High
                            <small v-if="priorityFilterValue === 'high'" class="text-success">(Active)</small>
                          </button>
                          <button
                            class="btn btn-filter btn-outline-info"
                            :class="{ 'active': priorityFilterValue === 'normal' }"
                            @click="priorityFilter('normal')"
                            type="button"
                          >
                            <i class="fa fa-minus mr-1"></i>
                            Normal
                            <small v-if="priorityFilterValue === 'normal'" class="text-success">(Active)</small>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <table class="table table-hover table-bordered">
                <thead>
                  <tr>
 <th style="padding-left: 12px">
                      <i class="zmdi zmdi-settings zmdi-hc-2x" title="Actions"></i>
                    </th>
                    <th class="w-10">
                      <input
                        class="form-check-input ml-0 select_all_checkbox"
                        @change="($event) => set_all_item_selected($event)"
                        type="checkbox"
                        :checked="isAllSelected"
                      />
                    </th>
                    <th class="w-10">Index</th>
                    <th class="">Name</th>
                    <th class="">Description</th>
                    <th class="">Task Status</th>
                    <th class="">Task Priority</th>
                    <th class="">Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in all?.data" :key="item.id" :class="`table_rows table_row_${item.id}`">
                    <td class="text-limit" :title="`Actions for ${item.category_id?.name || 'Todo'}`">
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
                        </ul>
                      </div>
                    </td>
                    <td class="text-limit" :title="`Select Todo`">
                      <input @change="set_item_selected(item, $event)" :checked="isSelected(item)" class="form-check-input ml-0" type="checkbox" />
                    </td>
                    <td class="text-limit" :title="`ID: ${index + 1}`">{{ index + 1 }}</td>
                    <td class="text-limit" :title="`Category: ${item.category_id?.name || 'N/A'}`">{{ item.category_id?.name }}</td>
                    <td class="text-limit" :title="`Description: ${item.description?.replace(/<[^>]*>/g, '') || 'N/A'}`">{{ truncateDescription(item.description) }}</td>
                    <td class="text-limit" :title="`Status: ${item.task_status}`">
                      <div class="d-flex align-items-center justify-content-start" style="gap: 0.5rem">
                        <span class="font-weight-bold text-capitalize" style="min-width: 100px">{{ item.task_status }}</span>
                        <label class="switch mb-0" style="margin-bottom: 0" :class="{ 'opacity-50': item.updating }">
                          <input 
                            type="checkbox" 
                            :checked="item.task_status === 'completed'" 
                            @change="toggleTodoStatus(item)"
                            :disabled="item.updating"
                          />
                          <span class="slider round"></span>
                        </label>
                      </div>
                    </td>
                    <td class="text-limit text-capitalize" :title="`Priority: ${item.priority}`">{{ item.priority }}</td>

                    <td class="text-limit" :title="`Created At: ${formatDateTime(item.created_at)}`">{{ formatDateTime(item.created_at) }}</td>

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
              <div class="mr-2 mb-2" v-if="this.selected?.length">
                <a href="" @click.prevent="export_selected_csv(selected)" class="btn action_btn btn-sm btn-secondary d-flex align-items-center">
                  <i class="fa fa-sign-out mr-2"></i> Export ({{ this.selected?.length }})
                </a>
              </div>
              <div class="mr-2 mb-2">
                <a href="" @click.prevent="import_csv_modal_show = true" class="btn action_btn btn-sm btn-secondary d-flex align-items-center">
                  <i class="fa fa-download mr-2"></i> Import
                </a>
              </div>
              <div class="mr-2 mb-2">
                <a href="" @click.prevent="change_status(`active`)" class="btn action_btn btn-sm btn-success d-flex align-items-center">
                  <i class="fa fa fa fa-eye mr-2"></i> Active ({{ active_data_count }})
                </a>
              </div>
              <div class="mr-2 mb-2">
                <a href="" @click.prevent="change_status(`inactive`)" class="btn action_btn btn-sm btn-warning d-flex align-items-center">
                  <i class="fa fa fa-eye-slash mr-2"></i>
                  Inactive ({{ inactive_data_count }})
                </a>
              </div>
              <div class="mr-2 mb-2">
                <a href="" @click.prevent="change_status(`trased`)" class="btn action_btn btn-sm btn-danger d-flex align-items-center">
                  <i class="fa fa-trash mr-2"></i> Trased ({{ trased_data_count }})
                </a>
              </div>

              <div class="mr-2 mb-2" v-if="this.selected?.length">
                <select class="form-control" style="width: 100px; height: 30px; font-size: 12px" @change="bulkActions">
                  <option disabled selected>Select action</option>
                  <option value="inactive">Inactive</option>
                  <option value="active">Action</option>
                  <option value="soft_delete">Soft Delete</option>
                  <option value="restore">Restore</option>
                  <option value="destroy">Destroy</option>
                </select>
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
  mounted: function () {
    // Initialize filter_criteria if it's not defined
    if (!this.filter_criteria) {
      console.log('Initializing filter_criteria');
      this.set_filter_criteria({});
    }
    console.log('Component mounted, filter_criteria:', this.filter_criteria);
  },
  methods: {
    /**
     * Toggle the status of a todo item between Completed and Pending
     */
    async toggleTodoStatus(item) {
      // Prevent multiple simultaneous updates
      if (item.updating) return;
      
      const newStatus = item.task_status === "completed" ? "pending" : "completed";
      
      // Set loading state
      item.updating = true;
      
      try {
        // Use the correct API endpoint from the routes
        const url = `${setup.api_host}/${setup.api_version}/${setup.api_end_point}/update-status`;
        
        const response = await axios.post(url, { 
          slug: item.slug, 
          task_status: newStatus 
        });
        
        if (response.data.status === "success") {
          // Update the item in the local state
          item.task_status = newStatus;
          window.s_alert("Task status updated successfully!");
        } else {
          window.s_warning(response.data?.message || "Failed to update task status");
        }
      } catch (error) {
        console.error("Error updating task status:", error);
        window.s_warning("Error updating task status. Please try again.");
      } finally {
        // Clear loading state
        item.updating = false;
      }
    },
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
    
    /**
     * Truncate description text by stripping HTML and limiting characters
     */
    truncateDescription(description, maxLength = 30) {
      if (!description) return 'N/A';
      
      // Strip HTML tags
      const plainText = description.replace(/<[^>]*>/g, '');
      
      // Trim whitespace
      const trimmedText = plainText.trim();
      
      // Truncate if longer than maxLength
      if (trimmedText.length <= maxLength) {
        return trimmedText;
      }
      
      return trimmedText.substring(0, maxLength) + '...';
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

    taskStatusFilter(task_status) {
      console.log('=== taskStatusFilter clicked ===');
      console.log('Setting task status filter to:', task_status);
      console.log('Current filter criteria BEFORE:', this.filter_criteria);
      
      // Update filter criteria while preserving other filters
      const newCriteria = { 
        ...this.filter_criteria,
        task_status 
      };
      console.log('New criteria to set:', newCriteria);
      
      this.set_filter_criteria(newCriteria);
      console.log('Current filter criteria AFTER:', this.filter_criteria);
      
      this.set_only_latest_data(true); // Reset to first page
      this.get_all(); // Fetch filtered data
    },
    cleartaskStatusFilter() {
      console.log('=== cleartaskStatusFilter clicked ===');
      console.log('Clearing task status filter');
      console.log('Current filter criteria BEFORE:', this.filter_criteria);
      
      // Create new criteria object without task_status
      const updatedCriteria = { ...this.filter_criteria };
      delete updatedCriteria.task_status;
      
      // Alternative: Set task_status to null explicitly
      updatedCriteria.task_status = null;
      
      console.log('Updated criteria to set:', updatedCriteria);
      
      this.set_filter_criteria(updatedCriteria);
      console.log('Current filter criteria AFTER:', this.filter_criteria);
      
      this.set_only_latest_data(true); // Reset to first page
      this.get_all(); // Fetch unfiltered data
    },
    clearPriorityFilter() {
      console.log('=== clearPriorityFilter clicked ===');
      console.log('Clearing priority filter');
      console.log('Current filter criteria BEFORE:', this.filter_criteria);
      
      // Create new criteria object without priority
      const updatedCriteria = { ...this.filter_criteria };
      delete updatedCriteria.priority;
      
      // Alternative: Set priority to null explicitly
      updatedCriteria.priority = null;
      
      console.log('Updated criteria to set:', updatedCriteria);
      
      this.set_filter_criteria(updatedCriteria);
      console.log('Current filter criteria AFTER:', this.filter_criteria);
      
      this.set_only_latest_data(true); // Reset to first page
      this.get_all(); // Fetch unfiltered data
    },
    priorityFilter(priority) {
      console.log('=== priorityFilter clicked ===');
      console.log('Setting priority filter to:', priority);
      console.log('Current filter criteria BEFORE:', this.filter_criteria);
      
      // Update filter criteria while preserving other filters
      const newCriteria = { 
        ...this.filter_criteria,
        priority 
      };
      console.log('New criteria to set:', newCriteria);
      
      this.set_filter_criteria(newCriteria);
      console.log('Current filter criteria AFTER:', this.filter_criteria);
      
      this.set_only_latest_data(true); // Reset to first page
      this.get_all(); // Fetch filtered data
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
      "search_key",
      "page",
      "filter_criteria",
    ]),
    isAllSelected() {
      return this.all?.data?.length > 0 && this.all.data?.every((item) => this.selected.some((s) => s.id === item.id));
    },
    taskStatusFilterValue() {
      console.log('Computing taskStatusFilterValue:', this.filter_criteria);
      const value = this.filter_criteria?.task_status;
      // Return null for both undefined and null values, and empty string
      return (value === undefined || value === null || value === '') ? null : value;
    },
    priorityFilterValue() {
      console.log('Computing priorityFilterValue:', this.filter_criteria);
      const value = this.filter_criteria?.priority;
      // Return null for both undefined and null values, and empty string
      return (value === undefined || value === null || value === '') ? null : value;
    },
  },

  watch: {
    is_trashed_data: {
      handler: function (newValue, oldValue) {
        this.is_trashed_data = newValue;
      },
      immediate: true,
    },

    filter_criteria: {
      handler: function (newValue, oldValue) {
        console.log('Filter criteria changed:', newValue, 'Old:', oldValue);
      },
      deep: true,
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
<style scoped>
.switch {
  position: relative;
  display: inline-block;
  width: 36px;
  height: 20px;
}
.switch input {
  display: none;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: 0.4s;
  border-radius: 20px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 2px;
  bottom: 2px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
}
input:checked + .slider {
  background-color: #28a745;
}
input:checked + .slider:before {
  transform: translateX(16px);
}

.filter-section {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  border: 1px solid #4a5568;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.filter-group {
  margin-bottom: 1rem;
}

.filter-label {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
  color: #e2e8f0;
  font-size: 0.95rem;
}

.filter-label i {
  font-size: 1.1rem;
}

.filter-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.btn-filter {
  font-size: 0.85rem;
  padding: 0.5rem 0.75rem;
  border-radius: 0.375rem;
  transition: all 0.2s ease-in-out;
  font-weight: 500;
  position: relative;
  overflow: hidden;
}

.btn-filter:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-filter.active {
  transform: translateY(0);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.btn-filter i {
  font-size: 0.8rem;
}

@media (max-width: 576px) {
  .filter-buttons {
    justify-content: center;
  }
  
  .btn-filter {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
  }
  
  .filter-label {
    justify-content: center;
    text-align: center;
  }
}
</style>

