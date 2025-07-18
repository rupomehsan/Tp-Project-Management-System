<template>
  <div class="google-sheets-container mb-5" :class="{ 'dark-mode': isDarkMode }">
    <!-- Header Toolbar -->
    <div class="sheets-toolbar">
      <div class="toolbar-left">
        <button class="btn-icon">
          <i class="fa fa-bars"></i>
        </button>
        <div class="file-info">
          <h2 class="sheet-title">
            Task Management Board
            <span v-if="selectedProject" class="project-filter-badge"> - {{ selectedProject.name }} </span>
          </h2>
          <small v-if="filteredTaskCount !== undefined" class="task-count-info">
            Showing {{ filteredTaskCount }} task{{ filteredTaskCount !== 1 ? "s" : "" }}
          </small>
        </div>
      </div>
      <div class="toolbar-right">
        <button class="btn-icon" @click="toggleDarkMode" :title="isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
          <i :class="isDarkMode ? 'fa fa-sun' : 'fa fa-moon'"></i>
        </button>
        <button class="btn-icon" @click="export_all_csv">
          <i class="fa fa-download"></i>
        </button>
        <button class="btn-icon">
          <i class="fa fa-share-alt"></i>
        </button>
        <button class="btn-icon">
          <i class="fa fa-comments"></i>
        </button>
        <div class="user-avatar">
          <img src="/avatar.png" alt="User" />
        </div>
      </div>
    </div>

    <!-- Formula Bar -->
    <!-- <div class="formula-bar">
      <div class="formula-bar-left">
        <div class="name-box">A1</div>
        <div class="formula-functions">
          <i class="fa fa-function"></i>
        </div>
      </div>
      <div class="formula-input">
        <input type="text" placeholder="Enter formula or data..." />
      </div>
    </div> -->

    <!-- Table Container -->
    <div class="table-container">
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead class="table-header">
            <tr>
              <th class="w-10" @dblclick="toggleEditMode">ID</th>
              <th>Task Title</th>
              <th>Priority</th>
              <th>Task Status</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Progress</th>
              <th>Rating</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(group, groupIndex) in groupedTasks" :key="'group-' + groupIndex">
              <!-- Group Header Row -->
              <tr class="group-header-row">
                <td class="group-toggle" @click="toggleGroup(group.id)">
                  <i :class="group.collapsed ? 'fa fa-chevron-right' : 'fa fa-chevron-down'"></i>
                </td>
                <td colspan="7" class="group-name-cell">
                  <div class="group-info">
                    <h5 class="group-title">
                      {{ group.name || "Ungrouped Tasks" }}
                      <span class="task-count">({{ group.tasks.length }} tasks)</span>
                    </h5>
                  </div>
                </td>
                <td class="group-actions">
                  <button
                    class="btn btn-sm btn-success add-group-task-btn"
                    @click="addTaskToGroup(group.id, group.name)"
                    title="Add task to this group"
                  >
                    <i class="fa fa-plus"></i>
                  </button>
                </td>
              </tr>

              <!-- Group Tasks -->
              <template v-if="!group.collapsed">
                <tr
                  v-for="task in group.tasks"
                  :key="task.id"
                  :class="`table_rows table_row_${task.id} ${editableRows[getTaskGlobalIndex(task)] ? 'editable-row' : ''} group-task-row`"
                  @click="handleRowClick(task)"
                  @dblclick="enableRowEdit(task)"
                >
                  <!-- ID -->
                  <td class="text-limit task-id-cell" :title="task.id">
                    {{ task.id }}
                    <i v-if="editableRows[getTaskGlobalIndex(task)]" class="fa fa-edit text-warning ml-1"></i>
                  </td>

                  <!-- Task Title -->
                  <td class="text-limit" :title="task.title">
                    <input
                      v-if="editableRows[getTaskGlobalIndex(task)]"
                      type="text"
                      v-model="task.title"
                      class="form-control form-control-sm"
                      @blur="immediateSaveTask(task)"
                      @keyup.enter="immediateSaveTask(task)"
                      @click.stop
                      ref="titleInput"
                    />
                    <span v-else @dblclick="enableTitleEdit(task)" class="editable-title">{{ task.title }}</span>
                  </td>

                  <!-- Priority -->
                  <td class="text-limit" :title="task.priority">
                    <select
                      v-if="editableRows[getTaskGlobalIndex(task)]"
                      v-model="task.priority"
                      class="form-control form-control-sm"
                      @change="debouncedSaveTask(task, 500)"
                      @click.stop
                    >
                      <option value="low">🟢 Low</option>
                      <option value="normal">🟡 Normal</option>
                      <option value="high">🔴 High</option>
                      <option value="urgent">⚫ Urgent</option>
                    </select>
                    <span v-else>
                      <span v-if="task.priority === 'low'">🟢 Low</span>
                      <span v-else-if="task.priority === 'normal'">🟡 Normal</span>
                      <span v-else-if="task.priority === 'high'">🔴 High</span>
                      <span v-else-if="task.priority === 'urgent'">⚫ Urgent</span>
                      <span v-else>{{ task.priority }}</span>
                    </span>
                  </td>

                  <!-- Task Status -->
                  <td class="text-limit" :class="getTaskStatusClass(task.task_user_status)" :title="task.task_user_status">
                    <select
                      v-if="editableRows[getTaskGlobalIndex(task)]"
                      v-model="task.task_user_status"
                      class="form-control form-control-sm"
                      @change="debouncedSaveTask(task, 500)"
                      @click.stop
                    >
                      <option value="Pending">📝 Pending</option>
                      <option value="In Progress">⚡ In Progress</option>
                      <option value="Completed">✅ Completed</option>
                      <option value="Not Completed">❌ Not Completed</option>
                    </select>
                    <span v-else>
                      <span v-if="task.task_user_status === 'Pending'">📝 Pending</span>
                      <span v-else-if="task.task_user_status === 'In Progress'">⚡ In Progress</span>
                      <span v-else-if="task.task_user_status === 'Completed'">✅ Completed</span>
                      <span v-else-if="task.task_user_status === 'Not Completed'">❌ Not Completed</span>
                      <span v-else>{{ task.task_user_status }}</span>
                    </span>
                  </td>

                  <!-- Start Date -->
                  <td class="text-limit" :title="formatDateTime(task.start_date)">
                    <input
                      v-if="editableRows[getTaskGlobalIndex(task)]"
                      type="date"
                      v-model="task.start_date"
                      class="form-control form-control-sm"
                      @change="debouncedSaveTask(task, 500)"
                      @click.stop
                    />
                    <span v-else>{{ formatDateTime(task.start_date) }}</span>
                  </td>

                  <!-- End Date -->
                  <td class="text-limit" :title="formatDateTime(task.end_date)">
                    <input
                      v-if="editableRows[getTaskGlobalIndex(task)]"
                      type="date"
                      v-model="task.end_date"
                      class="form-control form-control-sm"
                      @change="debouncedSaveTask(task, 500)"
                      @click.stop
                    />
                    <span v-else>{{ formatDateTime(task.end_date) }}</span>
                  </td>

                  <!-- Progress -->
                  <td class="text-limit" :title="`Progress: ${task.progress || 0}%`">
                    <div class="progress-cell" v-if="editableRows[getTaskGlobalIndex(task)]">
                      <input
                        type="range"
                        min="0"
                        max="100"
                        v-model="task.progress"
                        class="form-range progress-slider"
                        @input="updateProgress(task)"
                        @click.stop
                      />
                      <span class="progress-text ml-2">{{ task.progress || 0 }}%</span>
                    </div>
                    <div class="progress-cell" v-else>
                      <div class="progress progress-sm">
                        <div
                          class="progress-bar bg-success"
                          role="progressbar"
                          :style="{ width: (task.progress || 0) + '%' }"
                          :aria-valuenow="task.progress || 0"
                          aria-valuemin="0"
                          aria-valuemax="100"
                        ></div>
                      </div>
                      <span class="progress-text ml-2">{{ task.progress || 0 }}%</span>
                    </div>
                  </td>

                  <!-- Rating -->
                  <td class="text-limit" :title="`Rating: ${task.rating || 0}/5`">
                    <div class="rating-cell">
                      <span v-for="n in 5" :key="n" class="star readonly" :class="{ filled: n <= (task.rating || 0) }">
                        <i class="fa fa-star" :class="n <= (task.rating || 0) ? 'text-warning' : 'text-secondary'"></i>
                      </span>
                      <span class="ml-2">({{ task.rating || 0 }})</span>
                    </div>
                  </td>

                  <!-- Actions -->
                  <td class="text-limit actions-cell">
                    <div class="task-actions">
                      <!-- Success indicator -->
                      <span v-if="recentlyUpdated[task.id]" class="success-indicator" title="Task updated successfully">
                        <i class="fa fa-check-circle text-success"></i>
                      </span>

                      <button class="btn btn-sm btn-danger task-delete-btn" @click="removeTask(task)" title="Delete task">
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </template>
            </template>
          </tbody>
          <!-- Add Rows Section -->
        </table>
      </div>
    </div>

    <!-- Sheet Tabs -->
    <div class="sheet-tabs ">
      <div class="tab-controls ">
        <!-- Individual Project Tabs -->
        <template v-for="project in (projects.data || projects)" :key="project.id">
          <button
            class="project-tab-btn"
            :class="{ active: selectedProject && selectedProject.id === project.id }"
            @click="selectProject(project, $event)"
            :title="`Show tasks for ${project.name}`"
          >
            <i class="fa fa-folder"></i>
            <span>{{ project.name }}</span>
          </button>
        </template>

        <!-- Add Project Button -->
        <!-- <button class="add-project-btn" title="Add new project">
          <i class="fa fa-plus"></i>
          <span>Add Project</span>
        </button> -->
      </div>
    </div>
  </div>
