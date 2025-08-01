<template>
  <div
    class="google-sheets-container mb-5"
    :class="{ 'dark-mode': isDarkMode }"
  >
    <!-- Header Toolbar -->
    <div class="sheets-toolbar">
      <div class="toolbar-left">
        <button class="btn-icon">
          <i class="fa fa-bars"></i>
        </button>
        <div class="file-info">
          <h2 class="sheet-title">
            Task Management Board
            <span v-if="selectedProject" class="project-filter-badge">
              - {{ selectedProject.name }}
            </span>
          </h2>
          <small v-if="filteredTaskCount !== undefined" class="task-count-info">
            Showing {{ filteredTaskCount }} task{{
              filteredTaskCount !== 1 ? "s" : ""
            }}
          </small>
        </div>
      </div>
      <div class="toolbar-right">
        <input
          type="date"
          class="form-control date-picker"
          v-model="selectedDate"
          @change="filterTasksByDate"
          :title="
            selectedDate
              ? `Filtering tasks for ${formatSelectedDate}`
              : 'Select date to filter tasks'
          "
        />
        <button
          v-if="selectedDate"
          class="btn btn-outline-secondary border btn-clear-date"
          @click="clearDateFilter"
          title="Clear date filter"
        >
          <i class="fa fa-times"></i>
        </button>
        <button
          class="btn btn-warning btn-refresh"
          @click="refreshComponent"
          title="Refresh Tasks and Groups"
        >
          <i class="fa fa-refresh"></i>
          <span>Refresh</span>
        </button>
        <button
          class="btn btn-success w-100 btn-add-group"
          @click="showAddTaskGroupModal"
          title="Add New Task Group"
        >
          <i class="fa fa-plus"></i>
          <span>Add Group</span>
        </button>
        <button
          class="btn btn-primary btn-add-task"
          @click="showAddTaskModal"
          title="Add New Task"
        >
          <i class="fa fa-plus"></i>
          <span>Add Task</span>
        </button>
        <!-- Debug button for testing drag and drop -->

        <!-- <button
          class="btn-icon"
          @click="toggleDarkMode"
          :title="isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
        >
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
        </div> -->
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
    <div class="table-container p-2">
      <!-- Sheet Tabs -->
      <div class="sheet-tabs mb-2 border-rounded">
        <div class="tab-controls">
          <!-- Individual Project Tabs -->
          <template
            v-for="project in projects.data || projects"
            :key="project.id"
          >
            <button
              class="project-tab-btn"
              :class="{
                active: selectedProject && selectedProject.id === project.id,
              }"
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

      <!-- Debug info for group ordering -->
      <!-- <div
        v-if="groupedTasks.length > 1"
        class="debug-info mb-2 p-2 bg-light border rounded"
      >
        <small class="text-muted">
          <strong>Debug:</strong>
          Groups:
          {{ groupedTasks.map((g) => `${g.name}(${g.id})`).join(" → ") }} |
          Custom Order:
          {{
            customGroupOrder.length > 0 ? customGroupOrder.join(" → ") : "None"
          }}
          | Length: {{ groupedTasks.length }}
        </small>
        <div class="mt-2">
          <button
            @click="testGroupReorder"
            class="btn btn-sm btn-outline-primary me-2"
            title="Test moving first group to second position"
          >
            Test Reorder
          </button>
          <button
            @click="resetGroupOrder"
            class="btn btn-sm btn-outline-warning"
            title="Reset group order to default alphabetical"
          >
            Reset Order
          </button>
        </div>
      </div> -->

      <!-- Sorting indicator -->
      <div
        v-if="sortField"
        class="sort-indicator mb-2 p-2 bg-info text-white border rounded d-flex align-items-center justify-content-between"
      >
        <small>
          <i class="fa fa-sort me-1"></i>
          <strong>Sorted by:</strong>
          {{ getSortFieldDisplayName(sortField) }}
          ({{ sortDirection === "asc" ? "A-Z" : "Z-A" }})
        </small>
        <button
          @click="clearSort"
          class="btn btn-sm btn-outline-light"
          title="Clear sorting"
        >
          <i class="fa fa-times"></i> Clear Sort
        </button>
      </div>

      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead class="table-header">
            <tr>
              <th class="w-10" @dblclick="toggleEditMode">ID</th>
              <th
                class="sortable-header"
                @click="sortBy('title')"
                :class="{ sorted: isSorted('title') }"
                title="Click to sort by Task Title"
              >
                Task Title
                <i class="fa ms-1" :class="getSortIcon('title')"></i>
              </th>
              <th
                class="sortable-header"
                @click="sortBy('priority')"
                :class="{ sorted: isSorted('priority') }"
                title="Click to sort by Priority"
              >
                Priority
                <i class="fa ms-1" :class="getSortIcon('priority')"></i>
              </th>
              <th
                class="sortable-header"
                @click="sortBy('task_user_status')"
                :class="{ sorted: isSorted('task_user_status') }"
                title="Click to sort by Dev Status"
              >
                Dev Status
                <i class="fa ms-1" :class="getSortIcon('task_user_status')"></i>
              </th>
              <th
                class="sortable-header"
                @click="sortBy('task_status')"
                :class="{ sorted: isSorted('task_status') }"
                title="Click to sort by Task Status"
              >
                Task Status
                <i class="fa ms-1" :class="getSortIcon('task_status')"></i>
              </th>
              <th
                class="sortable-header"
                @click="sortBy('start_date')"
                :class="{ sorted: isSorted('start_date') }"
                title="Click to sort by Start Date"
              >
                Start Date
                <i class="fa ms-1" :class="getSortIcon('start_date')"></i>
              </th>

              <th
                class="sortable-header"
                @click="sortBy('end_date')"
                :class="{ sorted: isSorted('end_date') }"
                title="Click to sort by End Date"
              >
                End Date
                <i class="fa ms-1" :class="getSortIcon('end_date')"></i>
              </th>
              <th
                class="sortable-header"
                @click="sortBy('actual_time')"
                :class="{ sorted: isSorted('actual_time') }"
                title="Click to sort by Actual Time"
              >
                Actual Time
                <i class="fa ms-1" :class="getSortIcon('actual_time')"></i>
              </th>
              <th>Rating</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <template
              v-for="(group, groupIndex) in groupedTasks"
              :key="'group-' + groupIndex"
            >
              <!-- Group Header Row -->
              <tr
                class="group-header-row"
                :class="{
                  dragging: isDragging && draggedGroupIndex === groupIndex,
                  'drag-over': dragOverGroupIndex === groupIndex,
                }"
                @dragover="onGroupDragOver($event, groupIndex)"
                @dragleave="onGroupDragLeave"
                @drop="onGroupDrop($event, groupIndex)"
              >
                <td class="group-toggle" @click="toggleGroup(group.id)">
                  <i
                    :class="
                      group.collapsed
                        ? 'fa fa-chevron-right'
                        : 'fa fa-chevron-down'
                    "
                  ></i>
                </td>
                <td colspan="8" class="group-name-cell">
                  <div class="group-info">
                    <!-- Always show drag handle when there are multiple groups -->
                    <div
                      v-if="groupedTasks.length > 1"
                      class="drag-handle"
                      title="Drag to reorder groups"
                      draggable="true"
                      @dragstart="onGroupDragStart($event, groupIndex)"
                      @dragend="onGroupDragEnd"
                      @click.stop="
                        () =>
                          console.log(
                            'Drag handle clicked for group:',
                            groupIndex
                          )
                      "
                      @mousedown="
                        () =>
                          console.log(
                            'Drag handle mousedown for group:',
                            groupIndex
                          )
                      "
                    >
                      <i class="fa fa-grip-vertical"></i>
                    </div>
                    <h5 class="group-title">
                      {{ group.name || "Ungrouped Tasks" }}
                      <span class="task-count"
                        >({{ group.tasks.length }} tasks)</span
                      >
                      <small
                        class="drag-instruction"
                        v-if="groupedTasks.length > 1"
                      >
                        <i class="fa fa-arrows-v"></i>
                      </small>
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
                  :class="`table_rows table_row_${task.id} ${
                    editableRows[getTaskGlobalIndex(task)] ? 'editable-row' : ''
                  } group-task-row`"
                  @click="handleRowClick(task)"
                  @dblclick="enableRowEdit(task)"
                >
                  <!-- ID -->
                  <td class="text-limit task-id-cell" :title="task.id">
                    {{ task.id }}
                    <i
                      v-if="editableRows[getTaskGlobalIndex(task)]"
                      class="fa fa-edit text-warning ml-1"
                    ></i>
                  </td>

                  <!-- Task Title -->
                  <td class="text-limit" :title="task.title">
                    <div class="task-title-container">
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
                      <div v-else class="task-title-display">
                        <span
                          @dblclick="enableTitleEdit(task)"
                          class="editable-title"
                          :class="{ 'task-assigned': isTaskAssigned(task) }"
                        >
                          {{ task.title }}
                        </span>
                        <div
                          v-if="isTaskAssigned(task)"
                          class="assignment-info"
                          :title="getAssignmentTooltip(task)"
                        >
                          <i class="fa fa-user-tag assignment-icon"></i>
                          <span class="assignment-text">
                            {{ getAssignmentText(task) }}
                          </span>
                        </div>
                      </div>
                    </div>
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
                      <option value="low">⚫ Low</option>
                      <option value="normal">🟢 Normal</option>
                      <option value="high">🟡 High</option>
                      <option value="urgent">🔴 Urgent</option>
                    </select>
                    <span v-else>
                      <span v-if="task.priority === 'low'">⚫ Low</span>
                      <span v-else-if="task.priority === 'normal'"
                        >🟢 Normal</span
                      >
                      <span v-else-if="task.priority === 'high'">🟡 High</span>
                      <span v-else-if="task.priority === 'urgent'"
                        >🔴 Urgent</span
                      >
                      <span v-else>{{ task.priority }}</span>
                    </span>
                  </td>

                  <!-- Task Status -->
                  <td
                    class="text-limit"
                    :class="getTaskStatusClass(task.task_user_status)"
                    :title="task.task_user_status"
                  >
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
                      <span v-if="task.task_user_status === 'Pending'"
                        >📝 Pending</span
                      >
                      <span v-else-if="task.task_user_status === 'In Progress'"
                        >⚡ In Progress</span
                      >
                      <span v-else-if="task.task_user_status === 'Completed'"
                        >✅ Completed</span
                      >
                      <span
                        v-else-if="task.task_user_status === 'Not Completed'"
                        >❌ Not Completed</span
                      >
                      <span v-else>{{ task.task_user_status }}</span>
                    </span>
                  </td>
                  <td>
                    {{ task.task_status }}
                  </td>

                  <!-- Start Date -->
                  <td
                    class="text-limit"
                    :title="formatDateTime(task.start_date)"
                  >
                    <input
                      v-if="editableRows[getTaskGlobalIndex(task)]"
                      type="datetime-local"
                      v-model="task.start_date"
                      :min="todayDate + 'T00:00'"
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
                      type="datetime-local"
                      v-model="task.end_date"
                      :min="
                        (task.start_date || todayDate) +
                        (task.start_date ? '' : 'T00:00')
                      "
                      class="form-control form-control-sm"
                      @change="debouncedSaveTask(task, 500)"
                      @click.stop
                    />
                    <span v-else>{{ formatDateTime(task.end_date) }}</span>
                  </td>

                  <td>
                    {{ FindActualTime(task.start_date, task.end_date) }}
                  </td>

                  <!-- Rating -->
                  <td
                    class="text-limit"
                    :title="`Rating: ${task.rating || 0}/5`"
                  >
                    <div class="rating-cell">
                      <span
                        v-for="n in 5"
                        :key="n"
                        class="star readonly"
                        :class="{ filled: n <= (task.rating || 0) }"
                      >
                        <i
                          class="fa fa-star"
                          :class="
                            n <= (task.rating || 0)
                              ? 'text-warning'
                              : 'text-secondary'
                          "
                        ></i>
                      </span>
                      <span class="ml-2">({{ task.rating || 0 }})</span>
                    </div>
                  </td>

                  <!-- Actions -->
                  <td class="text-limit actions-cell">
                    <div class="task-actions">
                      <!-- Success indicator -->
                      <span
                        v-if="recentlyUpdated[task.id]"
                        class="success-indicator"
                        title="Task updated successfully"
                      >
                        <i class="fa fa-check-circle text-success"></i>
                      </span>

                      <!-- Description button -->
                      <button
                        class="btn btn-sm btn-info task-description-btn"
                        @click="openDescriptionModal(task)"
                        title="View/Edit Description"
                      >
                        <i class="fa fa-file-text"></i>
                      </button>

                      <button
                        class="btn btn-sm task-delete-btn"
                        :class="{
                          'btn-danger': canRemoveTask(task),
                          'btn-secondary': !canRemoveTask(task),
                        }"
                        :disabled="!canRemoveTask(task)"
                        @click="removeTask(task)"
                        :title="
                          !canRemoveTask(task)
                            ? `Cannot delete - Task status is '${task.task_status}'. Only 'Pending' tasks can be deleted.`
                            : 'Delete task'
                        "
                      >
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

    <!-- Add Task Modal -->
    <div
      v-if="showAddTaskModalFlag"
      class="modal-overlay"
      :class="{ 'dark-mode': isDarkMode }"
      @click="closeAddTaskModal"
    >
      <div
        class="modal-content"
        :class="{ 'dark-mode': isDarkMode }"
        @click.stop
      >
        <div class="modal-header">
          <h3>Add New Task</h3>
          <button class="modal-close-btn" @click="closeAddTaskModal">
            <i class="fa fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createNewTask">
            <div class="form-group">
              <label for="taskTitle">Task Title *</label>
              <input
                type="text"
                id="taskTitle"
                v-model="newTask.title"
                class="form-control"
                placeholder="Enter task title"
                required
              />
            </div>

            <div class="form-group">
              <label for="taskGroup">Task Group</label>
              <select
                id="taskGroup"
                v-model="newTask.task_group_id"
                class="form-control"
              >
                <option value="">Select Task Group (Optional)</option>
                <option
                  v-for="group in task_groups"
                  :key="group.id"
                  :value="group.id"
                >
                  {{ group.name }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label for="taskPriority">Priority</label>
              <select
                id="taskPriority"
                v-model="newTask.priority"
                class="form-control"
              >
                <option value="low">🟢 Low</option>
                <option value="normal">🟡 Normal</option>
                <option value="high">🔴 High</option>
                <option value="urgent">⚫ Urgent</option>
              </select>
            </div>

            <div class="form-group">
              <label for="taskStatus">Status</label>
              <select
                id="taskStatus"
                v-model="newTask.task_user_status"
                class="form-control"
              >
                <option value="Pending">📝 Pending</option>
                <option value="In Progress">⚡ In Progress</option>
                <option value="Completed">✅ Completed</option>
                <option value="Not Completed">❌ Not Completed</option>
              </select>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="startDate">Start Date & Time</label>
                <input
                  type="datetime-local"
                  id="startDate"
                  v-model="newTask.start_date"
                  class="form-control text-dark"
                />
              </div>
              <div class="form-group">
                <label for="endDate">End Date & Time</label>
                <input
                  type="datetime-local"
                  id="endDate"
                  v-model="newTask.end_date"
                  :min="newTask.start_date"
                  class="form-control text-dark"
                />
              </div>
            </div>

            <!-- Date validation error message -->
            <div
              v-if="isNewTaskDateInvalid"
              class="alert alert-danger"
              style="
                margin-top: 10px;
                padding: 8px 12px;
                border-radius: 4px;
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
              "
            >
              <i class="fa fa-exclamation-triangle"></i>
              {{ dateValidationMessage }}
            </div>

            <div class="form-group">
              <label for="taskDescription">Description</label>
              <textarea
                id="taskDescription"
                v-model="newTask.description"
                class="form-control"
                rows="3"
                placeholder="Enter task description (optional)"
              ></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            @click="closeAddTaskModal"
          >
            Cancel
          </button>
          <button
            type="button"
            class="btn btn-primary"
            @click="createNewTask"
            :disabled="!newTask.title || isCreatingTask || isNewTaskDateInvalid"
          >
            <i v-if="isCreatingTask" class="fa fa-spinner fa-spin"></i>
            {{ isCreatingTask ? "Creating..." : "Create Task" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Add Task Group Modal -->
    <div
      v-if="showAddTaskGroupModalFlag"
      class="modal-overlay"
      :class="{ 'dark-mode': isDarkMode }"
      @click="closeAddTaskGroupModal"
    >
      <div
        class="modal-content"
        :class="{ 'dark-mode': isDarkMode }"
        @click.stop
      >
        <div class="modal-header">
          <h3>Add New Task Group</h3>
          <button class="modal-close-btn" @click="closeAddTaskGroupModal">
            <i class="fa fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="createNewTaskGroup">
            <div class="form-group">
              <label for="taskGroupName">Task Group Name *</label>
              <input
                type="text"
                id="taskGroupName"
                v-model="newTaskGroup.name"
                class="form-control"
                placeholder="Enter task group name"
                required
              />
            </div>

            <div class="form-group">
              <label for="taskGroupDescription">Description</label>
              <textarea
                id="taskGroupDescription"
                v-model="newTaskGroup.description"
                class="form-control"
                rows="3"
                placeholder="Enter task group description (optional)"
              ></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            @click="closeAddTaskGroupModal"
          >
            Cancel
          </button>
          <button
            type="button"
            class="btn btn-success"
            @click="createNewTaskGroup"
            :disabled="!newTaskGroup.name || isCreatingTaskGroup"
          >
            <i v-if="isCreatingTaskGroup" class="fa fa-spinner fa-spin"></i>
            {{ isCreatingTaskGroup ? "Creating..." : "Create Group" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Task Description Modal -->
    <div
      v-if="showDescriptionModalFlag"
      class="modal-overlay"
      :class="{ 'dark-mode': isDarkMode }"
      @click="closeDescriptionModal"
    >
      <div
        class="modal-content description-modal"
        :class="{ 'dark-mode': isDarkMode }"
        @click.stop
      >
        <div class="modal-header">
          <h3>
            <i class="fa fa-file-text me-2"></i>
            Task Description
          </h3>
          <div class="task-info">
            <span class="task-title">{{
              selectedTaskForDescription?.title
            }}</span>
            <span class="task-id">#{{ selectedTaskForDescription?.id }}</span>
          </div>
          <button class="modal-close-btn" @click="closeDescriptionModal">
            <i class="fa fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="description-editor">
            <label for="taskDescriptionText">Description</label>
            <textarea
              id="taskDescriptionText"
              v-model="descriptionText"
              class="form-control description-textarea"
              rows="8"
              placeholder="Enter task description..."
              :disabled="isSavingDescription"
            ></textarea>
            <div class="description-info">
              <small class="text-muted">
                <i class="fa fa-info-circle"></i>
                You can use line breaks to format your description
              </small>
              <small class="character-count">
                {{ descriptionText ? descriptionText.length : 0 }} characters
              </small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            @click="closeDescriptionModal"
            :disabled="isSavingDescription"
          >
            Cancel
          </button>
          <button
            type="button"
            class="btn btn-primary"
            @click="saveTaskDescription"
            :disabled="!descriptionText.trim() || isSavingDescription"
          >
            <i v-if="isSavingDescription" class="fa fa-spinner fa-spin"></i>
            {{ isSavingDescription ? "Saving..." : "Save Description" }}
          </button>
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

import axios from "axios";

export default {
  data() {
    return {
      setup,
      all: [],
      isOverviewModalVisible: false,
      selectedCells: [],
      activeCell: null,
      isDarkMode: true, // Dark mode state
      rowsToAdd: 10, // Number of rows to add at once
      collapsedGroups: {}, // Track which groups are collapsed
      projects: [], // List of projects for dropdowns
      selectedProject: null, // Currently selected project for filtering

      // Add Task Modal
      showAddTaskModalFlag: false,
      isCreatingTask: false,
      newTask: {
        title: "",
        task_group_id: "",
        priority: "normal",
        task_user_status: "Pending",
        start_date: "",
        end_date: "",
        description: "",
        project_id: null,
      },

      // Add Task Group Modal
      showAddTaskGroupModalFlag: false,
      isCreatingTaskGroup: false,
      newTaskGroup: {
        name: "",
        description: "",
        color: "#1a73e8",
        project_id: null,
      },

      // Task Description Modal
      showDescriptionModalFlag: false,
      selectedTaskForDescription: null,
      descriptionText: "",
      isSavingDescription: false,

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

      // Drag and Drop state
      isDragging: false,
      draggedGroupIndex: null,
      dragOverGroupIndex: null,
      customGroupOrder: [], // Store custom order of groups

      // Sorting state
      sortField: null, // Current field being sorted
      sortDirection: "asc", // 'asc' or 'desc'

      // Date filtering state
      selectedDate: "", // Selected date for filtering (empty by default to show all tasks)
      originalAllTasks: [], // Store original task data before filtering

      // Google Sheets columns configuration
      columns: [
        { label: "ID", key: "id", width: "80px", minWidth: "50px" },
        {
          label: "Task Title",
          key: "title",
          width: "200px",
          minWidth: "100px",
        },
        {
          label: "Group",
          key: "task_group_name",
          width: "150px",
          minWidth: "100px",
        },
        {
          label: "Priority",
          key: "priority",
          width: "120px",
          minWidth: "80px",
        },
        {
          label: "Status",
          key: "task_user_status",
          width: "120px",
          minWidth: "80px",
        },
        {
          label: "Start Date",
          key: "start_date",
          width: "120px",
          minWidth: "100px",
        },
        {
          label: "End Date",
          key: "end_date",
          width: "120px",
          minWidth: "100px",
        },
        { label: "Rating", key: "rating", width: "120px", minWidth: "80px" },
      ],

      // Sample data for dropdowns
      task_groups: [
        { id: 1, name: "Development" },
        { id: 2, name: "Design" },
        { id: 3, name: "Testing" },
        { id: 4, name: "Research" },
      ],

      filePath:
        "resources/js/backend/Views/SuperAdmin/Management/TestModule/helpers/demo.csv",
    };
  },
  created: async function () {
    let projectId = this.$route.query.project_id || null;

    // First get projects
    await this.get_all_projects();

    // If project_id is provided in query params, find and select that project
    if (projectId) {
      const projectsList = this.projects.data || this.projects;
      const foundProject = projectsList.find(
        (p) => p.id == projectId || p.slug == projectId
      );
      if (foundProject) {
        this.selectedProject = foundProject;
        console.log("Selected project from query param:", this.selectedProject);
      } else {
        console.warn("Project not found for ID:", projectId);
        // Fallback to first project if specified project not found
        if (projectsList && projectsList.length > 0) {
          this.selectedProject = projectsList[0];
        }
      }
    } else {
      // Auto-select the first project if no query param
      const projectsList = this.projects.data || this.projects;
      if (projectsList && projectsList.length > 0) {
        this.selectedProject = projectsList[0];
      }
    }

    // Get task groups for the dropdown
    await this.get_all_task_groups();
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

    // Load custom group order for the selected project
    this.loadGroupOrder();
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
    Object.values(this.saveTimeouts).forEach((timeout) =>
      clearTimeout(timeout)
    );
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

    // Date filtering methods
    filterTasksByDate() {
      console.log("Filtering tasks by date:", this.selectedDate);
      // The filtering logic is handled by the computed property
      // This method can be used for additional actions when date changes
    },

    clearDateFilter() {
      this.selectedDate = "";
      console.log("Date filter cleared");
    },

    // Store original tasks when component loads
    storeOriginalTasks() {
      if (!this.originalAllTasks && this.all?.data) {
        this.originalAllTasks = [...this.all.data];
        console.log("Original tasks stored:", this.originalAllTasks.length);
      }
    },

    // Get today's date in YYYY-MM-DD format
    getTodayDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, "0");
      const day = String(today.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },

    get_all_tasks: async function () {
      const projectId = this.selectedProject?.id || "";
      console.log("Fetching tasks for project:", projectId);
      console.log("Selected project object:", this.selectedProject);

      let response = await axios.get(`task?get_all=1&project_id=${projectId}`);
      this.all = response.data;

      // Store original tasks for date filtering
      if (response.data?.data) {
        this.originalAllTasks = [...response.data.data];
        console.log(
          "Original tasks stored for filtering:",
          this.originalAllTasks.length
        );
      }

      console.log("Tasks response:", response.data);
      console.log("Task count:", response.data?.data?.length || 0);

      // Log first few tasks to see structure
      if (response.data?.data?.length > 0) {
        console.log("Sample task structure:", response.data.data[0]);
        console.log(
          "Task project_ids:",
          response.data.data.map((task) => ({
            id: task.id,
            project_id: task.project_id,
          }))
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

      // Load group order for the new project
      this.loadGroupOrder();

      // Refetch tasks for the selected project
      this.get_all_tasks();
    },

    // Add Task Modal Methods
    showAddTaskModal() {
      this.resetNewTaskForm();
      this.showAddTaskModalFlag = true;
    },

    closeAddTaskModal() {
      this.showAddTaskModalFlag = false;
      this.resetNewTaskForm();
    },

    // Helper method to get current datetime in YYYY-MM-DDTHH:MM format
    getCurrentDateTime() {
      const now = new Date();
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, "0");
      const day = String(now.getDate()).padStart(2, "0");
      const hours = String(now.getHours()).padStart(2, "0");
      const minutes = String(now.getMinutes()).padStart(2, "0");
      return `${year}-${month}-${day}T${hours}:${minutes}`;
    },

    resetNewTaskForm() {
      const currentDateTime = this.getCurrentDateTime();
      this.newTask = {
        title: "",
        task_group_id: "",
        priority: "normal",
        task_user_status: "Pending",
        start_date: currentDateTime,
        end_date: currentDateTime,
        description: "",
        project_id: this.selectedProject?.id || null,
      };
      this.isCreatingTask = false;
    },

    async createNewTask() {
      if (!this.newTask.title || this.newTask.title.trim() === "") {
        window.s_warning("Task title is required");
        return;
      }

      // Validate start_date and end_date
      if (this.newTask.start_date && this.newTask.end_date) {
        const startDate = new Date(this.newTask.start_date);
        const endDate = new Date(this.newTask.end_date);
      }

      // Validate that start date is not in the past
      if (this.newTask.start_date) {
        const startDate = new Date(this.newTask.start_date);
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Reset time to start of day for accurate comparison
      }

      this.isCreatingTask = true;

      try {
        const taskData = {
          ...this.newTask,
          title: this.newTask.title.trim(),
          task_group_id: this.newTask.task_group_id || null,
          project_id: this.selectedProject?.id || null,
        };

        const response = await axios.post(`/task/store`, taskData);

        if (response.data.status === "success") {
          // Add the new task to local data
          if (!this.all?.data) {
            this.all = { data: [] };
          }

          // Enhance the returned task with group name for better display
          const createdTask = response.data.data;
          if (createdTask.task_group_id && !createdTask.task_group_name) {
            const group = this.task_groups.find(
              (g) => g.id == createdTask.task_group_id
            );
            if (group) {
              createdTask.task_group_name = group.name;
            }
          }

          // Add to both all.data and originalAllTasks for immediate display
          this.all.data.push(createdTask);

          // Also add to originalAllTasks so filtering works correctly
          if (!this.originalAllTasks) {
            this.originalAllTasks = [];
          }
          this.originalAllTasks.push(createdTask);

          // Clear date filter to show the new task immediately
          if (this.selectedDate) {
            this.selectedDate = "";
          }

          window.s_alert("Task created successfully!");
          this.closeAddTaskModal();

          // Auto-edit the new task
          this.$nextTick(() => {
            const newTaskIndex = this.getTaskGlobalIndex(response.data.data);
            if (newTaskIndex !== -1) {
              this.toggleRowEdit(newTaskIndex);
            }
          });
        } else {
          window.s_warning(response.data?.message || "Failed to create task");
        }
      } catch (error) {
        console.error("Error creating task:", error);

        // Handle Laravel validation errors
        if (
          error.response &&
          error.response.data &&
          error.response.data.errors
        ) {
          const errorMessages = [];
          for (const [field, messages] of Object.entries(
            error.response.data.errors
          )) {
            errorMessages.push(`${field}: ${messages.join(", ")}`);
          }
          window.s_warning(`Validation errors: ${errorMessages.join("; ")}`);
        } else if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          window.s_warning(error.response.data.message);
        } else {
          window.s_warning("Error creating task");
        }
      } finally {
        this.isCreatingTask = false;
      }
    },

    // Add Task Group Modal Methods
    showAddTaskGroupModal() {
      this.resetNewTaskGroupForm();
      this.showAddTaskGroupModalFlag = true;
    },

    closeAddTaskGroupModal() {
      this.showAddTaskGroupModalFlag = false;
      this.resetNewTaskGroupForm();
    },

    resetNewTaskGroupForm() {
      this.newTaskGroup = {
        name: "",
        description: "",
        color: "#1a73e8",
        project_id: this.selectedProject?.id || null,
      };
      this.isCreatingTaskGroup = false;
    },

    async createNewTaskGroup() {
      if (!this.newTaskGroup.name || this.newTaskGroup.name.trim() === "") {
        window.s_warning("Task group name is required");
        return;
      }

      this.isCreatingTaskGroup = true;

      try {
        const taskGroupData = {
          ...this.newTaskGroup,
          name: this.newTaskGroup.name.trim(),
          project_id: this.selectedProject?.id || null,
        };

        const response = await axios.post(`/task-group/store`, taskGroupData);

        if (response.data.status === "success") {
          // Add the new task group to local data
          const createdTaskGroup = response.data.data;
          this.task_groups.push(createdTaskGroup);

          window.s_alert("Task group created successfully!");
          this.closeAddTaskGroupModal();

          // Refresh task groups to update dropdowns
          await this.get_all_task_groups();
        } else {
          window.s_warning(
            response.data?.message || "Failed to create task group"
          );
        }
      } catch (error) {
        console.error("Error creating task group:", error);

        // Handle Laravel validation errors
        if (
          error.response &&
          error.response.data &&
          error.response.data.errors
        ) {
          const errorMessages = [];
          for (const [field, messages] of Object.entries(
            error.response.data.errors
          )) {
            errorMessages.push(`${field}: ${messages.join(", ")}`);
          }
          window.s_warning(`Validation errors: ${errorMessages.join("; ")}`);
        } else if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          window.s_warning(error.response.data.message);
        } else {
          window.s_warning("Error creating task group");
        }
      } finally {
        this.isCreatingTaskGroup = false;
      }
    },

    // Get all task groups for the dropdown
    async get_all_task_groups() {
      try {
        const response = await axios.get(`task-group?get_all=1`);
        if (response.data && response.data.data) {
          this.task_groups = response.data.data;
        }
      } catch (error) {
        console.error("Error fetching task groups:", error);
      }
    },

    // Task Description Modal Methods
    openDescriptionModal(task) {
      this.selectedTaskForDescription = task;
      this.descriptionText = task.description || "";
      this.showDescriptionModalFlag = true;
    },

    closeDescriptionModal() {
      this.showDescriptionModalFlag = false;
      this.selectedTaskForDescription = null;
      this.descriptionText = "";
      this.isSavingDescription = false;
    },

    async saveTaskDescription() {
      if (
        !this.selectedTaskForDescription ||
        !this.selectedTaskForDescription.slug
      ) {
        window.s_warning("Cannot save description. Task not found.");
        return;
      }

      this.isSavingDescription = true;

      try {
        const payload = {
          slug: this.selectedTaskForDescription.slug,
          title: this.selectedTaskForDescription.title,
          task_group_id: this.selectedTaskForDescription.task_group_id,
          priority: this.selectedTaskForDescription.priority,
          task_user_status: this.selectedTaskForDescription.task_user_status,
          start_date: this.selectedTaskForDescription.start_date,
          end_date: this.selectedTaskForDescription.end_date,
          description: this.descriptionText.trim(),
        };

        const response = await axios.post(
          `/task/update/${this.selectedTaskForDescription.slug}`,
          payload
        );

        if (response.data.status === "success") {
          // Update the task in local data
          const taskIndex = this.all.data.findIndex(
            (t) => t.id === this.selectedTaskForDescription.id
          );
          if (taskIndex !== -1) {
            this.all.data[taskIndex].description = this.descriptionText.trim();
          }

          // Update the selected task object
          this.selectedTaskForDescription.description =
            this.descriptionText.trim();

          window.s_alert("Description saved successfully!");
          this.closeDescriptionModal();
        } else {
          window.s_warning(
            response.data?.message || "Failed to save description"
          );
        }
      } catch (error) {
        console.error("Error saving description:", error);

        // Handle Laravel validation errors
        if (
          error.response &&
          error.response.data &&
          error.response.data.errors
        ) {
          const errorMessages = [];
          for (const [field, messages] of Object.entries(
            error.response.data.errors
          )) {
            errorMessages.push(`${field}: ${messages.join(", ")}`);
          }
          window.s_warning(`Validation errors: ${errorMessages.join("; ")}`);
        } else if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          window.s_warning(error.response.data.message);
        } else {
          window.s_warning("Error saving description");
        }
      } finally {
        this.isSavingDescription = false;
      }
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

    // Refresh component data
    async refreshComponent() {
      try {
        // Show loading state (you can add a loading indicator if needed)
        window.s_alert("Refreshing data...", "info");

        // Refresh all data
        await Promise.all([
          this.get_all_tasks(),
          this.get_all_task_groups(),
          this.get_all_projects(),
        ]);

        // Clear any edit modes
        this.editableRows = {};

        // Clear any pending save timeouts
        Object.values(this.saveTimeouts).forEach((timeout) =>
          clearTimeout(timeout)
        );
        this.saveTimeouts = {};

        // Reset recently updated indicators
        this.recentlyUpdated = {};

        window.s_alert("Data refreshed successfully!", "success");
      } catch (error) {
        console.error("Error refreshing component:", error);
        window.s_warning("Failed to refresh data");
      }
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
      this.startHeight = parseInt(
        this.rowHeights[rowIndex] || this.defaultRowHeight
      );
      event.preventDefault();
    },

    handleMouseMove(event) {
      if (!this.isResizing) return;

      if (this.resizeType === "column") {
        const deltaX = event.clientX - this.startX;
        const newWidth = Math.max(
          parseInt(this.columns[this.resizeIndex].minWidth) || 50,
          this.startWidth + deltaX
        );
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
      const currentDateTime = this.getCurrentDateTime();
      const newTask = {
        id: Date.now(),
        title: "New Task",
        task_group_id: null,
        task_group: null,
        priority: "normal",
        task_user_status: "Pending",
        start_date: currentDateTime,
        end_date: currentDateTime,
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
        const currentDateTime = this.getCurrentDateTime();

        for (let i = 0; i < numberOfRows; i++) {
          const newTaskData = {
            title: `New Task ${i + 1}`,
            task_group_id: null,
            priority: "normal",
            task_user_status: "Pending",
            start_date: currentDateTime,
            end_date: currentDateTime,
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
            const createdTask = response.data.data;
            this.all.data.push(createdTask);

            // Also add to originalAllTasks for filtering consistency
            if (!this.originalAllTasks) {
              this.originalAllTasks = [];
            }
            this.originalAllTasks.push(createdTask);
          }
        });

        // Clear date filter to show the new tasks immediately
        if (this.selectedDate) {
          this.selectedDate = "";
        }

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

      const currentDateTime = this.getCurrentDateTime();
      const newTaskData = {
        title: `New Task`,
        task_group_id: groupId === "ungrouped" ? null : groupId,
        priority: "normal",
        task_user_status: "Pending",
        start_date: currentDateTime,
        end_date: currentDateTime,
        description: "",
        project_id: this.selectedProject?.id || null,
      };

      try {
        // Call the API to store the task
        const response = await axios.post(`/task/store`, newTaskData);

        console.log("Task created response:", response.data);

        if (response.data.status === "success") {
          // Add the returned task data to local array
          const createdTask = response.data.data;

          // Enhance the task with group name for better display
          if (createdTask.task_group_id && !createdTask.task_group_name) {
            const group = this.task_groups.find(
              (g) => g.id == createdTask.task_group_id
            );
            if (group) {
              createdTask.task_group_name = group.name;
            }
          }

          this.all.data.push(createdTask);

          // Also add to originalAllTasks for filtering consistency
          if (!this.originalAllTasks) {
            this.originalAllTasks = [];
          }
          this.originalAllTasks.push(createdTask);

          // Clear date filter to show the new task immediately
          if (this.selectedDate) {
            this.selectedDate = "";
          }

          // Ensure the group is expanded to show the new task
          if (groupId !== "ungrouped") {
            this.collapsedGroups[groupId] = false;
          }

          window.s_alert(`Added new task to ${groupName}`);

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
      // Check if task can be removed (only Pending tasks)
      if (!this.canRemoveTask(task)) {
        window.s_warning(
          `Cannot remove task. Task status is "${task.task_status}". Only tasks with "Pending" status can be removed.`
        );
        return;
      }

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

        // Also remove from originalAllTasks for filtering consistency
        if (this.originalAllTasks) {
          const originalIndex = this.originalAllTasks.findIndex(
            (t) => t.id === task.id
          );
          if (originalIndex !== -1) {
            this.originalAllTasks.splice(originalIndex, 1);
          }
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

    // Calculate actual time between start and end dates
    FindActualTime(startDate, endDate) {
      if (!startDate || !endDate) {
        return "Not set";
      }

      try {
        const start = new Date(startDate);
        const end = new Date(endDate);

        // Validate dates
        if (isNaN(start.getTime()) || isNaN(end.getTime())) {
          return "Invalid date";
        }

        // Calculate difference in milliseconds
        const diffMs = end.getTime() - start.getTime();

        // If end date is before start date
        if (diffMs < 0) {
          return "Invalid range";
        }

        // Convert to different units
        const diffMinutes = Math.floor(diffMs / (1000 * 60));
        const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

        // Return appropriate format based on duration
        if (diffDays > 0) {
          const remainingHours = diffHours % 24;
          if (remainingHours > 0) {
            return `${diffDays}d ${remainingHours}h`;
          }
          return `${diffDays} day${diffDays > 1 ? "s" : ""}`;
        } else if (diffHours > 0) {
          const remainingMinutes = diffMinutes % 60;
          if (remainingMinutes > 0) {
            return `${diffHours}h ${remainingMinutes}m`;
          }
          return `${diffHours} hour${diffHours > 1 ? "s" : ""}`;
        } else if (diffMinutes > 0) {
          return `${diffMinutes} min${diffMinutes > 1 ? "s" : ""}`;
        } else {
          return "0 minutes";
        }
      } catch (error) {
        console.error("Error calculating actual time:", error);
        return "Error";
      }
    },

    // Task assignment helper methods
    isTaskAssigned(task) {
      return task.assigned_to && task.assigned_to !== task.creator;
    },

    getAssignmentText(task) {
      if (!this.isTaskAssigned(task)) return "";

      // If we have assignee name from the task object
      if (task.assignee_name) {
        return `Assigned to ${task.assignee_name}`;
      }

      // If we have assigned_to ID but no name, show generic text
      if (task.assigned_to) {
        return "Author";
      }

      return "";
    },

    getAssignmentTooltip(task) {
      if (!this.isTaskAssigned(task)) return "";

      const assigneeInfo = task.assignee_name
        ? task.assignee_name
        : `User ID: ${task.assigned_to}`;
      const creatorInfo = task.creator_name
        ? task.creator_name
        : `Creator ID: ${task.creator}`;

      return `Assigned to: ${assigneeInfo}\nCreated by: ${creatorInfo}`;
    },

    // Check if a task can be removed (only Pending tasks can be removed)
    canRemoveTask(task) {
      return !task.task_status || task.task_status === "Pending";
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
      const editedIndices = Object.keys(this.editableRows).filter(
        (index) => this.editableRows[index]
      );

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
      const hasEditableRows = Object.values(this.editableRows).some(
        (editable) => editable
      );

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
          const firstInput = document.querySelector(
            `tr:nth-child(${index + 1}) input, tr:nth-child(${
              index + 1
            }) select`
          );
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

          // Update the task in both all.data and originalAllTasks to maintain consistency
          const updatedTask = response.data.data || task;

          // Update in all.data
          if (this.all?.data && this.all.data[index]) {
            this.all.data[index] = { ...this.all.data[index], ...updatedTask };
          }

          // Update in originalAllTasks for filtering consistency
          if (this.originalAllTasks) {
            const originalIndex = this.originalAllTasks.findIndex(
              (t) => t.id === task.id
            );
            if (originalIndex !== -1) {
              this.originalAllTasks[originalIndex] = {
                ...this.originalAllTasks[originalIndex],
                ...updatedTask,
              };
            }
          }
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

      // Validate start_date and end_date
      if (task.start_date && task.end_date) {
        const startDate = new Date(task.start_date);
        const endDate = new Date(task.end_date);
      }

      // Validate that start date is not in the past
      if (task.start_date) {
        const startDate = new Date(task.start_date);
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Reset time to start of day for accurate comparison
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
            for (const [field, messages] of Object.entries(
              response.data.errors
            )) {
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
        if (
          error.response &&
          error.response.data &&
          error.response.data.errors
        ) {
          const errorMessages = [];
          for (const [field, messages] of Object.entries(
            error.response.data.errors
          )) {
            errorMessages.push(`${field}: ${messages.join(", ")}`);
          }
          window.s_warning(`Validation errors: ${errorMessages.join("; ")}`);
        } else if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
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
            for (const [field, messages] of Object.entries(
              response.data.errors
            )) {
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
        if (
          error.response &&
          error.response.data &&
          error.response.data.errors
        ) {
          const errorMessages = [];
          for (const [field, messages] of Object.entries(
            error.response.data.errors
          )) {
            errorMessages.push(`${field}: ${messages.join(", ")}`);
          }
          window.s_warning(`Validation errors: ${errorMessages.join("; ")}`);
        } else if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          window.s_warning(error.response.data.message);
        } else {
          window.s_warning("Error updating task status");
        }
      }
    },

    formatDateTime(dateTime) {
      if (!dateTime) return "";

      try {
        const date = new Date(dateTime);
        if (isNaN(date.getTime())) return "Invalid date";

        const today = new Date();
        const isToday = date.toDateString() === today.toDateString();

        if (isToday) {
          // Show only time for today's dates
          const options = {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true,
          };
          return date.toLocaleString("en-US", options);
        } else {
          // Show full date and time for other dates
          const options = {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true,
          };
          return date.toLocaleString("en-US", options);
        }
      } catch (error) {
        return "Invalid date";
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

    // Drag and Drop Methods for Group Ordering
    onGroupDragStart(event, groupIndex) {
      console.log(
        "🚀 DRAG START - Group index:",
        groupIndex,
        "Group:",
        this.groupedTasks[groupIndex]?.name
      );

      this.isDragging = true;
      this.draggedGroupIndex = groupIndex;

      // Set drag effect and data
      event.dataTransfer.effectAllowed = "move";
      event.dataTransfer.setData("text/plain", groupIndex.toString());

      // Add dragging class for visual feedback
      const row = event.target.closest(".group-header-row");
      if (row) {
        row.classList.add("dragging");
      }

      // Add global body class to change cursor
      document.body.classList.add("dragging-active");

      console.log("✅ Drag started successfully for group index:", groupIndex);
    },

    onGroupDragEnd(event) {
      console.log("🏁 DRAG END");

      this.isDragging = false;
      this.draggedGroupIndex = null;
      this.dragOverGroupIndex = null;

      // Remove dragging class
      const row = event.target.closest(".group-header-row");
      if (row) {
        row.classList.remove("dragging");
      }

      // Remove global body class
      document.body.classList.remove("dragging-active");

      // Remove any drag-over classes
      document
        .querySelectorAll(".group-header-row.drag-over")
        .forEach((row) => {
          row.classList.remove("drag-over");
        });

      console.log("✅ Drag ended successfully");
    },

    onGroupDragOver(event, groupIndex) {
      // Allow dropping by preventing default
      event.preventDefault();

      if (
        this.draggedGroupIndex === null ||
        this.draggedGroupIndex === groupIndex
      ) {
        return;
      }

      console.log(
        "📍 DRAG OVER - From:",
        this.draggedGroupIndex,
        "To:",
        groupIndex
      );

      event.dataTransfer.dropEffect = "move";
      this.dragOverGroupIndex = groupIndex;

      // Add visual feedback
      const row = event.target.closest(".group-header-row");
      if (row) {
        row.classList.add("drag-over");
      }
    },

    onGroupDragLeave(event) {
      // Only remove if actually leaving the element
      const row = event.target.closest(".group-header-row");
      if (row && !row.contains(event.relatedTarget)) {
        row.classList.remove("drag-over");
      }
    },

    onGroupDrop(event, dropIndex) {
      event.preventDefault();
      event.stopPropagation();

      console.log(
        "🎯 DROP EVENT - From:",
        this.draggedGroupIndex,
        "To:",
        dropIndex
      );

      if (
        this.draggedGroupIndex === null ||
        this.draggedGroupIndex === dropIndex
      ) {
        console.log("❌ Drop cancelled - same position or no drag source");
        return;
      }

      console.log(
        `✅ Moving group from index ${this.draggedGroupIndex} to ${dropIndex}`
      );

      // Reorder the groups in the customGroupOrder array
      this.reorderGroups(this.draggedGroupIndex, dropIndex);

      // Clean up drag state
      this.isDragging = false;
      this.draggedGroupIndex = null;
      this.dragOverGroupIndex = null;

      // Remove visual feedback classes
      document
        .querySelectorAll(".group-header-row.drag-over")
        .forEach((row) => {
          row.classList.remove("drag-over");
        });

      // Remove global body class
      document.body.classList.remove("dragging-active");

      window.s_alert("Group order updated successfully!");
    },

    reorderGroups(fromIndex, toIndex) {
      console.log("🔄 Reordering groups from", fromIndex, "to", toIndex);

      // Get the current grouped tasks
      const groups = [...this.groupedTasks];

      if (
        fromIndex < 0 ||
        fromIndex >= groups.length ||
        toIndex < 0 ||
        toIndex >= groups.length
      ) {
        console.error("❌ Invalid indices for reordering");
        return;
      }

      // Create or update custom group order
      if (this.customGroupOrder.length === 0) {
        // Initialize custom order with current group IDs
        this.customGroupOrder = groups.map((group) => group.id);
        console.log("🆕 Initialized group order:", this.customGroupOrder);
      }

      console.log(
        "Before reorder - groups:",
        groups.map((g) => ({ id: g.id, name: g.name }))
      );
      console.log("Before reorder - customGroupOrder:", this.customGroupOrder);

      // Make a copy of the current order to trigger reactivity
      const newOrder = [...this.customGroupOrder];

      // Move the group in the custom order array
      const movedGroupId = newOrder[fromIndex];
      newOrder.splice(fromIndex, 1);
      newOrder.splice(toIndex, 0, movedGroupId);

      console.log("After manipulation - newOrder:", newOrder);

      // Replace the entire array to trigger reactivity
      this.customGroupOrder = newOrder;

      console.log("✅ New group order:", this.customGroupOrder);

      // Store in localStorage for persistence
      if (this.selectedProject?.id) {
        localStorage.setItem(
          `groupOrder_${this.selectedProject.id}`,
          JSON.stringify(this.customGroupOrder)
        );
        console.log("💾 Saved group order to localStorage");
      }

      // Force Vue to re-evaluate the computed property
      this.$nextTick(() => {
        console.log(
          "🔄 After update - grouped tasks:",
          this.groupedTasks.map((g) => ({ id: g.id, name: g.name }))
        );
      });
    },

    // Test method for debugging group reorder
    testGroupReorder() {
      console.log("🧪 Testing group reorder functionality");
      console.log(
        "Current groups:",
        this.groupedTasks.map((g) => ({ id: g.id, name: g.name }))
      );
      console.log("Current custom order:", this.customGroupOrder);

      if (this.groupedTasks.length >= 2) {
        // Test reordering: move first group to second position
        console.log("🔄 Moving first group to second position");
        this.reorderGroups(0, 1);
        console.log(
          "After reorder:",
          this.groupedTasks.map((g) => ({ id: g.id, name: g.name }))
        );
      } else {
        console.log("❌ Need at least 2 groups to test reordering");
      }
    },

    // Reset group order to default (alphabetical)
    resetGroupOrder() {
      console.log("🔄 Resetting group order to default");

      // Clear custom order
      this.customGroupOrder = [];

      // Remove from localStorage
      if (this.selectedProject?.id) {
        localStorage.removeItem(`groupOrder_${this.selectedProject.id}`);
        console.log("🗑️ Removed group order from localStorage");
      }

      console.log("✅ Group order reset to alphabetical");
      window.s_alert("Group order reset to default alphabetical order");
    },

    // Sorting methods
    sortBy(field) {
      console.log(`🔄 Sorting by field: ${field}`);

      if (this.sortField === field) {
        // Toggle direction if same field
        this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
      } else {
        // Set new field and default to ascending
        this.sortField = field;
        this.sortDirection = "asc";
      }

      console.log(`📊 Sort: ${field} ${this.sortDirection}`);
    },

    // Get sort icon for table headers
    getSortIcon(field) {
      if (this.sortField !== field) {
        return "fa-sort"; // Default sort icon
      }
      return this.sortDirection === "asc" ? "fa-sort-up" : "fa-sort-down";
    },

    // Check if field is currently being sorted
    isSorted(field) {
      return this.sortField === field;
    },

    // Get display name for sort field
    getSortFieldDisplayName(field) {
      const fieldNames = {
        title: "Task Title",
        priority: "Priority",
        task_user_status: "Dev Status",
        task_status: "Task Status",
        start_date: "Start Date",
        end_date: "End Date",
      };
      return fieldNames[field] || field;
    },

    // Clear sorting
    clearSort() {
      this.sortField = null;
      this.sortDirection = "asc";
      console.log("🔄 Sorting cleared");
    },

    loadGroupOrder() {
      // Load custom group order from localStorage
      if (this.selectedProject?.id) {
        const saved = localStorage.getItem(
          `groupOrder_${this.selectedProject.id}`
        );
        if (saved) {
          try {
            this.customGroupOrder = JSON.parse(saved);
            console.log("Loaded group order:", this.customGroupOrder);
          } catch (e) {
            console.warn("Failed to parse saved group order:", e);
            this.customGroupOrder = [];
          }
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
      return (
        this.all?.data?.length > 0 &&
        this.all.data?.every((item) =>
          this.selected.some((s) => s.id === item.id)
        )
      );
    },

    // Format selected date for display in tooltip
    formatSelectedDate() {
      if (!this.selectedDate) return "";
      const date = new Date(this.selectedDate);
      return date.toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    },

    // Tasks filtered by search and date
    filteredTasks() {
      let tasks = this.originalAllTasks || this.all?.data || [];

      // Filter by date if date is selected
      if (this.selectedDate) {
        const selectedDate = new Date(this.selectedDate);
        selectedDate.setHours(0, 0, 0, 0); // Set to start of day for comparison

        tasks = tasks.filter((task) => {
          const taskStartDate = new Date(task.start_date);
          const taskEndDate = new Date(task.end_date);
          taskStartDate.setHours(0, 0, 0, 0);
          taskEndDate.setHours(23, 59, 59, 999);

          // Check if the selected date falls within the task date range
          return selectedDate >= taskStartDate && selectedDate <= taskEndDate;
        });
      }

      // Filter by search query if needed (keeping existing search functionality)
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        tasks = tasks.filter((task) => {
          return (
            task.title.toLowerCase().includes(query) ||
            task.description.toLowerCase().includes(query) ||
            task.priority.toLowerCase().includes(query) ||
            task.task_status.toLowerCase().includes(query) ||
            task.task_user_status.toLowerCase().includes(query) ||
            (task.assigned_users &&
              task.assigned_users.some((user) =>
                user.name.toLowerCase().includes(query)
              ))
          );
        });
      }

      return tasks;
    },

    groupedTasks() {
      // Start with filtered tasks instead of all tasks
      let tasks = this.filteredTasks || [];

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
        let groupId = "ungrouped";
        let groupName = "Ungrouped Tasks";

        // Only group if task_group_id exists AND the group actually exists
        if (task.task_group_id) {
          // Check if the group actually exists in task_groups array
          const existingGroup = this.task_groups.find(
            (g) => g.id == task.task_group_id
          );

          if (existingGroup) {
            groupId = task.task_group_id;
            // First try to use task_group_name from the task object
            groupName =
              task.task_group_name ||
              task.task_group?.name ||
              existingGroup.name;
          }
          // If group doesn't exist, it will remain as "ungrouped"
        }

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
      const allGroups = Object.values(groups).map((group) => ({
        id: group.id,
        name: group.name,
        tasks: group.tasks,
        collapsed: this.collapsedGroups[group.id] || false,
      }));

      // Clean up customGroupOrder - remove any group IDs that no longer exist
      const existingGroupIds = allGroups.map((group) => group.id);
      const cleanedCustomOrder = this.customGroupOrder.filter((groupId) =>
        existingGroupIds.includes(groupId)
      );

      // If the custom order was cleaned up, update it
      if (cleanedCustomOrder.length !== this.customGroupOrder.length) {
        console.log("🧹 Cleaning up custom group order");
        console.log("Old order:", this.customGroupOrder);
        console.log("Existing groups:", existingGroupIds);
        console.log("Cleaned order:", cleanedCustomOrder);

        // Update the custom order
        this.customGroupOrder = cleanedCustomOrder;

        // Save to localStorage
        if (this.selectedProject?.id) {
          localStorage.setItem(
            `groupOrder_${this.selectedProject.id}`,
            JSON.stringify(this.customGroupOrder)
          );
        }
      }

      const result = allGroups.sort((a, b) => {
        // If we have custom group order, use it
        if (this.customGroupOrder.length > 0) {
          const aIndex = this.customGroupOrder.indexOf(a.id);
          const bIndex = this.customGroupOrder.indexOf(b.id);

          console.log(
            `Sorting: ${a.name}(${a.id}) (index: ${aIndex}) vs ${b.name}(${b.id}) (index: ${bIndex})`
          );

          // If both groups are in custom order, sort by their custom position
          if (aIndex !== -1 && bIndex !== -1) {
            console.log(
              `Both in custom order: ${a.name} at ${aIndex}, ${b.name} at ${bIndex}`
            );
            return aIndex - bIndex;
          }

          // If only a is in custom order, it comes first
          if (aIndex !== -1 && bIndex === -1) {
            console.log(
              `${a.name} in custom order, ${b.name} not - ${a.name} first`
            );
            return -1;
          }

          // If only b is in custom order, it comes first
          if (aIndex === -1 && bIndex !== -1) {
            console.log(
              `${b.name} in custom order, ${a.name} not - ${b.name} first`
            );
            return 1;
          }
        }

        // Default sorting: "Ungrouped Tasks" comes last, others alphabetically
        if (a.name === "Ungrouped Tasks") return 1;
        if (b.name === "Ungrouped Tasks") return -1;
        return a.name.localeCompare(b.name);
      });

      // Sort tasks within each group if sorting is active
      if (this.sortField) {
        result.forEach((group) => {
          group.tasks.sort((a, b) => {
            const aValue = a[this.sortField] || "";
            const bValue = b[this.sortField] || "";

            // Handle different data types
            let comparison = 0;

            if (
              this.sortField === "task_user_status" ||
              this.sortField === "task_status"
            ) {
              // Status sorting with custom priority order
              const statusOrder = {
                Pending: 1,
                "In Progress": 2,
                Completed: 3,
                "Not Completed": 4,
              };

              const aOrder = statusOrder[aValue] || 999;
              const bOrder = statusOrder[bValue] || 999;
              comparison = aOrder - bOrder;
            } else if (this.sortField === "priority") {
              // Priority sorting with custom order
              const priorityOrder = {
                high: 1,
                medium: 2,
                normal: 3,
                low: 4,
              };

              const aOrder = priorityOrder[aValue.toLowerCase()] || 999;
              const bOrder = priorityOrder[bValue.toLowerCase()] || 999;
              comparison = aOrder - bOrder;
            } else if (
              this.sortField === "start_date" ||
              this.sortField === "end_date"
            ) {
              // Date sorting
              const aDate = aValue ? new Date(aValue) : new Date(0);
              const bDate = bValue ? new Date(bValue) : new Date(0);
              comparison = aDate.getTime() - bDate.getTime();
            } else {
              // General string comparison for title and other fields
              comparison = String(aValue).localeCompare(String(bValue));
            }

            // Apply sort direction
            return this.sortDirection === "asc" ? comparison : -comparison;
          });
        });

        console.log(
          `📊 Tasks sorted by ${this.sortField} (${this.sortDirection})`
        );
      }

      return result;
    },

    // Get filtered task count for display
    filteredTaskCount() {
      // Return count of filtered tasks
      return this.filteredTasks?.length || 0;
    },

    // Check if new task dates are invalid
    isNewTaskDateInvalid() {
      // Check if start date is in the past
      if (this.newTask.start_date) {
        const startDate = new Date(this.newTask.start_date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (startDate < today) {
          return true;
        }
      }

      // Check if start date is after end date
      if (this.newTask.start_date && this.newTask.end_date) {
        const startDate = new Date(this.newTask.start_date);
        const endDate = new Date(this.newTask.end_date);
        return startDate > endDate;
      }

      return false;
    },

    // Get today's date in YYYY-MM-DD format for min date validation
    todayDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, "0");
      const day = String(today.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },

    // Get the appropriate date validation error message
    dateValidationMessage() {
      if (this.newTask.start_date) {
        const startDate = new Date(this.newTask.start_date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
      }

      if (this.newTask.start_date && this.newTask.end_date) {
        const startDate = new Date(this.newTask.start_date);
        const endDate = new Date(this.newTask.end_date);
      }

      return "";
    },
  },

  watch: {
    // Watch for route query parameter changes
    "$route.query.project_id": {
      handler(newProjectId) {
        if (newProjectId) {
          const projectsList = this.projects.data || this.projects;
          const foundProject = projectsList.find(
            (p) => p.id == newProjectId || p.slug == newProjectId
          );
          if (foundProject && foundProject.id !== this.selectedProject?.id) {
            this.selectedProject = foundProject;
            console.log(
              "Project changed via route query:",
              this.selectedProject
            );
            // Reload tasks for the new project
            this.get_all_tasks();
          }
        }
      },
      immediate: false,
    },
  },
};
</script>

<style scoped>
@import "./TaskBoard.css";

/* Task Title Professional Styling */
.task-title-container {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.task-title-display {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.editable-title {
  cursor: pointer;
  font-weight: 500;
  color: #ffffff;
  transition: all 0.2s ease;
  display: block;
  padding: 2px 0;
}

.editable-title:hover {
  color: #3498db;
  text-decoration: underline;
}

.editable-title.task-assigned {
  border-left: 3px solid #f39c12;
  padding-left: 6px;
  background: linear-gradient(
    90deg,
    rgba(243, 156, 18, 0.1) 0%,
    transparent 100%
  );
}

.assignment-info {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.75rem;
  color: #7f8c8d;
  margin-top: 2px;
}

.assignment-icon {
  color: #f39c12;
  font-size: 0.7rem;
}

.assignment-text {
  font-style: italic;
  font-weight: 400;
}

/* Task Actions Styling */
.task-actions {
  display: flex;
  align-items: center;
  gap: 5px;
}

.task-description-btn {
  margin-right: 5px;
}

.task-description-btn:hover {
  background-color: #17a2b8;
  border-color: #138496;
}

/* Description Modal Styling */
.description-modal {
  max-width: 600px;
  width: 90%;
}

.description-modal .modal-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 20px 24px 16px;
  border-bottom: 1px solid #e9ecef;
}

.description-modal .modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
  display: flex;
  align-items: center;
}

.task-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.task-title {
  font-weight: 500;
  color: #495057;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.task-id {
  font-size: 0.85rem;
  color: #6c757d;
  background: #f8f9fa;
  padding: 2px 8px;
  border-radius: 12px;
}

.description-editor {
  padding: 0;
}

.description-editor label {
  font-weight: 500;
  margin-bottom: 8px;
  color: #495057;
  display: block;
}

.description-textarea {
  border: 1px solid #ced4da;
  border-radius: 6px;
  padding: 12px;
  font-size: 14px;
  line-height: 1.5;
  resize: vertical;
  min-height: 120px;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.description-textarea:focus {
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.description-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
}

.character-count {
  color: #6c757d;
  font-size: 0.8rem;
}

/* Dark mode support */
.dark-mode .editable-title {
  color: #ecf0f1;
}

.dark-mode .editable-title:hover {
  color: #3498db;
}

.dark-mode .assignment-info {
  color: #bdc3c7;
}

.dark-mode .editable-title.task-assigned {
  background: linear-gradient(
    90deg,
    rgba(243, 156, 18, 0.2) 0%,
    transparent 100%
  );
}

/* Dark mode support for description modal */
.dark-mode .description-modal .modal-header h3 {
  color: #ecf0f1;
}

.dark-mode .task-title {
  color: #bdc3c7;
}

.dark-mode .task-id {
  color: #95a5a6;
  background: #34495e;
}

.dark-mode .description-editor label {
  color: #bdc3c7;
}

.dark-mode .description-textarea {
  background-color: #2c3e50;
  border-color: #34495e;
  color: #ecf0f1;
}

.dark-mode .description-textarea:focus {
  border-color: #3498db;
  box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

.dark-mode .character-count {
  color: #95a5a6;
}

/* Responsive design */
@media (max-width: 768px) {
  .assignment-info {
    font-size: 0.7rem;
  }

  .assignment-text {
    display: none;
  }

  .assignment-info::after {
    content: "Assigned";
    font-size: 0.65rem;
  }

  .description-modal {
    width: 95%;
    max-width: none;
  }

  .task-actions {
    flex-direction: column;
    gap: 3px;
  }

  .task-description-btn,
  .task-delete-btn {
    font-size: 0.75rem;
    padding: 4px 8px;
  }
}
</style>
