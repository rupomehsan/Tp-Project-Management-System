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
              <!-- Modern Professional Filter Section -->
              <div class="filter-dashboard-container">
                <div class="filter-card">
                  <div class="filter-header">
                    <div class="filter-header-content">
                      <div class="filter-icon-wrapper">
                        <i class="zmdi zmdi-filter-list"></i>
                      </div>
                      <div class="filter-header-text">
                        <h3 class="filter-main-title">Todo Filters</h3>
                        <p class="filter-subtitle">Filter your todos by status and priority</p>
                      </div>
                    </div>
                  </div>
                  <div class="filter-content">
                    <div class="row">
                      <!-- Status Filter -->
                      <div class="col-lg-6 col-md-12">
                        <div class="filter-group-modern">
                          <div class="filter-label-container">
                            <div class="filter-label-icon">
                              <i class="zmdi zmdi-assignment"></i>
                            </div>
                            <div class="filter-label-text">
                              <span class="filter-label-modern">Task Status</span>
                              <span class="filter-label-description">Filter by completion status</span>
                            </div>
                          </div>
                          <div class="filter-buttons-container">
                            <div class="btn-group-modern btn-group-flex-status">
                              <input type="radio" class="btn-check-modern" name="statusFilter" id="status-all" autocomplete="off" :checked="!taskStatusFilterValue">
                              <label class="btn-modern btn-modern-secondary btn-modern-sm" for="status-all" @click="cleartaskStatusFilter">
                                <span class="btn-icon"><i class="zmdi zmdi-view-list"></i></span>
                                <span class="btn-text">All Todos</span>
                                <span class="btn-badge">{{ (all?.data || []).length }}</span>
                              </label>
                              
                              <input type="radio" class="btn-check-modern" name="statusFilter" id="status-pending" autocomplete="off" :checked="taskStatusFilterValue === 'pending'">
                              <label class="btn-modern btn-modern-warning btn-modern-sm" for="status-pending" @click="taskStatusFilter('pending')">
                                <span class="btn-icon"><i class="zmdi zmdi-time"></i></span>
                                <span class="btn-text">Pending</span>
                              </label>
                              
                              <input type="radio" class="btn-check-modern" name="statusFilter" id="status-completed" autocomplete="off" :checked="taskStatusFilterValue === 'completed'">
                              <label class="btn-modern btn-modern-success btn-modern-sm" for="status-completed" @click="taskStatusFilter('completed')">
                                <span class="btn-icon"><i class="zmdi zmdi-check"></i></span>
                                <span class="btn-text">Completed</span>
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Priority Filter -->
                      <div class="col-lg-6 col-md-12">
                        <div class="filter-group-modern">
                          <div class="filter-label-container">
                            <div class="filter-label-icon">
                              <i class="zmdi zmdi-flag"></i>
                            </div>
                            <div class="filter-label-text">
                              <span class="filter-label-modern">Priority Level</span>
                              <span class="filter-label-description">Filter by task priority</span>
                            </div>
                          </div>
                          <div class="filter-buttons-container">
                            <div class="btn-group-modern btn-group-flex-priority">
                              <input type="radio" class="btn-check-modern" name="priorityFilter" id="priority-all" autocomplete="off" :checked="!priorityFilterValue">
                              <label class="btn-modern btn-modern-secondary btn-modern-sm" for="priority-all" @click="clearPriorityFilter">
                                <span class="btn-icon"><i class="zmdi zmdi-view-list"></i></span>
                                <span class="btn-text">All</span>
                              </label>
                              
                              <input type="radio" class="btn-check-modern" name="priorityFilter" id="priority-urgent" autocomplete="off" :checked="priorityFilterValue === 'urgent'">
                              <label class="btn-modern btn-modern-danger btn-modern-sm" for="priority-urgent" @click="priorityFilter('urgent')">
                                <span class="btn-icon"><i class="zmdi zmdi-alert-circle"></i></span>
                                <span class="btn-text">Urgent</span>
                                <span class="priority-indicator urgent"></span>
                              </label>
                              
                              <input type="radio" class="btn-check-modern" name="priorityFilter" id="priority-high" autocomplete="off" :checked="priorityFilterValue === 'high'">
                              <label class="btn-modern btn-modern-warning btn-modern-sm" for="priority-high" @click="priorityFilter('high')">
                                <span class="btn-icon"><i class="zmdi zmdi-triangle-up"></i></span>
                                <span class="btn-text">High</span>
                                <span class="priority-indicator high"></span>
                              </label>
                              
                              <input type="radio" class="btn-check-modern" name="priorityFilter" id="priority-normal" autocomplete="off" :checked="priorityFilterValue === 'normal'">
                              <label class="btn-modern btn-modern-info btn-modern-sm" for="priority-normal" @click="priorityFilter('normal')">
                                <span class="btn-icon"><i class="zmdi zmdi-minus"></i></span>
                                <span class="btn-text">Normal</span>
                                <span class="priority-indicator normal"></span>
                              </label>
                            </div>
                          </div>
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
                        </ul>
                      </div>
                    </td>
                    <td>
                      <input @change="set_item_selected(item, $event)" :checked="isSelected(item)" class="form-check-input ml-0" type="checkbox" />
                    </td>
                    <td>{{ index + 1 }}</td>
                    <td>{{ item.category_id?.name }}</td>
                    <td v-html="item.description"></td>
                    <td>
                      <div class="d-flex align-items-center justify-content-start" style="gap: 0.5rem">
                        <span class="font-weight-bold text-capitalize" style="min-width: 100px">{{ item.task_status }}</span>
                        <label class="switch mb-0" style="margin-bottom: 0">
                          <input type="checkbox" :checked="item.task_status === 'completed'" @change="toggleTodoStatus(item)" />
                          <span class="slider round"></span>
                        </label>
                      </div>
                    </td>
                    <td class="text-capitalize">{{ item.priority }}</td>

                    <td>{{ formatDateTime(item.created_at) }}</td>

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
  methods: {
    /**
     * Toggle the status of a todo item between Completed and Pending
     */
    async toggleTodoStatus(item) {
      const newStatus = item.task_status === "completed" ? "pending" : "completed";
      try {
        // You may need to adjust the API endpoint and payload as per your backend
        const response = await axios.post(`/todo/update-status?slug=${item.slug}`, { task_status: newStatus });
        if (response.data.status === "success") {
          item.task_status = newStatus;
          window.s_alert("Task status updated!");
        } else {
          window.s_warning(response.data?.message || "Update failed");
        }
      } catch (error) {
        window.s_warning("Error updating task status");
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
      this.set_filter_criteria({ task_status }); // Update filter criteria
      this.set_only_latest_data(true); // Reset to first page
      this.get_all(); // Fetch filtered data
    },
    cleartaskStatusFilter() {
      this.set_filter_criteria({ task_status: null }); // Clear task status filter
      this.set_only_latest_data(true); // Reset to first page
      this.get_all(); // Fetch unfiltered data
    },
    clearPriorityFilter() {
      this.set_filter_criteria({ priority: null }); // Clear priority filter
      this.set_only_latest_data(true); // Reset to first page
      this.get_all(); // Fetch unfiltered data
    },
    priorityFilter(priority) {
      this.set_filter_criteria({ priority }); // Update filter criteria
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
    ]),
    isAllSelected() {
      return this.all?.data?.length > 0 && this.all.data?.every((item) => this.selected.some((s) => s.id === item.id));
    },
    taskStatusFilterValue() {
      return this.$store?.state?.filter_criteria?.task_status || null;
    },
    priorityFilterValue() {
      return this.$store?.state?.filter_criteria?.priority || null;
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
<style scoped>
/* Compact Professional Filter Dashboard Styles */
.filter-dashboard-container {
  margin-bottom: 1.5rem;
  padding: 0;
}

.filter-card {
   background: linear-gradient(145deg, #23272f 0%, #2d3748 100%);
  border-radius: 12px;
  box-shadow: 
    0 2px 4px -1px rgba(0, 0, 0, 0.08),
    0 1px 2px -1px rgba(0, 0, 0, 0.04),
    0 0 0 1px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(226, 232, 240, 0.6);
  overflow: hidden;
  backdrop-filter: blur(4px);
}

.filter-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 1rem 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.filter-header-content {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.filter-icon-wrapper {
  width: 36px;
  height: 36px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(4px);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.filter-icon-wrapper i {
  color: white;
  font-size: 1rem;
}

.filter-header-text {
  flex: 1;
}

.filter-main-title {
  color: white;
  font-size: 1.125rem;
  font-weight: 600;
  margin: 0;
  letter-spacing: -0.025em;
}

.filter-subtitle {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.8rem;
  margin: 0.125rem 0 0 0;
  font-weight: 400;
}

.filter-content {
  padding: 0.25rem;
}

.filter-group-modern {
  margin-bottom: 1rem;
}

.filter-label-container {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}

.filter-label-icon {
  width: 28px;
  height: 28px;
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.filter-label-icon i {
  color: #64748b;
  font-size: 0.8rem;
}

.filter-label-text {
  flex: 1;
}

.filter-label-modern {
  display: block;
  font-size: 0.9rem;
  font-weight: 600;
  color: #ffffff;
  margin: 0;
  line-height: 1.25;
}

.filter-label-description {
  display: block;
  font-size: 0.7rem;
  color: #64748b;
  margin-top: 0.125rem;
  font-weight: 400;
}

.filter-buttons-container {
  margin-left: 2rem;
}

.btn-group-modern {
  display: flex;
  flex-wrap: wrap;
  gap: 0.375rem;
}

.btn-group-vertical-modern {
  flex-direction: column;
  align-items: stretch;
}

.btn-group-flex-status {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
  justify-content: flex-start;
}

.btn-group-flex-status .btn-modern {
  flex: 1 1 auto;
  min-width: 0;
  justify-content: flex-start;
}

.btn-group-flex-status .btn-text {
  text-align: left;
  flex: 1;
}

.btn-group-flex-priority {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
  justify-content: flex-start;
}

.btn-group-flex-priority .btn-modern {
  flex: 1;
  min-width: 0;
  justify-content: center;
  text-align: center;
}

.btn-group-flex-priority .btn-text {
  text-align: center;
  flex: none;
}

.btn-check-modern {
  display: none;
}

.btn-modern {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.5rem 0.75rem;
  font-size: 0.8rem;
  font-weight: 500;
  line-height: 1.25;
  border-radius: 8px;
  border: 2px solid transparent;
  background: #f8fafc;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
  position: relative;
  overflow: hidden;
  min-height: 36px;
}

.btn-modern-sm {
  padding: 0.375rem 0.5rem;
  font-size: 0.75rem;
  min-height: 32px;
}

.btn-modern:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}

.btn-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 14px;
  height: 14px;
  flex-shrink: 0;
}

.btn-text {
  flex: 1;
  text-align: left;
  white-space: nowrap;
}

.btn-badge {
  background: rgba(255, 255, 255, 0.9);
  color: #374151;
  padding: 0.125rem 0.25rem;
  border-radius: 4px;
  font-size: 0.7rem;
  font-weight: 600;
  min-width: 18px;
  text-align: center;
}

.priority-indicator {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
  margin-left: auto;
}

.priority-indicator.urgent {
  background: #ef4444;
  box-shadow: 0 0 6px rgba(239, 68, 68, 0.4);
}

.priority-indicator.high {
  background: #f59e0b;
  box-shadow: 0 0 6px rgba(245, 158, 11, 0.4);
}

.priority-indicator.normal {
  background: #06b6d4;
  box-shadow: 0 0 6px rgba(6, 182, 212, 0.4);
}

/* Button Variants */
.btn-modern-secondary {
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  border-color: #cbd5e1;
  color: #475569;
}

.btn-modern-primary {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  border-color: #93c5fd;
  color: #1d4ed8;
}

.btn-modern-info {
  background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);
  border-color: #67e8f9;
  color: #0e7490;
}

.btn-modern-success {
  background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
  border-color: #86efac;
  color: #15803d;
}

.btn-modern-danger {
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  border-color: #fca5a5;
  color: #dc2626;
}

.btn-modern-warning {
  background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
  border-color: #fcd34d;
  color: #d97706;
}

/* Active/Checked States */
.btn-check-modern:checked + .btn-modern-secondary {
  background: linear-gradient(135deg, #64748b 0%, #475569 100%);
  border-color: #334155;
  color: white;
  box-shadow: 0 2px 8px rgba(100, 116, 139, 0.3);
}

.btn-check-modern:checked + .btn-modern-primary {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  border-color: #1e40af;
  color: white;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.btn-check-modern:checked + .btn-modern-info {
  background: linear-gradient(135deg, #06b6d4 0%, #0e7490 100%);
  border-color: #0c4a6e;
  color: white;
  box-shadow: 0 2px 8px rgba(6, 182, 212, 0.3);
}

.btn-check-modern:checked + .btn-modern-success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-color: #047857;
  color: white;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.btn-check-modern:checked + .btn-modern-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  border-color: #b91c1c;
  color: white;
  box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

.btn-check-modern:checked + .btn-modern-warning {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  border-color: #b45309;
  color: white;
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.btn-check-modern:checked + .btn-modern .btn-badge {
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
  .filter-header {
    padding: 0.75rem 1rem;
  }
  
  .filter-content {
    padding: 1rem;
  }
  
  .filter-buttons-container {
    margin-left: 0;
  }
  
  .btn-group-modern {
    flex-direction: column;
  }
  
  .btn-group-vertical-modern {
    flex-direction: column;
  }
  
  .btn-group-flex-priority {
    flex-direction: column;
    gap: 0.375rem;
  }
  
  .btn-group-flex-priority .btn-modern {
    flex: none;
    justify-content: flex-start;
  }
  
  .btn-group-flex-priority .btn-text {
    text-align: left;
    flex: 1;
  }
  
  .btn-modern {
    justify-content: flex-start;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .btn-group-flex-priority {
    flex-wrap: wrap;
    gap: 0.25rem;
  }
  
  .btn-group-flex-priority .btn-modern {
    flex: 0 1 calc(50% - 0.125rem);
    min-width: 0;
  }
}

/* Animation for countdown icon */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.zmdi-time-countdown {
  animation: spin 2s linear infinite;
}

/* Switch Styles */
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
</style>