</template>

<script>
/** plugins */
import { mapActions, mapWritableState } from "pinia";
import setup from "../setup";
import { store as data_store } from "../store";

import axios from "axios";

export default {
  data() {
    return {
      setup,
      all: [],
      isOverviewModalVisible: false,
      selectedCells: [],
      activeCell: null,
      isDarkMode: false, // Dark mode state
      rowsToAdd: 10, // Number of rows to add at once
      collapsedGroups: {}, // Track which groups are collapsed
      projects: [], // List of projects for dropdowns
      selectedProject: null, // Currently selected project for filtering

      // Resizing state
      isResizing: false,
      resizeType: null, // 'column' or 'row'
      resizeIndex: null,
      startX: 0,
      startY: 0,
      startWidth: 0,
      startHeight: 0,
      defaultRowHeight: "32px",
      rowHeights: {}, // Store custom row heights

      // Edit mode tracking
      editableRows: {}, // Track which rows are in edit mode
      saveTimeouts: {}, // Store save timeouts for debouncing
      recentlyUpdated: {}, // Track recently updated tasks for visual feedback

      // Google Sheets columns configuration
      columns: [
        { label: "ID", key: "id", width: "80px", minWidth: "50px" },
        { label: "Task Title", key: "title", width: "200px", minWidth: "100px" },
        { label: "Group", key: "task_group_name", width: "150px", minWidth: "100px" },
        { label: "Priority", key: "priority", width: "120px", minWidth: "80px" },
        { label: "Status", key: "task_user_status", width: "120px", minWidth: "80px" },
        { label: "Start Date", key: "start_date", width: "120px", minWidth: "100px" },
        { label: "End Date", key: "end_date", width: "120px", minWidth: "100px" },
        { label: "Progress", key: "progress", width: "100px", minWidth: "80px" },
        { label: "Rating", key: "rating", width: "120px", minWidth: "80px" },
      ],

      // Sample data for dropdowns
      task_groups: [
        { id: 1, name: "Development" },
        { id: 2, name: "Design" },
        { id: 3, name: "Testing" },
        { id: 4, name: "Research" },
      ],

      filePath: "resources/js/backend/Views/SuperAdmin/Management/TestModule/helpers/demo.csv",
    };
  },
  created: async function () {
    // First get projects and auto-select the first one
    await this.get_all_projects();
    // Then get tasks for the selected project
    await this.get_all_tasks();
    // Initialize dark mode from localStorage
    this.isDarkMode = localStorage.getItem("darkMode") === "true";
    this.applyDarkMode();
    // Add event listeners for resizing
    document.addEventListener("mousemove", this.handleMouseMove);
    document.addEventListener("mouseup", this.handleMouseUp);
    // Add event listener for Escape key to exit edit mode
    document.addEventListener("keydown", this.handleKeyDown);
    // Add event listener for clicking outside to exit edit mode
    document.addEventListener("click", this.handleOutsideClick);
  },

  mounted() {
    // Apply dark mode after component is mounted
    this.applyDarkMode();
  },

  beforeDestroy() {
    // Clean up event listeners
    document.removeEventListener("mousemove", this.handleMouseMove);
    document.removeEventListener("mouseup", this.handleMouseUp);
    document.removeEventListener("keydown", this.handleKeyDown);
    document.removeEventListener("click", this.handleOutsideClick);

    // Clean up save timeouts
    Object.values(this.saveTimeouts).forEach((timeout) => clearTimeout(timeout));
    this.saveTimeouts = {};
  },
  methods: {
    ...mapActions(data_store, [
      "get_all",
      "restore",
      "soft_delete",
      "update_status",
      "destroy",
      "bulk_action",
      "clear_selected",
      "import_data",
      "set_show_filter_canvas",
      "set_only_latest_data",
      "set_item",
      "set_filter_criteria",
      "set_page",
      "set_status",
      "set_paginate",
      "task_overview",
      "reset_filter_criteria",
    ]),

    get_all_tasks: async function () {
      const projectId = this.selectedProject?.id || "";
      console.log("Fetching tasks for project:", projectId);
      console.log("Selected project object:", this.selectedProject);

      let response = await axios.get(`task?get_all=1&project_id=${projectId}`);
      this.all = response.data;
      console.log("Tasks response:", response.data);
      console.log("Task count:", response.data?.data?.length || 0);

      // Log first few tasks to see structure
      if (response.data?.data?.length > 0) {
        console.log("Sample task structure:", response.data.data[0]);
        console.log(
          "Task project_ids:",
          response.data.data.map((task) => ({ id: task.id, project_id: task.project_id }))
        );
      }
    },

    get_all_projects: async function () {
      let response = await axios.get(`project?get_all=1`);
      this.projects = response.data;

      // Auto-select the first project
      const projectsList = this.projects.data || this.projects;
      if (projectsList && projectsList.length > 0) {
        this.selectedProject = projectsList[0];
        console.log("Auto-selected first project:", this.selectedProject);
      }
    },

    // Project selection methods
    selectProject(project, event) {
      this.selectedProject = project;
      console.log("Selected project:", project);

      // Add ripple effect
      if (event && event.target) {
        this.addRippleEffect(event.target);
      }

      // Refetch tasks for the selected project
      this.get_all_tasks();
    },

    // Add ripple effect to buttons
    addRippleEffect(element) {
      // Remove existing ripple
      element.classList.remove("ripple-effect");

      // Add ripple effect
      setTimeout(() => {
        element.classList.add("ripple-effect");
      }, 10);

      // Remove ripple after animation
      setTimeout(() => {
        element.classList.remove("ripple-effect");
      }, 600);
    },

    // Google Sheets specific methods
    selectCell(cellId) {
      this.selectedCells = [cellId];
      this.activeCell = cellId;
    },

    // Dark mode methods
    toggleDarkMode() {
      this.isDarkMode = !this.isDarkMode;
      localStorage.setItem("darkMode", this.isDarkMode.toString());
      this.applyDarkMode();
    },

    applyDarkMode() {
      const container = this.$el?.querySelector(".google-sheets-container");
      if (container) {
        if (this.isDarkMode) {
          container.classList.add("dark-mode");
        } else {
          container.classList.remove("dark-mode");
        }
      }
    },

    // Column resizing methods
    startColumnResize(event, columnIndex) {
      this.isResizing = true;
      this.resizeType = "column";
      this.resizeIndex = columnIndex;
      this.startX = event.clientX;
      this.startWidth = parseInt(this.columns[columnIndex].width);
      event.preventDefault();
    },

    // Row resizing methods
    startRowResize(event, rowIndex) {
      this.isResizing = true;
      this.resizeType = "row";
      this.resizeIndex = rowIndex;
      this.startY = event.clientY;
      this.startHeight = parseInt(this.rowHeights[rowIndex] || this.defaultRowHeight);
      event.preventDefault();
    },

    handleMouseMove(event) {
      if (!this.isResizing) return;

      if (this.resizeType === "column") {
        const deltaX = event.clientX - this.startX;
        const newWidth = Math.max(parseInt(this.columns[this.resizeIndex].minWidth) || 50, this.startWidth + deltaX);
        this.columns[this.resizeIndex].width = newWidth + "px";
      } else if (this.resizeType === "row") {
        const deltaY = event.clientY - this.startY;
        const newHeight = Math.max(32, this.startHeight + deltaY);
        this.rowHeights[this.resizeIndex] = newHeight + "px";
      }
    },

    handleMouseUp() {
      if (this.isResizing) {
        this.isResizing = false;
        this.resizeType = null;
        this.resizeIndex = null;
      }
    },

    // Handle keyboard events
    handleKeyDown(event) {
      if (event.key === "Escape") {
        // Exit all edit modes when Escape is pressed
        this.editableRows = {};
      }
    },

    // Handle clicking outside to exit edit mode
    handleOutsideClick(event) {
      // Check if click is outside the table or on a non-editable area
      const table = this.$el?.querySelector(".table");
      if (table && !table.contains(event.target)) {
        // Clicked outside the table, exit all edit modes
        this.editableRows = {};
      }
    },

    addNewRow() {
      // Add new task row logic
      const newTask = {
        id: Date.now(),
        title: "New Task",
        task_group_id: null,
        task_group: null,
        priority: "normal",
        task_user_status: "Pending",
        start_date: "",
        end_date: "",
        progress: 0,
        rating: 0,
      };

      if (this.all?.data) {
        this.all.data.push(newTask);
      }
    },

    async addMultipleRows() {
      const numberOfRows = parseInt(this.rowsToAdd) || 1;

      if (numberOfRows < 1 || numberOfRows > 100) {
        window.s_warning("Please enter a number between 1 and 100");
        return;
      }

      if (!this.all?.data) {
        this.all = { data: [] };
      }

      try {
        const promises = [];

        for (let i = 0; i < numberOfRows; i++) {
          const newTaskData = {
            title: `New Task ${i + 1}`,
            task_group_id: null,
            priority: "normal",
            task_user_status: "Pending",
            start_date: "",
            end_date: "",
            progress: 0,
            rating: 0,
            description: "",
          };

          promises.push(axios.post(`/task/store`, newTaskData));
        }

        // Wait for all tasks to be created
        const responses = await Promise.all(promises);

        // Add all successfully created tasks to local data
        responses.forEach((response) => {
          if (response.data.status === "success") {
            this.all.data.push(response.data.data);
          }
        });

        // window.s_alert(`Added ${numberOfRows} new rows successfully!`);

        // Scroll to bottom to show new rows
        this.$nextTick(() => {
          const tableContainer = this.$el.querySelector(".table-container");
          if (tableContainer) {
            tableContainer.scrollTop = tableContainer.scrollHeight;
          }
        });
      } catch (error) {
        console.error("Error creating tasks:", error);
        window.s_warning("Error creating some tasks");
      }
    },

    async addTaskToGroup(groupId, groupName) {
      if (!this.all?.data) {
        this.all = { data: [] };
      }

      const newTaskData = {
        title: `New Task`,
        task_group_id: groupId === "ungrouped" ? null : groupId,
        priority: "normal",
        task_user_status: "Pending",
        start_date: "",
        end_date: "",
        description: "",
      };

      try {
        // Call the API to store the task
        const response = await axios.post(`/task/store`, newTaskData);

        console.log("Task created response:", response.data);

        if (response.data.status === "success") {
          // Add the returned task data to local array
          const createdTask = response.data.data;
          this.all.data.push(createdTask);

          // Ensure the group is expanded to show the new task

          window.s_alert(`Added new task `);

          // Auto-edit the new task
          this.$nextTick(() => {
            const newTaskIndex = this.getTaskGlobalIndex(createdTask);
            if (newTaskIndex !== -1) {
              this.toggleRowEdit(newTaskIndex);
            }
          });
        }
      } catch (error) {
        console.error("Error creating task:", error);
        window.s_warning("Error creating task");
      }
    },

    async removeTask(task) {
      // const confirmed = await window.s_confirm("Are you sure you want to delete this task?");
      // if (!confirmed) return;

      try {
        // If task has a slug, delete from server
        if (task.slug) {
          const response = await axios.post(`/task/destroy/${task.slug}`);
          if (response.data.status === "success") {
            window.s_alert("Task deleted successfully!");
          } else {
            window.s_warning(response.data?.message || "Delete failed");
            return;
          }
        }

        // Remove from local data
        const taskIndex = this.all.data.findIndex((t) => t.id === task.id);
        if (taskIndex !== -1) {
          this.all.data.splice(taskIndex, 1);
        }

        // Clear edit mode for this task
        const globalIndex = this.getTaskGlobalIndex(task);
        if (globalIndex !== -1) {
          delete this.editableRows[globalIndex];
        }

        window.s_alert("Task removed successfully!");
      } catch (error) {
        console.error("Error deleting task:", error);
        window.s_warning("Error deleting task");
      }
    },

    // Helper methods for displaying data
    getTaskGroupName(groupId) {
      const group = this.task_groups.find((g) => g.id === groupId);
      return group ? group.name : "";
    },

    // Group management methods
    toggleGroup(groupId) {
      this.collapsedGroups[groupId] = !this.collapsedGroups[groupId];
    },

    getTaskGlobalIndex(task) {
      // Find the original index of the task in the flat array
      const allTasks = this.all?.data || [];
      return allTasks.findIndex((t) => t.id === task.id);
    },

    // Auto-save any currently edited row
    autoSaveCurrentEditedRow() {
      const editedIndices = Object.keys(this.editableRows).filter((index) => this.editableRows[index]);

      if (editedIndices.length > 0) {
        const allTasks = this.all?.data || [];
        editedIndices.forEach((index) => {
          const task = allTasks[index];
          if (task && task.slug) {
            // Clear any pending debounced saves
            const taskId = task.id;
            if (this.saveTimeouts[taskId]) {
              clearTimeout(this.saveTimeouts[taskId]);
              delete this.saveTimeouts[taskId];
            }
            // Save immediately
            this.saveTaskChanges(task, index);
          }
        });
      }
    },

    // Handle single click on row to disable edit mode on other rows
    handleRowClick(task) {
      const clickedIndex = this.getTaskGlobalIndex(task);

      // Check if any row is currently in edit mode
      const hasEditableRows = Object.values(this.editableRows).some((editable) => editable);

      if (hasEditableRows) {
        // Check if the clicked row is already editable
        const isClickedRowEditable = this.editableRows[clickedIndex];

        if (!isClickedRowEditable) {
          // Auto-save any currently edited row before disabling
          this.autoSaveCurrentEditedRow();

          // Disable edit mode for all rows
          this.editableRows = {};
        }
      }
    },

    // Edit mode methods
    enableRowEdit(task) {
      const index = this.getTaskGlobalIndex(task);
      if (index !== -1) {
        // Auto-save any currently edited row before switching
        this.autoSaveCurrentEditedRow();

        // Disable all other rows first
        this.editableRows = {};

        // Enable only the current row
        this.editableRows[index] = true;

        // Focus on the first input when entering edit mode
        this.$nextTick(() => {
          const row = document.querySelector(`.table_row_${task.id}`);
          const firstInput = row?.querySelector("input, select");
          if (firstInput) {
            firstInput.focus();
          }
        });
      }
    },

    enableTitleEdit(task) {
      const index = this.getTaskGlobalIndex(task);
      if (index !== -1) {
        // Auto-save any currently edited row before switching
        this.autoSaveCurrentEditedRow();

        // Disable all other rows first
        this.editableRows = {};

        // Enable only the current row
        this.editableRows[index] = true;

        // Focus specifically on the title input when double-clicking title
        this.$nextTick(() => {
          const row = document.querySelector(`.table_row_${task.id}`);
          const titleInput = row?.querySelector('input[type="text"]');
          if (titleInput) {
            titleInput.focus();
            titleInput.select(); // Select all text for easy editing
          }
        });
      }
    },

    toggleRowEdit(index) {
      // If the row is not currently editable, auto-save current row and disable all other rows first
      if (!this.editableRows[index]) {
        this.autoSaveCurrentEditedRow();
        this.editableRows = {};
      }

      this.editableRows[index] = !this.editableRows[index];

      if (this.editableRows[index]) {
        // Focus on the first input when entering edit mode
        this.$nextTick(() => {
          const firstInput = document.querySelector(`tr:nth-child(${index + 1}) input, tr:nth-child(${index + 1}) select`);
          if (firstInput) {
            firstInput.focus();
          }
        });
      }
    },

    toggleEditMode() {
      // Toggle edit mode for all rows
      const allEditable = Object.values(this.editableRows).every((val) => val);
      if (allEditable) {
        this.editableRows = {};
      } else {
        this.all?.data?.forEach((_, index) => {
          this.editableRows[index] = true;
        });
      }
    },

    updateProgress(task) {
      // Auto-save progress changes with debounce
      this.debouncedSaveTask(task, 500);
    },

    async saveTaskChanges(task, index) {
      if (!task.slug) {
        window.s_warning("Cannot save new task. Please create task first.");
        return;
      }

      try {
        const payload = {
          slug: task.slug,
          title: task.title,
          task_group_id: task.task_group_id,
          priority: task.priority,
          start_date: task.start_date,
          end_date: task.end_date,
        };

        const response = await axios.post(`/task/update/${task.slug}`, payload);
        if (response.data.status === "success") {
          // Show success indicator instead of toast
          this.showSuccessIndicator(task.id);
          this.editableRows[index] = false;
          await this.get_all();
        } else {
          window.s_warning(response.data?.message || "Update failed");
        }
      } catch (error) {
        console.error("Error saving task:", error);
        window.s_warning("Error saving task changes");
      }
    },

    // Show success indicator for a task
    showSuccessIndicator(taskId) {
      // Show the success indicator
      this.recentlyUpdated[taskId] = true;

      // Hide the indicator after 3 seconds
      setTimeout(() => {
        this.recentlyUpdated[taskId] = false;
      }, 3000);
    },

    // Check if item is selected (for checkbox functionality)
    isSelected(item) {
      return this.selected.some((selectedItem) => selectedItem.id === item.id);
    },

    // Set all items selected/unselected
    set_all_item_selected(event) {
      if (event.target.checked) {
        this.selected = [...(this.all?.data || [])];
      } else {
        this.selected = [];
      }
    },

    // Set individual item selected/unselected
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

    // Get task status CSS class
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
        default:
          return "";
      }
    },

    // Debounced save to avoid too many API calls
    debouncedSaveTask(task, delay = 1000) {
      const taskId = task.id;

      // Clear existing timeout for this task
      if (this.saveTimeouts[taskId]) {
        clearTimeout(this.saveTimeouts[taskId]);
      }

      // Set new timeout
      this.saveTimeouts[taskId] = setTimeout(() => {
        this.saveTaskChanges(task, this.getTaskGlobalIndex(task));
        delete this.saveTimeouts[taskId];
      }, delay);
    },

    // Immediate save for critical changes
    immediateSaveTask(task) {
      const taskId = task.id;

      // Clear any pending debounced save
      if (this.saveTimeouts[taskId]) {
        clearTimeout(this.saveTimeouts[taskId]);
        delete this.saveTimeouts[taskId];
      }

      // Save immediately
      this.saveTaskChanges(task, this.getTaskGlobalIndex(task));
    },

    async saveTaskChanges(task, index) {
      if (!task.slug) {
        window.s_warning("Cannot save new task. Please create task first.");
        return;
      }

      // Validate required fields
      if (!task.title || task.title.trim() === "") {
        window.s_warning("Task title is required");
        return;
      }

      try {
        const payload = {
          slug: task.slug,
          title: task.title.trim(),
          task_group_id: task.task_group_id || null,
          priority: task.priority || "normal",
          task_user_status: task.task_user_status || "Pending",
          start_date: task.start_date || null,
          end_date: task.end_date || null,
          progress: task.progress || 0,
        };

        const response = await axios.post(`/task/update/${task.slug}`, payload);
        if (response.data.status === "success") {
          // Show success indicator instead of toast
          this.showSuccessIndicator(task.id);
          // Keep the row in edit mode for continued editing
          // User can click elsewhere or press Escape to exit edit mode
        } else {
          // Handle validation errors
          if (response.data.errors) {
            const errorMessages = [];
            for (const [field, messages] of Object.entries(response.data.errors)) {
              errorMessages.push(`${field}: ${messages.join(", ")}`);
            }
            window.s_warning(`Validation errors: ${errorMessages.join("; ")}`);
          } else {
            window.s_warning(response.data?.message || "Update failed");
          }
        }
      } catch (error) {
        console.error("Error saving task:", error);

        // Handle Laravel validation errors
        if (error.response && error.response.data && error.response.data.errors) {
          const errorMessages = [];
          for (const [field, messages] of Object.entries(error.response.data.errors)) {
            errorMessages.push(`${field}: ${messages.join(", ")}`);
          }
          window.s_warning(`Validation errors: ${errorMessages.join("; ")}`);
        } else if (error.response && error.response.data && error.response.data.message) {
          window.s_warning(error.response.data.message);
        } else {
          window.s_warning("Error saving task changes");
        }
      }
    },

    // Exit edit mode when clicking outside or pressing Escape
    exitEditMode(task) {
      const index = this.getTaskGlobalIndex(task);
      if (index !== -1) {
        this.editableRows[index] = false;
      }
    },

    /**
     * Reset all filters (date, status, priority, search, sort, pagination, etc.) and reload all tasks
     */

    // taskStatusFilterValue
    showOverviewModal(item) {
      this.selectedTask = item; // keep a live reference
      this.selectedStatus = item.task_user_status;
      this.isOverviewModalVisible = true;
    },
    closeOverviewModal() {
      this.isOverviewModalVisible = false;
    },
    async updateTaskStatus(item) {
      try {
        // Ensure required fields are present
        if (!item.title || item.title.trim() === "") {
          window.s_warning("Task title is required");
          return;
        }

        let payload = {
          slug: item.slug,
          title: item.title.trim(),
          task_user_status: item.task_user_status,
          priority: item.priority,
          task_group_id: item.task_group_id,
          start_date: item.start_date,
          end_date: item.end_date,
        };

        let response = await axios.post(`/task/update/${item.slug}`, payload);
        if (response.data.status === "success") {
          // Show success indicator instead of toast
          this.showSuccessIndicator(item.id);
          // Don't reload all data, just keep the current state
        } else {
          // Handle validation errors
          if (response.data.errors) {
            const errorMessages = [];
            for (const [field, messages] of Object.entries(response.data.errors)) {
              errorMessages.push(`${field}: ${messages.join(", ")}`);
            }
            window.s_warning(`Validation errors: ${errorMessages.join("; ")}`);
          } else {
            window.s_warning(response.data?.message || "Update failed");
          }
        }
      } catch (error) {
        console.error("Error updating task status:", error);

        // Handle Laravel validation errors
        if (error.response && error.response.data && error.response.data.errors) {
          const errorMessages = [];
          for (const [field, messages] of Object.entries(error.response.data.errors)) {
            errorMessages.push(`${field}: ${messages.join(", ")}`);
          }
          window.s_warning(`Validation errors: ${errorMessages.join("; ")}`);
        } else if (error.response && error.response.data && error.response.data.message) {
          window.s_warning(error.response.data.message);
        } else {
          window.s_warning("Error updating task status");
        }
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
  },
  computed: {
    ...mapWritableState(data_store, [
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

    groupedTasks() {
      let tasks = this.all?.data || [];

      console.log("All tasks:", tasks);
      console.log("Selected project:", this.selectedProject);

      // Since we're filtering on the server, no need to filter here again
      // But if for some reason server filtering isn't working, we can keep this as backup
      // if (this.selectedProject) {
      //   tasks = tasks.filter(task => task.project_id === this.selectedProject.id);
      //   console.log('Filtered tasks for project:', tasks);
      // }

      const groups = {};

      // Group tasks by task_group_id
      tasks.forEach((task) => {
        const groupId = task.task_group_id || "ungrouped";
        // Use task_group_name from the task object directly, or fall back to task_group.name
        const groupName = task.task_group_name || task.task_group?.name || "Ungrouped Tasks";

        if (!groups[groupId]) {
          groups[groupId] = {
            id: groupId,
            name: groupName,
            tasks: [],
          };
        }
        groups[groupId].tasks.push(task);
      });

      // Convert to array format with group metadata
      const result = Object.values(groups)
        .map((group) => ({
          id: group.id,
          name: group.name,
          tasks: group.tasks,
          collapsed: this.collapsedGroups[group.id] || false,
        }))
        .sort((a, b) => {
          // Sort so "Ungrouped Tasks" comes last
          if (a.name === "Ungrouped Tasks") return 1;
          if (b.name === "Ungrouped Tasks") return -1;
          return a.name.localeCompare(b.name);
        });

      console.log("Grouped tasks result:", result);
      return result;
    },

    // Get filtered task count for display
    filteredTaskCount() {
      // Since we're filtering on the server, just return the total count
      return this.all?.data?.length || 0;
    },
  },
};
</script>

<style scoped>
/* Google Sheets Layout */
.google-sheets-container {
  font-family: "Roboto", Arial, sans-serif;
  background: #fff;
  height: 100vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transition: background-color 0.3s ease, color 0.3s ease;
}

/* Dark Mode Styles */
.google-sheets-container.dark-mode {
  background: #1a1a1a;
  color: #e0e0e0;
}

@media (prefers-color-scheme: dark) {
  .google-sheets-container {
    background: #1a1a1a;
    color: #e0e0e0;
  }
}

/* Toolbar */
.sheets-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 16px;
  border-bottom: 1px solid #e0e0e0;
  background: #fafafa;
  min-height: 56px;
  transition: background-color 0.3s ease, border-color 0.3s ease;
}

.google-sheets-container.dark-mode .sheets-toolbar {
  background: #2d2d2d;
  border-bottom-color: #404040;
}

@media (prefers-color-scheme: dark) {
  .sheets-toolbar {
    background: #2d2d2d;
    border-bottom-color: #404040;
  }
}

.toolbar-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.toolbar-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-icon {
  background: none;
  border: none;
  padding: 8px;
  border-radius: 4px;
  cursor: pointer;
  color: #5f6368;
  transition: background-color 0.2s;
}

.btn-icon:hover {
  background-color: #f1f3f4;
}

.google-sheets-container.dark-mode .btn-icon {
  color: #e0e0e0;
}

.google-sheets-container.dark-mode .btn-icon:hover {
  background-color: #404040;
}

@media (prefers-color-scheme: dark) {
  .btn-icon {
    color: #e0e0e0;
  }

  .btn-icon:hover {
    background-color: #404040;
  }
}

.file-info h2 {
  font-size: 18px;
  font-weight: 400;
  color: #202124;
  margin: 0;
  transition: color 0.3s ease;
}

.google-sheets-container.dark-mode .file-info h2 {
  color: #e0e0e0;
}

@media (prefers-color-scheme: dark) {
  .file-info h2 {
    color: #e0e0e0;
  }
}

.last-edit {
  font-size: 12px;
  color: #5f6368;
}

.user-avatar img {
  width: 32px;
  height: 32px;
  border-radius: 50%;
}

/* Project Filter Badge */
.project-filter-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #1a73e8;
  font-weight: 500;
}

