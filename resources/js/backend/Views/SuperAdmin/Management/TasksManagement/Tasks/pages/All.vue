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
                <input
                  class="form-control"
                  @keyup="(e) => set_search_key(e)"
                  placeholder="Search"
                />
              </div>

              <!-- Sorting Button -->
              <div class="col-12 col-md-3 text-md-right text-sm-left">
                <button
                  class="btn btn-outline-success btn-sm"
                  @click="set_show_filter_canvas"
                >
                  <i class="fa fa-gear mx-2"></i>Filter
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div
              class="table-responsive table_responsive card_body_fixed_height"
            >
              <!-- Enhanced Professional Filter Section -->
              <div class="filter-section mb-4">
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
                            :class="{ 'active': !taskStatusFilterValue }"
                            @click="cleartaskStatusFilter"
                            type="button"
                          >
                            <i class="fa fa-list mr-1"></i>
                            <span class="d-none d-sm-inline">All Tasks</span>
                            <span class="d-sm-none">All</span>
                          </button>
                          <button
                            class="btn btn-filter btn-outline-primary"
                            :class="{ 'active': taskStatusFilterValue === 'Pending' }"
                            @click="taskStatusFilter('Pending')"
                            type="button"
                          >
                            <i class="fa fa-clock-o mr-1"></i>
                            Pending
                          </button>
                          <button
                            class="btn btn-filter btn-outline-info"
                            :class="{ 'active': taskStatusFilterValue === 'In Progress' }"
                            @click="taskStatusFilter('In Progress')"
                            type="button"
                          >
                            <i class="fa fa-spinner mr-1"></i>
                            <span class="d-none d-md-inline">In Progress</span>
                            <span class="d-md-none">Progress</span>
                          </button>
                          <button
                            class="btn btn-filter btn-outline-success"
                            :class="{ 'active': taskStatusFilterValue === 'Completed' }"
                            @click="taskStatusFilter('Completed')"
                            type="button"
                          >
                            <i class="fa fa-check mr-1"></i>
                            <span class="d-none d-sm-inline">Completed</span>
                            <span class="d-sm-none">Done</span>
                          </button>
                          <button
                            class="btn btn-filter btn-outline-danger"
                            :class="{ 'active': taskStatusFilterValue === 'Not Completed' }"
                            @click="taskStatusFilter('Not Completed')"
                            type="button"
                          >
                            <i class="fa fa-times mr-1"></i>
                            <span class="d-none d-md-inline">Not Completed</span>
                            <span class="d-md-none">Failed</span>
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
                            :class="{ 'active': !priorityFilterValue }"
                            @click="clearPriorityFilter"
                            type="button"
                          >
                            <i class="fa fa-filter mr-1"></i>
                            All
                          </button>
                          <button
                            class="btn btn-filter btn-outline-danger"
                            :class="{ 'active': priorityFilterValue === 'urgent' }"
                            @click="priorityFilter('urgent')"
                            type="button"
                          >
                            <i class="fa fa-exclamation-triangle mr-1"></i>
                            Urgent
                          </button>
                          <button
                            class="btn btn-filter btn-outline-warning"
                            :class="{ 'active': priorityFilterValue === 'high' }"
                            @click="priorityFilter('high')"
                            type="button"
                          >
                            <i class="fa fa-arrow-up mr-1"></i>
                            High
                          </button>
                          <button
                            class="btn btn-filter btn-outline-info"
                            :class="{ 'active': priorityFilterValue === 'normal' }"
                            @click="priorityFilter('normal')"
                            type="button"
                          >
                            <i class="fa fa-minus mr-1"></i>
                            Normal
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <table class="table table-hover text-center table-bordered">
                <thead>
                  <tr>
                    <th style="padding-left: 12px">
                      <i
                        class="zmdi zmdi-settings zmdi-hc-2x"
                        title="Actions"
                      ></i>
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
                    <th>Title</th>
                    <th>Project</th>
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
                  <tr
                    v-for="(item, index) in all?.data"
                    :key="item.id"
                    :class="`table_rows table_row_${item.id}`"
                  >
                    <td class="text-limit" :title="`Actions for ${item.title}`">
                      <span
                        class="icon"
                        @click.prevent="active_row($event)"
                      ></span>
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
                            <a
                              v-if="item.status == 'active'"
                              href=""
                              @click.prevent="updateStatus(item)"
                              class="border-warning"
                            >
                              <i class="fa fa-eye-slash text-warning"></i>
                              Inactive
                            </a>
                            <a
                              v-if="item.status == 'inactive'"
                              href=""
                              @click.prevent="updateStatus(item)"
                              class="border-warning"
                            >
                              <i class="fa fa-eye text-warning"></i>
                              Active
                            </a>
                          </li>
                          <li v-if="!is_trashed_data">
                            <a
                              @click.prevent="softDelete(item)"
                              href=""
                              class="border-danger"
                            >
                              <i class="fa fa-ban text-warning"></i>
                              Soft Delete
                            </a>
                          </li>
                          <li v-if="is_trashed_data">
                            <a
                              @click.prevent="restore_data(item)"
                              href=""
                              class="border-danger"
                            >
                              <i class="fa fa-refresh text-warning"></i>
                              Restore data
                            </a>
                          </li>
                          <li>
                            <a
                              @click.prevent="destroy_data(item)"
                              href=""
                              class="border-danger"
                            >
                              <i class="fa fa-trash text-danger"></i>
                              Destroy
                            </a>
                          </li>
                          <!-- Your existing list item -->
                          <li>
                            <a
                              href=""
                              @click.prevent="showOverviewModal(item)"
                              class="border-info"
                            >
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
                            <div
                              class="modal-dialog modal-dialog-centered"
                              role="document"
                            >
                              <div class="modal-content shadow">
                                <div class="modal-header bg-info text-white">
                                  <h5 class="modal-title">
                                    <i class="fa fa-tasks mr-2"></i>Update Task
                                    Status
                                  </h5>
                                  <button
                                    type="button"
                                    class="close text-white"
                                    @click="closeOverviewModal"
                                  >
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <form
                                  @submit.prevent="submitTaskOverviewForm(item)"
                                >
                                  <div class="modal-body">
                                    <div class="form-group">
                                      <label
                                        for="taskStatusSelect"
                                        class="font-weight-bold"
                                        >Task Status</label
                                      >
                                      <select
                                        id="taskStatusSelect"
                                        class="form-control"
                                        v-model="item.task_status"
                                        name="task_status"
                                      >
                                        <option disabled value="">
                                          Select Status
                                        </option>
                                        <option value="In Progress">
                                          In Progress
                                        </option>
                                        <option value="Completed">
                                          Completed
                                        </option>
                                        <option value="Not Completed">
                                          Not Completed
                                        </option>
                                      </select>
                                    </div>
                                    <div class="form-group">
                                      <label
                                        for="taskRating"
                                        class="font-weight-bold"
                                        >Task Rating</label
                                      >
                                      <div>
                                        <span
                                          v-for="n in 5"
                                          :key="'star-' + n"
                                          class="fa"
                                          :class="[
                                            'fa-star',
                                            n <= (item.rating || 0)
                                              ? 'text-warning'
                                              : 'text-secondary',
                                          ]"
                                          style="font-size: 1.3em"
                                        ></span>
                                      </div>
                                      <div
                                        class="d-flex align-items-center"
                                        style="gap: 10px"
                                      >
                                        <select
                                          id="taskRating"
                                          class="form-control"
                                          name="rating"
                                          v-model="item.rating"
                                        >
                                          <option disabled value="">
                                            Select Rating
                                          </option>
                                          <option
                                            v-for="n in 5"
                                            :key="n"
                                            :value="n"
                                          >
                                            {{ n }}
                                          </option>
                                        </select>
                                      </div>
                                    </div>
                                    <div
                                      v-if="
                                        item.task_status === 'Not Completed'
                                      "
                                      class="form-group"
                                    >
                                      <label
                                        for="nextDateInput"
                                        class="font-weight-bold"
                                        >Next Date</label
                                      >
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
                                    <button
                                      type="button"
                                      class="btn btn-secondary"
                                      @click="closeOverviewModal"
                                    >
                                      <i class="fa fa-times mr-1"></i>Close
                                    </button>
                                    <button
                                      v-if="
                                        selectedTask &&
                                        selectedTask.task_status !== 'Pending'
                                      "
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
                    <td class="text-limit" :title="`Select ${item.title}`">
                      <input
                        @change="set_item_selected(item, $event)"
                        :checked="isSelected(item)"
                        class="form-check-input ml-0"
                        type="checkbox"
                      />
                    </td>
                    <td class="text-limit" :title="`ID: ${index + 1}`">{{ index + 1 }}</td>
                    <td class="text-limit" :title="`Title: ${item.title}`">{{ item.title }}</td>
                    <td class="text-limit" :title="`Project: ${item.project_id?.name || 'N/A'}`">{{ item.project_id?.name }}</td>
                    <td class="text-limit" :title="`Assigned To: ${item.user?.name || 'Not Assigned'}`">
                      {{ item.user?.name || "Not Assigned" }}
                    </td>
                    <td class="text-limit" :title="`Priority: ${item.priority}`">{{ item.priority }}</td>
                    <td class="text-limit" :title="`User Status: ${item.task_user_status ?? 'N/A'}`">
                      {{ item.task_user_status ?? "N/A" }}
                    </td>
                    <td
                      class="text-limit"
                      :class="getTaskStatusClass(item.task_status)"
                      :title="`Task Status: ${item.task_status}`"
                    >
                      {{ item.task_status }}
                    </td>
                    <td class="text-limit" :title="`Rating: ${item.rating || 0}/5`">
                      <span>
                        <span
                          v-for="n in 5"
                          :key="'star-' + n"
                          class="fa"
                          :class="[
                            'fa-star',
                            n <= (item.rating || 0)
                              ? 'text-warning'
                              : 'text-secondary',
                          ]"
                          style="font-size: 1.3em"
                        ></span>
                        <span class="ml-2 font-weight-bold"
                          >({{ item.rating }})</span
                        >
                      </span>
                    </td>
                    <td class="text-limit" :title="`Actual Time: ${FindActualTime(item.start_date, item.end_date)}`">
                      {{ FindActualTime(item.start_date, item.end_date) }}
                    </td>
                    <td class="text-limit" :title="`Start Date: ${formatDateTime(item.start_date)}`">
                      {{ formatDateTime(item.start_date) }}
                    </td>
                    <td class="text-limit" :title="`End Date: ${formatDateTime(item.end_date)}`">
                      {{ formatDateTime(item.end_date) }}
                    </td>
                    <td class="text-limit" :title="`Created At: ${formatDateTime(item.created_at)}`">
                      {{ formatDateTime(item.created_at) }}
                    </td>

                    <!-- <td>
                      <img :src="item.image" alt="" height="50" width="50" />
                    </td> -->
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="mx-3">
            <nav
              aria-label=""
              class="d-flex gap-2 align-items-center"
              style="gap: 10px"
            >
              <ul class="pagination my-2" style="font-size: 11px">
                <template v-for="(link, index) in all?.links" :key="index">
                  <li class="page-item" :class="{ active: link.active }">
                    <a
                      class="page-link"
                      :class="
                        all?.current_page == all?.last_page &&
                        all?.links.length - 1 == index
                          ? 'disabled'
                          : ''
                      "
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
                <select
                  v-model="paginate"
                  @change="set_per_page_limit"
                  class="bg-transparent text-white rounded-1"
                >
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
                <a
                  href=""
                  @click.prevent="export_selected_csv(selected)"
                  class="btn action_btn btn-sm btn-secondary d-flex align-items-center"
                >
                  <i class="fa fa-sign-out mr-2"></i> Export ({{
                    this.selected?.length
                  }})
                </a>
              </div>
              <div class="mr-2 mb-2">
                <a
                  href=""
                  @click.prevent="import_csv_modal_show = true"
                  class="btn action_btn btn-sm btn-secondary d-flex align-items-center"
                >
                  <i class="fa fa-download mr-2"></i> Import
                </a>
              </div>
              <div class="mr-2 mb-2">
                <a
                  href=""
                  @click.prevent="change_status(`active`)"
                  class="btn action_btn btn-sm btn-success d-flex align-items-center"
                >
                  <i class="fa fa fa fa-eye mr-2"></i> Active ({{
                    active_data_count
                  }})
                </a>
              </div>
              <div class="mr-2 mb-2">
                <a
                  href=""
                  @click.prevent="change_status(`inactive`)"
                  class="btn action_btn btn-sm btn-warning d-flex align-items-center"
                >
                  <i class="fa fa fa-eye-slash mr-2"></i>
                  Inactive ({{ inactive_data_count }})
                </a>
              </div>
              <div class="mr-2 mb-2">
                <a
                  href=""
                  @click.prevent="change_status(`trased`)"
                  class="btn action_btn btn-sm btn-danger d-flex align-items-center"
                >
                  <i class="fa fa-trash mr-2"></i> Trased ({{
                    trased_data_count
                  }})
                </a>
              </div>

              <div class="mr-2 mb-2" v-if="this.selected?.length">
                <select
                  class="form-control"
                  style="width: 100px; height: 30px; font-size: 12px"
                  @change="bulkActions"
                >
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
    <div
      class="off_canvas data_filter"
      :class="`${show_filter_canvas ? 'active' : ''}`"
    >
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
            <label
              for="start_date"
              class="text-capitalize d-block date_custom_control"
            >
              <input
                v-model="start_date"
                type="date"
                id="start_date"
                name="start_date"
                class="form-control"
              />
              <!-- <div class="form-control preview"></div> -->
            </label>
          </div>
          <div class="filter_item">
            <label for="end_date">End Date</label>
            <label
              for="end_date"
              class="text-capitalize d-block date_custom_control"
            >
              <input
                v-model="end_date"
                type="date"
                id="end_date"
                name="end_date"
                class="form-control"
              />
              <!-- <div class="form-control preview"></div> -->
            </label>
          </div>

          <div class="filter_item">
            <label for="sort_by_col">Sort By Col</label
            ><label
              for="sort_by_col"
              class="text-capitalize d-block date_custom_control"
            >
              <select v-model="sort_by_col" class="form-control">
                <option v-for="col in sort_by_cols" :key="col">
                  {{ col }}
                </option>
              </select>
            </label>
          </div>
          <div class="filter_item">
            <label for="sort_by_col">Sort Type</label
            ><label
              for="sort_by_col"
              class="text-capitalize d-block date_custom_control"
            >
              <select v-model="sort_type" class="form-control">
                <option v-for="col in ['ASC', 'DESC']" :key="col">
                  {{ col }}
                </option>
              </select>
            </label>
          </div>
          <div
            class="filter_item d-flex justify-content-between align-items-center"
          >
            <button
              @click.prevent="get_all()"
              type="button"
              class="btn btn-sm btn-outline-info"
            >
              Submit
            </button>
            <button
              class="btn btn-outline-danger btn-sm"
              @click="reset_filters"
            >
              Reset
            </button>
          </div>
        </div>
      </div>
      <div class="off_canvas_overlay"></div>
    </div>
    <div
      class="modal fade"
      :class="`${import_csv_modal_show ? 'show d-block' : 'd-none'}`"
      id="primarymodal"
      aria-modal="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <form @submit.prevent="FileUploadHandler">
          <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
              <h5 class="modal-title text-white">Import {{ setup.prefix }}</h5>
              <button
                @click="import_csv_modal_show = false"
                type="button"
                class="close text-white"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="">
                <label for="">Upload file</label>
                <input type="file" name="file" class="form-control" required />
              </div>
              <p class="mt-3">
                Please check the sample CSV file below to ensure compatibility
                with the demo data import.
              </p>
              <a
                href=""
                @click.prevent="export_demo_csv"
                class="btn btn-sm btn-primary"
                >Download Demo CSV</a
              >
            </div>
            <div class="modal-footer">
              <button
                @click="import_csv_modal_show = false"
                type="button"
                class="btn btn-light"
                data-dismiss="modal"
              >
                <i class="fa fa-times"></i> Close
              </button>
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-download"></i> Import
              </button>
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
    isOverviewModalVisible: false,
    // selectedTask: null,
    // selectedStatus: "",
    filePath:
      "resources/js/backend/Views/SuperAdmin/Management/TestModule/helpers/demo.csv",
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

    /**
     * Reset all filters (date, status, priority, search, sort, pagination, etc.) and reload all tasks
     */

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
      let con = await window.s_confirm(
        "Are you sure want to " + action + " items ?"
      );
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

    /**
     * Reset all filters (date, status, priority, search, sort, pagination, etc.) and reload all tasks
     */

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
    FindActualTime(start_date, end_date) {
      if (!start_date || !end_date) return "N/A";
      const start = new Date(start_date);
      const end = new Date(end_date);
      const diff = end - start; // difference in milliseconds
      const hours = Math.floor(diff / 3600000); // convert to hours
      const minutes = Math.floor((diff % 3600000) / 60000); // convert to minutes
      return `${hours}(h) ${minutes}(m)`;
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
      "filter_criteria",
    ]),
    isAllSelected() {
      return (
        this.all?.data?.length > 0 &&
        this.all.data?.every((item) =>
          this.selected.some((s) => s.id === item.id)
        )
      );
    },
    // Computed properties for filter values
    taskStatusFilterValue() {
      return this.filter_criteria?.task_status || null;
    },
    priorityFilterValue() {
      return this.filter_criteria?.priority || null;
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
  },
};
</script>