.clear-filter-btn {
  background: none;
  border: none;
  color: #dc3545;
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 2px;
  font-size: 12px;
  transition: background-color 0.2s;
}

.clear-filter-btn:hover {
  background-color: rgba(220, 53, 69, 0.1);
}

.task-count-info {
  color: #6c757d;
  font-size: 12px;
  margin-top: 2px;
}

.google-sheets-container.dark-mode .project-filter-badge {
  color: #64b5f6;
}

.google-sheets-container.dark-mode .task-count-info {
  color: #adb5bd;
}

.google-sheets-container.dark-mode .clear-filter-btn {
  color: #ff6b6b;
}

.google-sheets-container.dark-mode .clear-filter-btn:hover {
  background-color: rgba(255, 107, 107, 0.1);
}

/* Professional Sheet Tabs Container */
.sheet-tabs {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  background: #ffffff;
  border-top: 1px solid #e9ecef;
  padding: 12px 20px;
  transition: all 0.3s ease;
  overflow-x: auto;
  white-space: nowrap;
  box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.1);
}

.google-sheets-container.dark-mode .sheet-tabs {
  background: #2d2d2d;
  border-top-color: #404040;
  box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.3);
}

.tab-controls {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: min-content;
}

.sheet-tab {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 20px;
  border-radius: 12px;
  background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
  border: 1px solid #e1e5e9;
  cursor: pointer;
  font-size: 13px;
  font-weight: 500;
  color: #495057;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  position: relative;
  white-space: nowrap;
  max-width: 240px;
  overflow: hidden;
  text-overflow: ellipsis;
  box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
  transform: translateY(0) scale(1);
  user-select: none;
}

.sheet-tab::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(145deg, rgba(26, 115, 232, 0.05) 0%, rgba(26, 115, 232, 0.02) 100%);
  border-radius: 12px;
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 0;
}

.sheet-tab > * {
  position: relative;
  z-index: 1;
}

.sheet-tab:hover {
  background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 100%);
  color: #1a73e8;
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 8px 25px rgba(26, 115, 232, 0.15), 0 3px 10px rgba(0, 0, 0, 0.08);
  border-color: #c8d6e5;
}

.sheet-tab:hover::before {
  opacity: 1;
}

.sheet-tab:active {
  transform: translateY(-1px) scale(0.98);
  transition: all 0.15s ease;
}

.sheet-tab.active {
  background: linear-gradient(145deg, #1a73e8 0%, #1557b0 100%);
  color: #ffffff;
  border-color: #1a73e8;
  box-shadow: 0 6px 25px rgba(26, 115, 232, 0.35), 0 2px 10px rgba(26, 115, 232, 0.2);
  transform: translateY(-4px) scale(1.05);
  font-weight: 600;
}

.sheet-tab.active::before {
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
  opacity: 1;
}

.sheet-tab.active::after {
  content: "";
  position: absolute;
  top: -2px;
  left: -2px;
  right: -2px;
  bottom: -2px;
  background: linear-gradient(145deg, rgba(26, 115, 232, 0.2) 0%, rgba(21, 87, 176, 0.15) 100%);
  border-radius: 14px;
  z-index: -1;
  filter: blur(8px);
}

.project-tab {
  min-width: 140px;
  letter-spacing: 0.4px;
  text-transform: capitalize;
}

.project-tab i {
  font-size: 13px;
  opacity: 0.8;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  margin-right: 4px;
  filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

.project-tab:hover i {
  opacity: 1;
  transform: scale(1.15) rotate(5deg);
  filter: drop-shadow(0 2px 4px rgba(26, 115, 232, 0.3));
}

.project-tab.active i {
  opacity: 1;
  color: #ffffff;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
  transform: scale(1.1);
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.google-sheets-container.dark-mode .sheet-tab {
  background: linear-gradient(145deg, #2a2a2a 0%, #1f1f1f 100%);
  border-color: #404040;
  color: #e0e0e0;
  box-shadow: 0 3px 12px rgba(0, 0, 0, 0.4), 0 1px 3px rgba(0, 0, 0, 0.6);
}

.google-sheets-container.dark-mode .sheet-tab::before {
  background: linear-gradient(145deg, rgba(100, 181, 246, 0.08) 0%, rgba(100, 181, 246, 0.04) 100%);
}

.google-sheets-container.dark-mode .sheet-tab:hover {
  background: linear-gradient(145deg, #333333 0%, #262626 100%);
  color: #64b5f6;
  border-color: #555;
  box-shadow: 0 8px 25px rgba(100, 181, 246, 0.2), 0 3px 10px rgba(0, 0, 0, 0.4);
}

.google-sheets-container.dark-mode .sheet-tab.active {
  background: linear-gradient(145deg, #1976d2 0%, #1565c0 100%);
  border-color: #1976d2;
  box-shadow: 0 6px 25px rgba(25, 118, 210, 0.4), 0 2px 10px rgba(25, 118, 210, 0.3);
  color: #ffffff;
}

.google-sheets-container.dark-mode .sheet-tab.active::before {
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
}

.google-sheets-container.dark-mode .sheet-tab.active::after {
  background: linear-gradient(145deg, rgba(25, 118, 210, 0.25) 0%, rgba(21, 101, 192, 0.2) 100%);
  filter: blur(10px);
}

/* Professional Project Tab Buttons */
.project-tab-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: #ffffff;
  border: 1px solid #e1e5e9;
  border-radius: 6px;
  color: #495057;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
  min-height: 40px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.project-tab-btn:hover {
  background: #f8f9fa;
  border-color: #1a73e8;
  color: #1a73e8;
  transform: translateY(-1px);
  box-shadow: 0 2px 6px rgba(26, 115, 232, 0.15);
}

.project-tab-btn.active {
  background: #1a73e8;
  border-color: #1a73e8;
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(26, 115, 232, 0.3);
}

.project-tab-btn:active {
  transform: translateY(0);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.project-tab-btn i {
  font-size: 13px;
  opacity: 0.8;
}

.project-tab-btn:hover i,
.project-tab-btn.active i {
  opacity: 1;
}

.project-tab-btn span {
  font-weight: 500;
  letter-spacing: 0.02em;
}

/* Dark Mode Styles */
.google-sheets-container.dark-mode .project-tab-btn {
  background: #2a2a2a;
  border-color: #404040;
  color: #e0e0e0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.google-sheets-container.dark-mode .project-tab-btn:hover {
  background: #333333;
  border-color: #64b5f6;
  color: #64b5f6;
  box-shadow: 0 2px 6px rgba(100, 181, 246, 0.2);
}

.google-sheets-container.dark-mode .project-tab-btn.active {
  background: #1976d2;
  border-color: #1976d2;
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(25, 118, 210, 0.4);
}

.project-tab-btn:focus,
.project-tab-btn:focus-visible {
  outline: 2px solid #2196f3;
  outline-offset: 2px;
}

.google-sheets-container.dark-mode .project-tab-btn:focus,
.google-sheets-container.dark-mode .project-tab-btn:focus-visible {
  outline: 2px solid #42a5f5;
  outline-offset: 2px;
}

/* Add Project Button */
.add-project-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 14px;
  background: transparent;
  border: 1px dashed #ced4da;
  border-radius: 6px;
  color: #6c757d;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
  min-height: 40px;
  margin-left: 8px;
}

.add-project-btn:hover {
  background: #f8f9fa;
  border-color: #28a745;
  color: #28a745;
  border-style: solid;
}

.google-sheets-container.dark-mode .add-project-btn {
  border-color: #555;
  color: #adb5bd;
}

.google-sheets-container.dark-mode .add-project-btn:hover {
  background: #333333;
  border-color: #28a745;
  color: #28a745;
}

/* Simple scrollbar for tabs */
.sheet-tabs::-webkit-scrollbar {
  height: 4px;
}

.sheet-tabs::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 2px;
}

.sheet-tabs::-webkit-scrollbar-thumb {
  background: #ced4da;
  border-radius: 2px;
  transition: background 0.2s ease;
}

.sheet-tabs::-webkit-scrollbar-thumb:hover {
  background: #adb5bd;
}

.google-sheets-container.dark-mode .sheet-tabs::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

.google-sheets-container.dark-mode .sheet-tabs::-webkit-scrollbar-thumb {
  background: #555;
}

.google-sheets-container.dark-mode .sheet-tabs::-webkit-scrollbar-thumb:hover {
  background: #666;
}

/* Focus states for accessibility */
.sheet-tab:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.3);
}

.google-sheets-container.dark-mode .sheet-tab:focus {
  box-shadow: 0 0 0 3px rgba(100, 181, 246, 0.4);
}

/* Button press animation */
@keyframes buttonPress {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(0.95);
  }
  100% {
    transform: scale(1);
  }
}

.sheet-tab:active {
  animation: buttonPress 0.15s ease-in-out;
}

/* Ripple effect for clicks */
@keyframes ripple {
  0% {
    transform: translate(-50%, -50%) scale(0);
    opacity: 0.6;
  }
  100% {
    transform: translate(-50%, -50%) scale(4);
    opacity: 0;
  }
}

.sheet-tab.ripple-effect::after {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 10px;
  height: 10px;
  background: rgba(26, 115, 232, 0.3);
  border-radius: 50%;
  animation: ripple 0.6s ease-out;
  pointer-events: none;
  z-index: 2;
}

.google-sheets-container.dark-mode .sheet-tab.ripple-effect::after {
  background: rgba(100, 181, 246, 0.4);
}

/* Menu Bar */
.sheets-menubar {
  background: #fff;
  border-bottom: 1px solid #e0e0e0;
  padding: 0 16px;
  height: 48px;
  display: flex;
  align-items: center;
}

.menu-items {
  display: flex;
  gap: 24px;
}

.menu-item {
  font-size: 14px;
  color: #202124;
  cursor: pointer;
  padding: 8px 0;
  transition: color 0.2s;
}

.menu-item:hover {
  color: #1a73e8;
}

/* Formula Bar */
.formula-bar {
  display: flex;
  border-bottom: 1px solid #e0e0e0;
  background: #fff;
  height: 32px;
}

.formula-bar-left {
  display: flex;
  align-items: center;
  border-right: 1px solid #e0e0e0;
}

.name-box {
  padding: 0 12px;
  font-size: 12px;
  color: #202124;
  border-right: 1px solid #e0e0e0;
  height: 100%;
  display: flex;
  align-items: center;
  min-width: 60px;
  background: #f8f9fa;
}

.formula-functions {
  padding: 0 8px;
  display: flex;
  align-items: center;
  color: #5f6368;
}

.formula-input {
  flex: 1;
}

.formula-input input {
  width: 100%;
  border: none;
  padding: 8px 12px;
  font-size: 14px;
  outline: none;
}

/* Table Container */
.table-container {
  flex: 1;
  overflow: auto;
  padding: 20px;
  background: #fff;
  transition: background-color 0.3s ease;
}

.table-responsive {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.google-sheets-container.dark-mode .table-container {
  background: #1a1a1a;
}

.google-sheets-container.dark-mode .table-responsive {
  background: #2d2d2d;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

@media (prefers-color-scheme: dark) {
  .table-container {
    background: #1a1a1a;
  }

  .table-responsive {
    background: #2d2d2d;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  }
}

/* Table Styling */
.table {
  margin-bottom: 0;
  font-size: 14px;
  transition: color 0.3s ease;
}

.table-header th {
  background: #f8f9fa;
  border-bottom: 2px solid #e9ecef;
  font-weight: 600;
  color: #495057;
  padding: 15px 8px;
  vertical-align: middle;
  transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

.google-sheets-container.dark-mode .table {
  color: #e0e0e0;
}

.google-sheets-container.dark-mode .table-header th {
  background: #404040;
  border-bottom-color: #555;
  color: #e0e0e0;
}

.google-sheets-container.dark-mode .table-bordered {
  border-color: #555;
}

.google-sheets-container.dark-mode .table-bordered th,
.google-sheets-container.dark-mode .table-bordered td {
  border-color: #555;
}

@media (prefers-color-scheme: dark) {
  .table {
    color: #e0e0e0;
  }

  .table-header th {
    background: #404040;
    border-bottom-color: #555;
    color: #e0e0e0;
  }

  .table-bordered {
    border-color: #555;
  }

  .table-bordered th,
  .table-bordered td {
    border-color: #555;
  }
}

.table-header th:first-child {
  border-radius: 8px 0 0 0;
}

.table-header th:last-child {
  border-radius: 0 8px 0 0;
}

/* Row Styling */
.table_rows {
  transition: all 0.2s ease;
}

.table_rows:hover {
  background-color: #f8f9fa;
}

.table_rows.active {
  background-color: #e3f2fd;
}

.editable-row {
  background-color: #fff3cd !important;
  border-left: 4px solid #ffc107;
  box-shadow: inset 0 0 0 1px rgba(255, 193, 7, 0.3);
  transition: all 0.3s ease;
}

.editable-row:hover {
  background-color: #ffeaa7 !important;
  box-shadow: inset 0 0 0 1px rgba(255, 193, 7, 0.5);
}

.editable-row td {
  position: relative;
}

.editable-row td:not(.actions-cell):not(.group-toggle):hover {
  background-color: rgba(255, 193, 7, 0.1);
  cursor: text;
}

/* Show edit indicator */
.editable-row td:not(.actions-cell):not(.group-toggle)::before {
  content: "";
  position: absolute;
  top: 2px;
  right: 2px;
  width: 8px;
  height: 8px;
  background: #ffc107;
  border-radius: 50%;
  opacity: 0.7;
  z-index: 1;
}

/* Group Styling */
.group-header-row {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  cursor: pointer;
  transition: all 0.3s ease;
}

.group-header-row:hover {
  background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.group-toggle {
  width: 40px;
  text-align: center;
  padding: 12px 8px;
  vertical-align: middle;
}

.group-toggle i {
  font-size: 14px;
  transition: transform 0.3s ease;
}

.group-name-cell {
  padding: 12px 16px;
  vertical-align: middle;
}

.group-info {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.group-title {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
}

.task-count {
  font-size: 12px;
  font-weight: 400;
  opacity: 0.9;
  background: rgba(255, 255, 255, 0.2);
  padding: 2px 8px;
  border-radius: 12px;
}

.group-actions {
  text-align: center;
  padding: 8px;
  vertical-align: middle;
  width: 60px;
}

.add-group-task-btn {
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: #fff;
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 12px;
  transition: all 0.2s ease;
  cursor: pointer;
}

.add-group-task-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.add-group-task-btn:active {
  transform: translateY(0);
}

.add-group-task-btn i {
  font-size: 10px;
}

.group-task-row {
  border-left: 3px solid #667eea;
}

.group-task-spacer {
  position: relative;
  color: #6c757d;
  font-weight: 500;
}

.task-group-indicator {
  font-family: monospace;
  font-size: 12px;
  color: #adb5bd;
}

.task-id-cell {
  font-weight: 600;
  color: #495057;
}

/* Dark Mode Group Styles */
.google-sheets-container.dark-mode .group-header-row {
  background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
  color: #e0e0e0;
}

.google-sheets-container.dark-mode .group-header-row:hover {
  background: linear-gradient(135deg, #38495a 0%, #1a202c 100%);
}

.google-sheets-container.dark-mode .add-group-task-btn {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.2);
  color: #e0e0e0;
}

.google-sheets-container.dark-mode .add-group-task-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.3);
}

.google-sheets-container.dark-mode .group-task-row {
  border-left-color: #4a5568;
  background-color: #2d2d2d;
}

.google-sheets-container.dark-mode .task-id-cell {
  color: #e0e0e0;
}

.google-sheets-container.dark-mode .task-group-indicator {
  color: #6c757d;
}

@media (prefers-color-scheme: dark) {
  .table_rows {
    background-color: #2d2d2d;
    color: #e0e0e0;
  }

  .table_rows:hover {
    background-color: #404040;
  }

  .table_rows.active {
    background-color: #1e3a5f;
  }

  .editable-row {
    background-color: #4a3d00 !important;
    border-left-color: #ffc107;
    box-shadow: inset 0 0 0 1px rgba(255, 193, 7, 0.3);
  }

  .editable-row:hover {
    background-color: #5c4a00 !important;
    box-shadow: inset 0 0 0 1px rgba(255, 193, 7, 0.5);
  }

  .editable-row td:not(.actions-cell):not(.group-toggle):hover {
    background-color: rgba(255, 193, 7, 0.2);
  }

  .editable-row td:not(.actions-cell):not(.group-toggle)::before {
    background: #ffc107;
  }

  .group-header-row {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    color: #e0e0e0;
  }

  .group-header-row:hover {
    background: linear-gradient(135deg, #38495a 0%, #1a202c 100%);
  }

  .add-group-task-btn {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    color: #e0e0e0;
  }

  .add-group-task-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
  }

  .group-task-row {
    border-left-color: #4a5568;
    background-color: #2d2d2d;
  }

  .task-id-cell {
    color: #e0e0e0;
  }

  .task-group-indicator {
    color: #6c757d;
  }
}

/* Cell Content */
.text-limit {
  max-width: 150px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  padding: 12px 8px;
  vertical-align: middle;
  transition: color 0.3s ease;
}

/* Editable title styling */
.editable-title {
  cursor: default;
  transition: all 0.2s ease;
  padding: 2px 4px;
  border-radius: 3px;
  display: inline-block;
  min-width: 100%;
}

.editable-title:hover {
  background-color: rgba(0, 123, 255, 0.05);
  color: #007bff;
  cursor: pointer;
}

@media (prefers-color-scheme: dark) {
  .text-limit {
    color: #e0e0e0;
  }

  .editable-title:hover {
    background-color: rgba(13, 110, 253, 0.1);
    color: #64b5f6;
    cursor: pointer;
  }
}

/* Form Controls in Table */
.form-control-sm {
  font-size: 13px;
  padding: 4px 8px;
  border-radius: 4px;
  border: 1px solid #ced4da;
  transition: border-color 0.3s ease, background-color 0.3s ease, color 0.3s ease;
}

.form-control-sm:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

@media (prefers-color-scheme: dark) {
  .form-control-sm {
    background-color: #404040;
    border-color: #555;
    color: #e0e0e0;
  }

  .form-control-sm:focus {
    border-color: #0d6efd;
    background-color: #2d2d2d;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  }
}

/* Progress Bar */
.progress-cell {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 120px;
}

.progress-sm {
  height: 0.5rem;
  flex: 1;
}

.progress-text {
  font-size: 12px;
  color: #6c757d;
  min-width: 35px;
}

/* Progress Slider for Edit Mode */
.progress-slider {
  flex: 1;
  height: 6px;
  border-radius: 3px;
  background: #e0e0e0;
  outline: none;
  cursor: pointer;
}

.progress-slider::-webkit-slider-thumb {
  appearance: none;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #007bff;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.progress-slider::-webkit-slider-thumb:hover {
  background: #0056b3;
}

.progress-slider::-moz-range-thumb {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #007bff;
  cursor: pointer;
  border: none;
}

@media (prefers-color-scheme: dark) {
  .progress-slider {
    background: #555;
  }

  .progress-slider::-webkit-slider-thumb {
    background: #0d6efd;
  }

  .progress-slider::-webkit-slider-thumb:hover {
    background: #0b5ed7;
  }

  .progress-slider::-moz-range-thumb {
    background: #0d6efd;
  }
}

/* Rating Stars */
.rating-cell {
  display: flex;
  align-items: center;
  gap: 2px;
}

.star {
  cursor: default;
  transition: color 0.2s ease;
}

.star.readonly {
  cursor: default;
  pointer-events: none;
}

.star.readonly .fa-star {
  opacity: 0.8;
}

/* Actions Column */
.actions-cell {
  width: 80px;
  text-align: center;
  padding: 8px;
}

.task-actions {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 4px;
}

.success-indicator {
  display: inline-flex;
  align-items: center;
  animation: fadeInOut 3s ease-in-out;
  font-size: 14px;
}

.success-indicator i {
  transition: all 0.3s ease;
}

@keyframes fadeInOut {
  0% {
    opacity: 0;
    transform: scale(0.8);
  }
  20% {
    opacity: 1;
    transform: scale(1.1);
  }
  30% {
    opacity: 1;
    transform: scale(1);
  }
  90% {
    opacity: 1;
    transform: scale(1);
  }
  100% {
    opacity: 0;
    transform: scale(0.8);
  }
}

.task-delete-btn {
  background: #dc3545;
  border: 1px solid #dc3545;
  color: #fff;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  transition: all 0.2s ease;
  cursor: pointer;
}

.task-delete-btn:hover {
  background: #c82333;
  border-color: #bd2130;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
}

.task-delete-btn:active {
  transform: translateY(0);
}

.task-delete-btn i {
  font-size: 10px;
}

/* Action Buttons */
.table_action_btns {
  position: absolute;
  top: 50%;
  left: 15px;
  transform: translateY(-50%);
  background: #fff;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  display: none;
  min-width: 120px;
  transition: background-color 0.3s ease, border-color 0.3s ease;
}

.table_rows.active .table_action_btns {
  display: block;
}

.table_action_btns ul {
  list-style: none;
  margin: 0;
  padding: 8px 0;
}

.table_action_btns li {
  margin: 0;
}

.table_action_btns a {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  color: #495057;
  text-decoration: none;
  border-left: 3px solid transparent;
  transition: all 0.2s ease;
  font-size: 13px;
}

.table_action_btns a:hover {
  background-color: #f8f9fa;
  border-left-color: #007bff;
  color: #007bff;
}

.table_action_btns a i {
  margin-right: 8px;
  width: 14px;
}

@media (prefers-color-scheme: dark) {
  .table_action_btns {
    background: #2d2d2d;
    border-color: #555;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
  }

  .table_action_btns a {
    color: #e0e0e0;
  }

  .table_action_btns a:hover {
    background-color: #404040;
    color: #0d6efd;
    border-left-color: #0d6efd;
  }
}

/* Icon for row activation */
.icon {
  width: 20px;
  height: 20px;
  background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 8l3 3l6-6'/%3e%3c/svg%3e")
    no-repeat center center;
  background-size: 12px;
  cursor: pointer;
  border-radius: 3px;
  transition: background-color 0.2s ease;
}

.icon:hover {
  background-color: #f8f9fa;
}

@media (prefers-color-scheme: dark) {
  .icon {
    background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23e0e0e0' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 8l3 3l6-6'/%3e%3c/svg%3e")
      no-repeat center center;
    background-size: 12px;
  }

  .icon:hover {
    background-color: #404040;
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .table-container {
    padding: 10px;
  }

  .text-limit {
    max-width: 100px;
    font-size: 12px;
  }

  .table-header th {
    padding: 10px 5px;
    font-size: 12px;
  }
}

/* Column Headers */
.column-headers {
  display: flex;
  position: sticky;
  top: 0;
  background: #f8f9fa;
  border-bottom: 1px solid #e0e0e0;
  z-index: 2;
}

.corner-cell {
  width: 40px;
  height: 32px;
  border-right: 1px solid #e0e0e0;
  background: #f8f9fa;
  flex-shrink: 0;
}

.column-header {
  min-width: 50px;
  height: 32px;
  border-right: 1px solid #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;
  font-weight: 500;
  color: #202124;
  background: #f8f9fa;
  cursor: pointer;
  position: relative;
  padding: 0 8px;
  flex-shrink: 0;
}

.column-header:hover {
  background: #e8f0fe;
}

.column-label {
  flex: 1;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.column-resizer {
  position: absolute;
  right: -3px;
  top: 0;
  width: 6px;
  height: 100%;
  cursor: col-resize;
  background: transparent;
  z-index: 3;
}

.column-resizer:hover {
  background: #1a73e8;
}

.column-resizer:active {
  background: #1a73e8;
}

/* Data Grid */
.data-grid {
  position: relative;
}

.grid-row {
  display: flex;
  border-bottom: 1px solid #e0e0e0;
}

.row-header {
  width: 40px;
  min-height: 32px;
  border-right: 1px solid #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  color: #5f6368;
  background: #f8f9fa;
  cursor: pointer;
  position: sticky;
  left: 0;
  z-index: 1;
  flex-shrink: 0;
  position: relative;
}

.row-header:hover {
  background: #e8f0fe;
}

.row-number {
  flex: 1;
  text-align: center;
}

.row-resizer {
  position: absolute;
  bottom: -3px;
  left: 0;
  width: 100%;
  height: 6px;
  cursor: row-resize;
  background: transparent;
  z-index: 3;
}

.row-resizer:hover {
  background: #1a73e8;
}

.row-resizer:active {
  background: #1a73e8;
}

.add-row-btn {
  background: #fff;
  color: #1a73e8;
  font-size: 16px;
  font-weight: bold;
}

.add-row-btn:hover {
  background: #e8f0fe;
}

.grid-cell {
  min-width: 50px;
  min-height: 32px;
  border-right: 1px solid #e0e0e0;
  padding: 4px 8px;
  display: flex;
  align-items: center;
  cursor: cell;
  position: relative;
  background: #fff;
  flex-shrink: 0;
  box-sizing: border-box;
}

.grid-cell:hover {
  background: #f8f9fa;
}

.grid-cell.selected {
  background: #e8f0fe;
  border: 2px solid #1a73e8;
}

/* Prevent text selection during resize */
.sheets-workspace.resizing {
  user-select: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
}

.sheets-workspace.resizing * {
  pointer-events: none;
}

.sheets-workspace.resizing .column-resizer,
.sheets-workspace.resizing .row-resizer {
  pointer-events: all;
}

.cell-content {
  font-size: 14px;
  color: #202124;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 100%;
}

.cell-input,
.cell-select {
  width: 100%;
  border: none;
  background: transparent;
  font-size: 14px;
  color: #202124;
  outline: none;
  padding: 0;
}

.cell-select {
  cursor: pointer;
}

.date-input {
  cursor: pointer;
}

/* Priority and Status Styling */
.priority-select option.priority-low {
  color: #34a853;
}

.priority-select option.priority-normal {
  color: #fbbc04;
}

.priority-select option.priority-high {
  color: #ea4335;
}

.priority-select option.priority-urgent {
  color: #000;
}

.status-select option {
  padding: 4px 8px;
}

/* Progress Cell */
.progress-cell {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
}

.progress-bar {
  flex: 1;
  height: 6px;
  background: #e0e0e0;
  border-radius: 3px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #34a853 0%, #fbbc04 50%, #ea4335 100%);
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 12px;
  color: #5f6368;
  min-width: 30px;
}

/* Rating Cell */
.rating-cell {
  display: flex;
  gap: 2px;
}

.star {
  cursor: pointer;
  font-size: 16px;
  transition: opacity 0.2s;
  filter: grayscale(100%);
}

.star.filled {
  filter: grayscale(0%);
}

.star:hover {
  opacity: 0.7;
}

/* Add Row Section */
.add-row-section {
  background: #f8f9fa;
  border-top: 2px solid #e9ecef;
}

.add-row-section:hover {
  background: #e9ecef;
}

.add-controls {
  padding: 15px 12px;
  vertical-align: middle;
}

.add-row-btn {
  background: #007bff;
  border: none;
  border-radius: 6px;
  padding: 8px 16px;
  color: #fff;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 6px;
}

.add-row-btn:hover {
  background: #0056b3;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
}

.add-row-btn:active {
  transform: translateY(0);
}

.add-input-cell {
  padding: 10px 8px;
  vertical-align: middle;
}

.add-rows-input {
  width: 80px;
  text-align: center;
  font-weight: 500;
}

.add-placeholder {
  padding: 15px 12px;
  vertical-align: middle;
  font-style: italic;
  color: #6c757d;
}

.add-placeholder .text-muted {
  font-size: 12px;
}

/* Dark Mode Styles for Add Row Section */
.google-sheets-container.dark-mode .add-row-section {
  background: #404040;
  border-top-color: #555;
}

.google-sheets-container.dark-mode .add-row-section:hover {
  background: #4a4a4a;
}

.google-sheets-container.dark-mode .add-row-btn {
  background: #0d6efd;
  color: #fff;
}

.google-sheets-container.dark-mode .add-row-btn:hover {
  background: #0b5ed7;
}

.google-sheets-container.dark-mode .add-rows-input {
  background: #2d2d2d;
  border-color: #555;
  color: #e0e0e0;
}

.google-sheets-container.dark-mode .add-placeholder {
  color: #adb5bd;
}

.google-sheets-container.dark-mode .editable-row {
  background-color: #4a3d00 !important;
  border-left-color: #ffc107;
  box-shadow: inset 0 0 0 1px rgba(255, 193, 7, 0.3);
}

.google-sheets-container.dark-mode .editable-row:hover {
  background-color: #5c4a00 !important;
  box-shadow: inset 0 0 0 1px rgba(255, 193, 7, 0.5);
}

.google-sheets-container.dark-mode .editable-row td:not(.actions-cell):not(.group-toggle):hover {
  background-color: rgba(255, 193, 7, 0.2);
}

.google-sheets-container.dark-mode .editable-row td:not(.actions-cell):not(.group-toggle)::before {
  background: #ffc107;
}

.google-sheets-container.dark-mode .progress-slider {
  background: #555;
}

.google-sheets-container.dark-mode .progress-slider::-webkit-slider-thumb {
  background: #0d6efd;
}

.google-sheets-container.dark-mode .progress-slider::-webkit-slider-thumb:hover {
  background: #0b5ed7;
}

.google-sheets-container.dark-mode .progress-slider::-moz-range-thumb {
  background: #0d6efd;
}

.google-sheets-container.dark-mode .editable-title:hover {
  background-color: rgba(13, 110, 253, 0.2);
  color: #64b5f6;
}

@media (prefers-color-scheme: dark) {
  .add-row-section {
    background: #404040;
    border-top-color: #555;
  }

  .add-row-section:hover {
    background: #4a4a4a;
  }

  .add-row-btn {
    background: #0d6efd;
    color: #fff;
  }

  .add-row-btn:hover {
    background: #0b5ed7;
  }

  .add-rows-input {
    background: #2d2d2d;
    border-color: #555;
    color: #e0e0e0;
  }

  .add-placeholder {
    color: #adb5bd;
  }

  .task-delete-btn {
    background: #dc3545;
    border-color: #dc3545;
    color: #fff;
  }

  .task-delete-btn:hover {
    background: #c82333;
    border-color: #bd2130;
  }
}

.add-cell {
  background: #fafafa;
  border-style: dashed;
}

.add-placeholder {
  font-size: 12px;
  color: #9aa0a6;
  font-style: italic;
}

/* Sheet Tabs */
.sheet-tabs {
  display: flex;
  align-items: center;
  background: #fff;
  border-top: 1px solid #e0e0e0;
  height: 55px;
  padding: 0 16px;
  transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tab-controls {
  margin-right: 16px;
}

.tab-btn {
  background: none;
  border: none;
  padding: 8px;
  border-radius: 4px;
  cursor: pointer;
  color: #5f6368;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.tab-btn:hover {
  background: #f1f3f4;
}

.tabs {
  display: flex;
  gap: 4px;
}

.sheet-tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 4px 4px 0 0;
  cursor: pointer;
  font-size: 14px;
  color: #5f6368;
  transition: all 0.2s;
}

.sheet-tab:hover {
  background: #f1f3f4;
}

.sheet-tab.active {
  background: #e8f0fe;
  color: #1a73e8;
  border-bottom: 2px solid #1a73e8;
}

@media (prefers-color-scheme: dark) {
  .sheet-tabs {
    background: #2d2d2d;
    border-top-color: #404040;
  }

  .tab-btn {
    color: #e0e0e0;
  }

  .tab-btn:hover {
    background: #404040;
  }

  .sheet-tab {
    color: #e0e0e0;
  }

  .sheet-tab:hover {
    background: #404040;
  }

  .sheet-tab.active {
    background: #1e3a5f;
    color: #64b5f6;
    border-bottom-color: #64b5f6;
  }
}

/* Scrollbar Styling */
.sheets-workspace::-webkit-scrollbar {
  width: 12px;
  height: 12px;
}

.sheets-workspace::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.sheets-workspace::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 6px;
}

.sheets-workspace::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Responsive Design */
@media (max-width: 768px) {
  .sheets-toolbar {
    padding: 8px;
  }

  .toolbar-left .file-info h2 {
    font-size: 16px;
  }

  .menu-items {
    gap: 16px;
  }

  .column-header,
  .grid-cell {
    min-width: 100px;
  }
}
</style>