<style scoped>
/* Enhanced Professional Filter Styles with Dark Background */
.filter-section {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
  border: 1px solid #34495e;
  color: #ffffff;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.filter-label {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
  color: #ecf0f1;
  font-weight: 600;
}

.filter-label i {
  font-size: 1.1rem;
}

.filter-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  align-items: center;
}

.btn-filter {
  position: relative;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 8px;
  transition: all 0.3s ease;
  border-width: 1.5px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  white-space: nowrap;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  background-color: rgba(255, 255, 255, 0.1);
  color: #ffffff;
}

.btn-filter:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
  background-color: rgba(255, 255, 255, 0.15);
}

.btn-filter.active {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
  font-weight: 600;
}

.btn-filter i {
  font-size: 0.875rem;
}

/* Specific button styles for dark theme */
.btn-outline-secondary {
  border-color: #95a5a6;
  color: #ecf0f1;
}

.btn-outline-secondary:hover {
  border-color: #bdc3c7;
  color: #ffffff;
}

.btn-outline-secondary.active {
  background-color: #6c757d;
  border-color: #6c757d;
  color: white;
  box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
}

.btn-outline-primary {
  border-color: #3498db;
  color: #3498db;
}

.btn-outline-primary:hover {
  border-color: #5dade2;
  color: #5dade2;
}

.btn-outline-primary.active {
  background-color: #007bff;
  border-color: #007bff;
  color: white;
  box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
}

.btn-outline-info {
  border-color: #1abc9c;
  color: #1abc9c;
}

.btn-outline-info:hover {
  border-color: #48c9b0;
  color: #48c9b0;
}

.btn-outline-info.active {
  background-color: #17a2b8;
  border-color: #17a2b8;
  color: white;
  box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
}

.btn-outline-success {
  border-color: #27ae60;
  color: #27ae60;
}

.btn-outline-success:hover {
  border-color: #58d68d;
  color: #58d68d;
}

.btn-outline-success.active {
  background-color: #28a745;
  border-color: #28a745;
  color: white;
  box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.btn-outline-danger {
  border-color: #e74c3c;
  color: #e74c3c;
}

.btn-outline-danger:hover {
  border-color: #ec7063;
  color: #ec7063;
}

.btn-outline-danger.active {
  background-color: #dc3545;
  border-color: #dc3545;
  color: white;
  box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

.btn-outline-warning {
  border-color: #f39c12;
  color: #f39c12;
}

.btn-outline-warning:hover {
  border-color: #f7dc6f;
  color: #f7dc6f;
}

.btn-outline-warning.active {
  background-color: #ffc107;
  border-color: #ffc107;
  color: #212529;
  box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
  .filter-section {
    padding: 1rem;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
  }
  
  .filter-group {
    gap: 0.5rem;
  }
  
  .filter-label {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
  }
  
  .filter-buttons {
    gap: 0.25rem;
  }
  
  .btn-filter {
    padding: 0.4rem 0.75rem;
    font-size: 0.8rem;
    border-radius: 6px;
  }
  
  .btn-filter i {
    font-size: 0.8rem;
  }
}

@media (max-width: 576px) {
  .filter-section {
    padding: 0.75rem;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  }
  
  .filter-buttons {
    gap: 0.2rem;
  }
  
  .btn-filter {
    padding: 0.35rem 0.6rem;
    font-size: 0.75rem;
    min-height: 32px;
  }
  
  .filter-label {
    font-size: 0.85rem;
  }
  
  .filter-label i {
    font-size: 1rem;
  }
}

/* Animation for smooth transitions */
@keyframes filterButtonPress {
  0% { transform: translateY(-2px) scale(1); }
  50% { transform: translateY(0) scale(0.98); }
  100% { transform: translateY(-2px) scale(1); }
}

.btn-filter:active {
  animation: filterButtonPress 0.2s ease;
}

/* Enhanced focus states for accessibility with dark theme */
.btn-filter:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.4);
}

/* Loading state for buttons with dark theme */
.btn-filter.loading {
  position: relative;
  color: transparent;
}

.btn-filter.loading::after {
  content: "";
  position: absolute;
  width: 16px;
  height: 16px;
  top: 50%;
  left: 50%;
  margin-left: -8px;
  margin-top: -8px;
  border: 2px solid #ecf0f1;
  border-radius: 50%;
  border-top-color: transparent;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Dark theme glow effects */
.btn-filter.active::before {
  content: '';
  position: absolute;
  top: -2px;
  left: -2px;
  right: -2px;
  bottom: -2px;
  background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  border-radius: 10px;
  z-index: -1;
  animation: glow 2s ease-in-out infinite alternate;
}

@keyframes glow {
  0% { opacity: 0.5; }
  100% { opacity: 1; }
}

/* Enhanced text contrast for dark background */
.filter-label .text-primary {
  color: #3498db !important;
}

.filter-label .text-warning {
  color: #f39c12 !important;
}
</style>
