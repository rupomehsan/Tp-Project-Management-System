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
            <span v-else-if="selectedUser" class="user-filter-badge">
              - {{ selectedUser.name }}'s Projects
            </span>
            <span v-else class="all-filter-badge"> - All Projects </span>
            <span v-if="selectedDevStatus" class="dev-status-filter-badge">
              | Dev: {{ selectedDevStatus }}
            </span>
            <span v-if="selectedTaskStatus" class="task-status-filter-badge">
              | Status: {{ selectedTaskStatus }}
            </span>
            <span v-if="searchQuery" class="search-filter-badge">
              | Search: "{{ searchQuery }}"
            </span>
          </h2>
          <small v-if="filteredTaskCount !== undefined" class="task-count-info">
            Showing {{ filteredTaskCount }} task{{
              filteredTaskCount !== 1 ? "s" : ""
            }}
            <span
              v-if="
                selectedProject ||
                selectedUser ||
                selectedDevStatus ||
                selectedTaskStatus ||
                filterStartDate ||
                filterEndDate ||
                searchQuery
              "
              class="filter-active-indicator"
            >
              (filtered)
            </span>
          </small>
        </div>
      </div>
      <div class="toolbar-right">
        <div class="action-buttons-row">
          <button
            v-if="
              selectedProject ||
              selectedUser ||
              selectedDevStatus ||
              selectedTaskStatus ||
              filterStartDate ||
              filterEndDate ||
              searchQuery
            "
            class="btn btn-secondary btn-clear-filters"
            @click="clearAllFilters"
            title="Clear All Filters"
          >
            <i class="fa fa-filter"></i>
            <span>Clear Filters</span>
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
            class="btn btn-success btn-add-group"
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
        </div>
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
      <!-- Professional User Tabs Section -->
      <div class="professional-user-tabs mb-3 px-2">
        <div class="user-section-header">
          <div class="section-title-container">
            <div class="section-icon">
              <i class="fa fa-users"></i>
            </div>
            <div class="section-title-content">
              <h5 class="section-title">Team Members</h5>
              <small class="section-subtitle"
                >{{ assignedUsers.length }} users assigned to tasks</small
              >
            </div>
          </div>

          <div class="section-actions">
            <div class="date-controls-row">
              <!-- Search Input -->
              <div class="search-filter-group">
                <div class="input-group">
                  <input
                    type="text"
                    class="form-control search-input"
                    v-model="searchQuery"
                    @input="filterTasksBySearch"
                    placeholder="Search tasks..."
                    :title="
                      searchQuery
                        ? `Searching for: ${searchQuery}`
                        : 'Search by title, description, user, or project'
                    "
                  />
                  <button
                    v-if="searchQuery"
                    class="btn btn-outline-secondary btn-clear-search"
                    @click="clearSearch"
                    title="Clear search"
                  >
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </div>

              <div class="date-filter-group">
                <input
                  type="date"
                  class="form-control date-picker"
                  v-model="filterStartDate"
                  @change="filterTasksByDateRange"
                  :title="
                    filterStartDate
                      ? `Filtering tasks from ${formatDate(filterStartDate)}`
                      : 'Select start date to filter tasks'
                  "
                  placeholder="Start Date"
                />
              </div>
              <div class="date-filter-group">
                <input
                  type="date"
                  class="form-control date-picker"
                  v-model="filterEndDate"
                  @change="filterTasksByDateRange"
                  :min="filterStartDate"
                  :title="
                    filterEndDate
                      ? `Filtering tasks until ${formatDate(filterEndDate)}`
                      : 'Select end date to filter tasks'
                  "
                  placeholder="End Date"
                />
              </div>
              <button
                v-if="filterStartDate || filterEndDate"
                class="btn btn-outline-secondary border btn-clear-date"
                @click="clearDateFilters"
                title="Clear date filters"
              >
                <i class="fa fa-times"></i>
              </button>
            </div>
            <!-- Filter Controls Row -->
            <div class="filter-controls-row">
              <!-- Project Dropdown -->
              <div class="dropdown me-1 me-md-2">
                <button
                  class="btn btn-sm btn-outline-light dropdown-toggle project-dropdown-btn"
                  type="button"
                  id="projectDropdown"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  :title="
                    selectedProject
                      ? `Current project: ${selectedProject.name}`
                      : 'All projects selected'
                  "
                >
                  <i class="fa fa-folder me-1 d-none d-md-inline"></i>
                  <span class="d-none d-md-inline">{{
                    selectedProject ? selectedProject.name : "All Projects"
                  }}</span>
                  <span class="d-md-none">{{
                    selectedProject
                      ? selectedProject.name.substring(0, 8) + "..."
                      : "Projects"
                  }}</span>
                </button>
                <ul
                  class="dropdown-menu project-dropdown-menu"
                  aria-labelledby="projectDropdown"
                >
                  <!-- All Projects Option -->
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectProject(null, $event)"
                      :class="{ active: !selectedProject }"
                    >
                      <i class="fa fa-globe me-2"></i>
                      All Projects
                    </a>
                  </li>
                  <!-- Separator -->
                  <li><hr class="dropdown-divider" /></li>
                  <!-- Individual Projects -->
                  <li
                    v-for="project in projects.data || projects"
                    :key="project.id"
                  >
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectProject(project, $event)"
                      :class="{
                        active:
                          selectedProject && selectedProject.id === project.id,
                      }"
                      :title="`Switch to ${project.name}`"
                    >
                      <i class="fa fa-folder me-2"></i>
                      {{ project.name }}
                    </a>
                  </li>
                  <!-- Empty state -->
                  <li
                    v-if="!projects.data && !projects.length"
                    class="px-3 py-2 text-muted"
                  >
                    <small>No projects available</small>
                  </li>
                </ul>
              </div>

              <!-- Dev Status Dropdown -->
              <div class="dropdown me-1 me-md-2">
                <button
                  class="btn btn-sm btn-outline-light dropdown-toggle dev-status-dropdown-btn"
                  type="button"
                  id="devStatusDropdown"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  :title="
                    selectedDevStatus
                      ? `Current dev status: ${selectedDevStatus}`
                      : 'All dev statuses'
                  "
                >
                  <i class="fa fa-cogs me-1 d-none d-md-inline"></i>
                  <span class="d-none d-md-inline">{{
                    selectedDevStatus || "All Dev Status"
                  }}</span>
                  <span class="d-md-none">{{
                    selectedDevStatus || "Dev"
                  }}</span>
                </button>
                <ul
                  class="dropdown-menu dev-status-dropdown-menu"
                  aria-labelledby="devStatusDropdown"
                >
                  <!-- All Dev Status Option -->
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectDevStatus(null, $event)"
                      :class="{ active: !selectedDevStatus }"
                    >
                      <i class="fa fa-list me-2"></i>
                      All Dev Status
                    </a>
                  </li>
                  <!-- Separator -->
                  <li><hr class="dropdown-divider" /></li>
                  <!-- Individual Dev Status Options -->
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectDevStatus('Pending', $event)"
                      :class="{ active: selectedDevStatus === 'Pending' }"
                    >
                      <i class="fa fa-clock me-2"></i>
                      📝 Pending
                    </a>
                  </li>
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectDevStatus('In Progress', $event)"
                      :class="{ active: selectedDevStatus === 'In Progress' }"
                    >
                      <i class="fa fa-play me-2"></i>
                      ⚡ In Progress
                    </a>
                  </li>
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectDevStatus('Completed', $event)"
                      :class="{ active: selectedDevStatus === 'Completed' }"
                    >
                      <i class="fa fa-check me-2"></i>
                      ✅ Completed
                    </a>
                  </li>
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectDevStatus('Not Completed', $event)"
                      :class="{ active: selectedDevStatus === 'Not Completed' }"
                    >
                      <i class="fa fa-times me-2"></i>
                      ❌ Not Completed
                    </a>
                  </li>
                </ul>
              </div>

              <!-- Task Status Dropdown -->
              <div class="dropdown me-1 me-md-2">
                <button
                  class="btn btn-sm btn-outline-light dropdown-toggle task-status-dropdown-btn"
                  type="button"
                  id="taskStatusDropdown"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  :title="
                    selectedTaskStatus
                      ? `Current task status: ${selectedTaskStatus}`
                      : 'All task statuses'
                  "
                >
                  <i class="fa fa-flag me-1 d-none d-md-inline"></i>
                  <span class="d-none d-md-inline">{{
                    selectedTaskStatus || "All Task Status"
                  }}</span>
                  <span class="d-md-none">{{
                    selectedTaskStatus || "Status"
                  }}</span>
                </button>
                <ul
                  class="dropdown-menu task-status-dropdown-menu"
                  aria-labelledby="taskStatusDropdown"
                >
                  <!-- All Task Status Option -->
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectTaskStatus(null, $event)"
                      :class="{ active: !selectedTaskStatus }"
                    >
                      <i class="fa fa-list me-2"></i>
                      All Task Status
                    </a>
                  </li>
                  <!-- Separator -->
                  <li><hr class="dropdown-divider" /></li>
                  <!-- Individual Task Status Options -->
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectTaskStatus('Pending', $event)"
                      :class="{ active: selectedTaskStatus === 'Pending' }"
                    >
                      <i class="fa fa-clock me-2"></i>
                      📝 Pending
                    </a>
                  </li>
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectTaskStatus('In Progress', $event)"
                      :class="{ active: selectedTaskStatus === 'In Progress' }"
                    >
                      <i class="fa fa-play me-2"></i>
                      ⚡ In Progress
                    </a>
                  </li>
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectTaskStatus('Completed', $event)"
                      :class="{ active: selectedTaskStatus === 'Completed' }"
                    >
                      <i class="fa fa-check me-2"></i>
                      ✅ Completed
                    </a>
                  </li>
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectTaskStatus('Not Completed', $event)"
                      :class="{
                        active: selectedTaskStatus === 'Not Completed',
                      }"
                    >
                      <i class="fa fa-times me-2"></i>
                      ❌ Not Completed
                    </a>
                  </li>
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectTaskStatus('On Hold', $event)"
                      :class="{ active: selectedTaskStatus === 'On Hold' }"
                    >
                      <i class="fa fa-pause me-2"></i>
                      ⏸️ On Hold
                    </a>
                  </li>
                  <li>
                    <a
                      class="dropdown-item"
                      href="#"
                      @click.prevent="selectTaskStatus('Cancelled', $event)"
                      :class="{ active: selectedTaskStatus === 'Cancelled' }"
                    >
                      <i class="fa fa-ban me-2"></i>
                      ❌ Cancelled
                    </a>
                  </li>
                </ul>
              </div>

              <!-- Action Buttons -->
              <div class="action-buttons-group">
                <!-- Collapse/Expand Button -->
                <button
                  @click="toggleTeamMembers()"
                  class="btn btn-sm btn-outline-secondary collapse-btn"
                  :title="
                    isTeamMembersCollapsed
                      ? 'Expand team members'
                      : 'Collapse team members'
                  "
                >
                  <i
                    class="fa"
                    :class="
                      isTeamMembersCollapsed
                        ? 'fa-chevron-down'
                        : 'fa-chevron-up'
                    "
                  ></i>
                  <span class="d-none d-md-inline ms-1">{{
                    isTeamMembersCollapsed ? "Show" : "Hide"
                  }}</span>
                </button>

                <button
                  v-if="assignedUsers.length === 0"
                  @click="getAssignedUsers()"
                  class="btn btn-sm btn-outline-primary reload-btn"
                  title="Reload users"
                >
                  <i class="fa fa-refresh"></i>
                  <span class="d-none d-md-inline ms-1">Reload</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Professional User Cards -->
          <div class="user-cards-container" v-show="!isTeamMembersCollapsed">
            <!-- All Users Card -->
            <div
              class="user-card all-users-card"
              :class="{ active: !selectedUser }"
              @click="selectUser(null, $event)"
              title="View all tasks from all team members"
            >
              <div class="user-avatar-container">
                <div class="user-avatar all-users-avatar">
                  <i class="fa fa-users"></i>
                </div>
                <div class="user-status-indicator online"></div>
                <div class="user-info">
                  <div class="user-name">All Members</div>
                  <div class="user-role">Show all tasks</div>
                </div>
              </div>
              <div class="user-stats">
                <div class="task-count">
                  <span class="count-number">{{
                    userTaskCounts.all || 0
                  }}</span>
                  <span class="count-label">tasks</span>
                </div>
              </div>
            </div>

            <!-- Individual User Cards -->
            <div
              v-for="user in assignedUsers"
              :key="user.id"
              class="user-card"
              :class="{ active: selectedUser && selectedUser.id === user.id }"
              @click="selectUser(user, $event)"
              :title="`View tasks assigned to ${user.name}`"
            >
              <div class="user-avatar-container">
                <div class="user-avatar">
                  <img
                    v-if="user.avatar || user.profile_image"
                    :src="user.avatar || user.profile_image || '/avatar.png'"
                    :alt="user.name"
                    @error="handleImageError"
                  />
                  <img
                    v-else-if="user.image"
                    :src="user.image"
                    :alt="user.name"
                    @error="handleImageError"
                  />
                  <div v-else class="user-avatar-placeholder">
                    <span>{{ getInitials(user.name) }}</span>
                  </div>
                  <div
                    class="user-status-indicator"
                    :class="getUserStatus(user)"
                  ></div>
                </div>
                <div class="user-info">
                  <div class="user-name">{{ user.name }}</div>
                  <div class="user-role">
                    {{ user.role || user.designation || "Team Member" }}
                  </div>
                </div>
              </div>
              <div class="user-stats">
                <div class="task-count">
                  <span class="count-number">{{
                    userTaskCounts[user.id] || 0
                  }}</span>
                  <span class="count-label">tasks</span>
                </div>
                <div class="user-actions">
                  <button
                    class="btn-icon user-action-btn"
                    @click.stop="viewUserProfile(user)"
                    title="View user profile"
                  >
                    <i class="fa fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="assignedUsers.length === 0" class="empty-users-state">
              <div class="empty-icon">
                <i class="fa fa-user-plus"></i>
              </div>
              <div class="empty-content">
                <h6>No Team Members Found</h6>
                <p>No users are currently assigned to tasks in this view.</p>
                <button
                  @click="getAssignedUsers()"
                  class="btn btn-primary btn-sm"
                >
                  <i class="fa fa-refresh"></i>
                  Refresh Users
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Sorting indicator -->
        <!-- <div v-if="sortField" class="sort-indicator mb-2 p-2 bg-info text-white border rounded d-flex align-items-center justify-content-between">
          <small>
            <i class="fa fa-sort me-1"></i>
            <strong>Sorted by:</strong>
            {{ getSortFieldDisplayName(sortField) }}
            ({{ sortDirection === "asc" ? "A-Z" : "Z-A" }})
          </small>
          <button @click="clearSort" class="btn btn-sm btn-outline-light" title="Clear sorting"><i class="fa fa-times"></i> Clear Sort</button>
        </div> -->

        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead class="table-header">
              <tr>
                <!-- <th class="w-10" @dblclick="toggleEditMode">ID</th> -->
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
                  @click="sortBy('user')"
                  :class="{ sorted: isSorted('user') }"
                  title="Click to sort by Assigned User"
                >
                  Assigned User
                  <i class="fa ms-1" :class="getSortIcon('user')"></i>
                </th>
                <th
                  class="sortable-header"
                  @click="sortBy('project')"
                  :class="{ sorted: isSorted('project') }"
                  title="Click to sort by Project"
                >
                  Project
                  <i class="fa ms-1" :class="getSortIcon('project')"></i>
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
                  <i
                    class="fa ms-1"
                    :class="getSortIcon('task_user_status')"
                  ></i>
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
                v-for="(dateGroup, dateIndex) in groupedTasks"
                :key="'date-' + dateIndex"
              >
                <!-- Date Header Row -->
                <tr class="date-header-row">
                  <!-- Combined chevron + date in first column -->
                  <td
                    class="date-toggle"
                    colspan="11"
                    @click="toggleDate(dateGroup.date)"
                  >
                    <i
                      :class="
                        collapsedDates[dateGroup.date]
                          ? 'fa fa-chevron-right'
                          : 'fa fa-chevron-down'
                      "
                    ></i>
                    <span class="date-text ms-2">{{
                      dateGroup.date === "No Date" ? "No Date" : dateGroup.date
                    }}</span>
                  </td>
                  <td></td>
                </tr>

                <template v-if="!collapsedDates[dateGroup.date]">
                  <template
                    v-for="(group, groupIndex) in dateGroup.groups"
                    :key="'group-' + dateIndex + '-' + groupIndex"
                  >
                    <!-- compute flat index for group operations -->
                    <tr
                      class="group-header-row"
                      :class="{
                        dragging:
                          isDragging &&
                          draggedGroupIndex ===
                            getFlatGroupIndex(dateIndex, groupIndex),
                        'drag-over':
                          dragOverGroupIndex ===
                          getFlatGroupIndex(dateIndex, groupIndex),
                      }"
                      @dragstart.prevent="
                        onGroupDragStart(
                          $event,
                          getFlatGroupIndex(dateIndex, groupIndex)
                        )
                      "
                      @dragend="onGroupDragEnd"
                      @dragover="
                        onGroupDragOver(
                          $event,
                          getFlatGroupIndex(dateIndex, groupIndex)
                        )
                      "
                      @dragleave="onGroupDragLeave"
                      @drop="
                        onGroupDrop(
                          $event,
                          getFlatGroupIndex(dateIndex, groupIndex)
                        )
                      "
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
                      <td colspan="10" class="group-name-cell">
                        <div class="group-info">
                          <h5 class="group-title">
                            {{ group.name || "Ungrouped Tasks" }}
                            <span class="task-count"
                              >({{ group.tasks.length }} tasks)</span
                            >
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

                    <!-- Tasks in Group -->
                    <template v-if="!group.collapsed">
                      <tr
                        v-for="(task, taskIndex) in group.tasks"
                        :key="'task-' + task.id"
                        :class="`table_rows table_row_${task.id} ${
                          editableRows[getTaskGlobalIndex(task)]
                            ? 'editable-row'
                            : ''
                        } group-task-row`"
                        draggable
                        @dragstart="
                          onTaskDragStart(
                            $event,
                            task,
                            getFlatGroupIndex(dateIndex, groupIndex),
                            taskIndex
                          )
                        "
                        @dragover.prevent
                      >
                        <!-- Title / editable input -->
                        <td class="task-title-cell">
                          <div class="task-title">
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
                                :class="{
                                  'task-assigned': isTaskAssigned(task),
                                }"
                              >
                                {{ task.title }}
                              </span>
                              <div
                                v-if="isTaskAssigned(task)"
                                class="assignment-info"
                                :title="getAssignmentTooltip(task)"
                              >
                                <i class="fa fa-user-tag assignment-icon"></i>
                                <span class="assignment-text">{{
                                  getAssignmentText(task)
                                }}</span>
                              </div>
                            </div>
                          </div>
                        </td>

                        <!-- Assigned User -->
                        <td
                          class="text-limit"
                          :title="task.user ? task.user.name : 'Not assigned'"
                        >
                          <div class="assigned-user-cell">
                            <span
                              v-if="task.user && task.user.name"
                              class="user-info text-success"
                            >
                              <i class="fa fa-user text-primary"></i>
                              {{ task.user.name }}
                            </span>
                            <span v-else class="no-user-assigned text-muted">
                              <i class="fa fa-user-times"></i>
                              Not assigned
                            </span>
                          </div>
                        </td>

                        <!-- Project -->
                        <td :title="getTaskProjectName(task)">
                          <div class="project-cell">
                            <span
                              v-if="getTaskProjectName(task)"
                              class="project-info text-white"
                            >
                              <i class="fa fa-folder"></i>
                              {{ getTaskProjectName(task) }}
                            </span>
                            <span v-else class="no-project-assigned text-muted">
                              <i class="fa fa-folder-open"></i>
                              No project
                            </span>
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
                            <span v-else-if="task.priority === 'high'"
                              >🟡 High</span
                            >
                            <span v-else-if="task.priority === 'urgent'"
                              >🔴 Urgent</span
                            >
                            <span v-else>{{ task.priority }}</span>
                          </span>
                        </td>

                        <!-- Dev Status -->
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
                            <option value="Not Completed">
                              ❌ Not Completed
                            </option>
                          </select>
                          <span v-else>
                            <span v-if="task.task_user_status === 'Pending'"
                              >📝 Pending</span
                            >
                            <span
                              v-else-if="
                                task.task_user_status === 'In Progress'
                              "
                              >⚡ In Progress</span
                            >
                            <span
                              v-else-if="task.task_user_status === 'Completed'"
                              >✅ Completed</span
                            >
                            <span
                              v-else-if="
                                task.task_user_status === 'Not Completed'
                              "
                              >❌ Not Completed</span
                            >
                            <span v-else>{{ task.task_user_status }}</span>
                          </span>
                        </td>

                        <!-- Task Status (text) -->
                        <td>{{ task.task_status }}</td>

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
                          <span v-else>{{
                            formatDateTime(task.start_date)
                          }}</span>
                        </td>

                        <!-- End Date -->
                        <td
                          class="text-limit"
                          :title="formatDateTime(task.end_date)"
                        >
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
                          <span v-else>{{
                            formatDateTime(task.end_date)
                          }}</span>
                        </td>

                        <!-- Actual Time -->
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
                            <span
                              v-if="recentlyUpdated[task.id]"
                              class="success-indicator"
                              title="Task updated successfully"
                              ><i class="fa fa-check-circle text-success"></i
                            ></span>
                            <button
                              class="btn btn-sm btn-outline-secondary dropdown-toggle"
                              type="button"
                              @click.stop="toggleDropdown(task.id)"
                              title="More actions"
                            >
                              <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div
                              class="custom-dropdown"
                              :class="{ show: activeDropdown === task.id }"
                            >
                              <div
                                class="dropdown-menu custom-dropdown-menu"
                                :class="{ show: activeDropdown === task.id }"
                              >
                                <a
                                  class="dropdown-item text-info"
                                  href="#"
                                  @click.prevent="
                                    openDescriptionModal(task), closeDropdown()
                                  "
                                  ><i class="fa fa-file-text"></i
                                  ><span>Description</span></a
                                >
                                <a
                                  class="dropdown-item text-warning"
                                  href="#"
                                  @click.prevent="
                                    openTaskReviewModal(task), closeDropdown()
                                  "
                                  ><i class="fa fa-star"></i
                                  ><span>Review</span></a
                                >
                                <a
                                  class="dropdown-item text-danger"
                                  href="#"
                                  @click.prevent="
                                    removeTask(task), closeDropdown()
                                  "
                                  :title="'Delete task'"
                                  ><i class="fa fa-trash"></i
                                  ><span>Delete</span></a
                                >
                                <router-link
                                  target="_blank"
                                  class="dropdown-item text-primary"
                                  :to="`/tasks/details/${task.slug}`"
                                  ><i class="fa fa-pencil"></i
                                  ><span>Details</span></router-link
                                >
                              </div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </template>
                  </template>
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
                  <input
                    type="datetime-local"
                    id="startDate"
                    v-model="newTask.start_date"
                    class="form-control text-dark"
                    placeholder="Start Date & Time"
                  />
                </div>
                <div class="form-group">
                  <input
                    type="datetime-local"
                    id="endDate"
                    v-model="newTask.end_date"
                    :min="newTask.start_date"
                    class="form-control text-dark"
                    placeholder="End Date & Time"
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
              :disabled="
                !newTask.title || isCreatingTask || isNewTaskDateInvalid
              "
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
              <!-- <text-editor :name="'description'" :value="descriptionText" @input="descriptionText = $event" /> -->

              <textarea
                id="taskDescriptionText"
                v-model="descriptionText"
                class="form-control description-textarea"
                rows="8"
                placeholder="Enter task description... (HTML tags will be converted to plain text)"
                :disabled="isSavingDescription"
                style="
                  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                  line-height: 1.5;
                  resize: vertical;
                "
              ></textarea>

              <div class="description-info">
                <small class="text-muted">
                  <i class="fa fa-info-circle"></i>
                  Content will be saved as plain text. Use line breaks for
                  formatting.
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

      <!-- Task Review Modal -->
      <div
        v-if="showTaskReviewModalFlag"
        class="modal-overlay"
        :class="{ 'dark-mode': isDarkMode }"
        @click="closeTaskReviewModal"
      >
        <div
          class="modal-content review-modal dark-mode"
          :class="{ 'dark-mode': isDarkMode }"
          @click.stop
        >
          <div class="modal-header">
            <h3>
              <i class="fa fa-star me-2"></i>
              Task Review
            </h3>
            <div class="task-info">
              <span class="task-title">{{ selectedTaskForReview?.title }}</span>
              <span class="task-id">#{{ selectedTaskForReview?.id }}</span>
            </div>
            <button class="modal-close-btn" @click="closeTaskReviewModal">
              <i class="fa fa-times"></i>
            </button>
          </div>
          <div class="modal-body">
            <div class="review-form">
              <div class="form-group">
                <label for="reviewTaskStatus">Task Status</label>
                <select
                  id="reviewTaskStatus"
                  v-model="reviewData.task_status"
                  class="form-control"
                  :disabled="isSavingReview"
                >
                  <option value="Pending">📝 Pending</option>
                  <option value="In Progress">⚡ In Progress</option>
                  <option value="Completed">✅ Completed</option>
                  <option value="Not Completed">❌ Not Completed</option>
                  <option value="On Hold">⏸️ On Hold</option>
                  <option value="Cancelled">❌ Cancelled</option>
                </select>
              </div>

              <div class="form-group">
                <label for="reviewRating">Rating</label>
                <div class="rating-container">
                  <div class="star-rating">
                    <span
                      v-for="star in 5"
                      :key="star"
                      class="star clickable"
                      :class="{
                        filled: star <= reviewData.rating,
                        hover: star <= hoveredRating,
                      }"
                      @click="setRating(star)"
                      @mouseenter="hoveredRating = star"
                      @mouseleave="hoveredRating = 0"
                    >
                      <i class="fa fa-star"></i>
                    </span>
                  </div>
                  <span class="rating-text">
                    {{ reviewData.rating }}/5
                    <small class="text-muted">
                      ({{ getRatingText(reviewData.rating) }})
                    </small>
                  </span>
                </div>
              </div>

              <div
                v-for="review in taskReviews"
                :key="review.id"
                class="review-item"
              >
                <!-- Review Content -->
                <div class="review-content">
                  <div class="review-header">
                    <div class="reviewer-info">
                      <div class="reviewer-avatar">
                        <i class="fa fa-user-circle"></i>
                      </div>
                      <div class="reviewer-details text-dark">
                        <h6 class="reviewer-name text-dark">
                          {{ getReviewerDisplayName(review) }}
                        </h6>
                        <span class="review-date">{{
                          formatReviewDate(review.created_at)
                        }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="review-text text-dark">
                    <p>{{ review.comment || "No comment provided." }}</p>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="reviewComments">Review Comments (Optional)</label>
                <textarea
                  id="reviewComments"
                  v-model="reviewData.comments"
                  class="form-control"
                  rows="4"
                  placeholder="Add your review comments here..."
                  :disabled="isSavingReview"
                ></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="closeTaskReviewModal"
              :disabled="isSavingReview"
            >
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-primary"
              @click="saveTaskReview"
              :disabled="isSavingReview"
            >
              <i v-if="isSavingReview" class="fa fa-spinner fa-spin"></i>
              {{ isSavingReview ? "Saving..." : "Save Review" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/** plugins */
import { mapActions, mapWritableState, mapState } from "pinia";
import setup from "../setup";
import { store as data_store } from "../store";
import { auth_store } from "../../../../../../GlobalStore/auth_store";
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
      collapsedDates: {}, // Track which dates are collapsed
      projects: [], // List of projects for dropdowns
      selectedProject: null, // Currently selected project for filtering

      // Status filtering
      selectedDevStatus: null, // Currently selected dev status for filtering
      selectedTaskStatus: null, // Currently selected task status for filtering

      // User filtering
      assignedUsers: [], // List of users assigned to tasks in current project
      selectedUser: null, // Currently selected user for filtering
      userTaskCounts: {}, // Task counts per user

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

      // Task Review Modal
      showTaskReviewModalFlag: false,
      selectedTaskForReview: null,
      reviewData: {
        task_status: "",
        rating: 0,
        comments: "",
      },
      hoveredRating: 0,
      isSavingReview: false,
      taskReviews: [],

      // Dropdown state
      activeDropdown: null, // Track which dropdown is open

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
      filterStartDate: "", // Start date filter
      filterEndDate: "", // End date filter
      searchQuery: "", // Search query for filtering tasks
      originalAllTasks: [], // Store original task data before filtering

      // Team members section collapse state
      isTeamMembersCollapsed: false, // Track if team members section is collapsed

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
      }
    }
    // Don't auto-select any project if no query param - show all tasks initially

    // Get task groups for the dropdown
    await this.get_all_task_groups();
    // Get all tasks initially (not project-specific)
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
    // Add event listeners for scroll and resize to reposition dropdowns
    document.addEventListener("scroll", this.handleScroll, true);
    window.addEventListener("resize", this.handleResize);

    // Load custom group order for the selected project (if any)
    if (this.selectedProject) {
      this.loadGroupOrder();
    }
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
    document.removeEventListener("scroll", this.handleScroll, true);
    window.removeEventListener("resize", this.handleResize);

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

    // Date range filtering methods
    filterTasksByDateRange() {
      console.log("Filtering tasks by date range:", {
        start: this.filterStartDate,
        end: this.filterEndDate,
      });
      // The filtering logic is handled by the computed property
      // This method can be used for additional actions when date range changes
    },

    clearDateFilters() {
      this.filterStartDate = "";
      this.filterEndDate = "";
      console.log("Date range filters cleared");
    },

    // Search filtering methods
    filterTasksBySearch() {
      console.log("Filtering tasks by search query:", this.searchQuery);
      // The filtering logic is handled by the computed property
      // This method can be used for additional actions when search changes
    },

    clearSearch() {
      this.searchQuery = "";
      console.log("Search filter cleared");
    },

    // Clear all filters method
    clearAllFilters() {
      this.selectedProject = null;
      this.selectedUser = null;
      this.selectedDevStatus = null;
      this.selectedTaskStatus = null;
      this.filterStartDate = "";
      this.filterEndDate = "";
      this.searchQuery = "";
      this.selectedDate = "";
      console.log("All filters cleared");

      // Refresh tasks to show all tasks
      this.get_all_tasks();
    },

    // Format date for display
    formatDate(dateString) {
      if (!dateString) return "";
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        weekday: "short",
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },

    // Team members section methods
    toggleTeamMembers() {
      this.isTeamMembersCollapsed = !this.isTeamMembersCollapsed;
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
      // If no project is selected, get all tasks from all projects
      const projectId = this.selectedProject?.id || "";
      console.log("Fetching tasks for project:", projectId || "ALL PROJECTS");
      console.log("Selected project object:", this.selectedProject);

      let response = await axios.get(
        `task?get_all=1${projectId ? `&project_id=${projectId}` : ""}`
      );
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

      // Get assigned users after tasks are loaded
      this.getAssignedUsers();

      // Default to showing tasks date-wise by start date if no explicit sort is set
      // (user can still change sort using table headers)
      if (!this.sortField) {
        this.sortField = "start_date";
        this.sortDirection = "desc"; // earliest first; change to 'desc' for newest first
        console.log(
          `Default sorting applied: ${this.sortField} (${this.sortDirection})`
        );
      }
    },

    get_all_users: async function () {
      try {
        let response = await axios.get(`user?get_all=1`);
        if (response.data && response.data.data) {
          this.assignedUsers = response.data.data;
          console.log("All users loaded:", this.assignedUsers);
        } else if (response.data && Array.isArray(response.data)) {
          this.assignedUsers = response.data;
          console.log("All users loaded:", this.assignedUsers);
        }
      } catch (error) {
        console.error("Error fetching users:", error);
        // Fallback to assigned users method if API fails
        this.getAssignedUsers();
      }
    },

    get_all_projects: async function (resetSelection = true) {
      let response = await axios.get(`project?get_all=1`);
      this.projects = response.data;

      // Only reset selected project if explicitly requested (default behavior)
      if (resetSelection) {
        this.selectedProject = null;
        console.log("Projects loaded, no project auto-selected");
      } else {
        console.log("Projects reloaded for filtering purposes");
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

      // When a specific project is selected, fetch only that project's tasks
      this.get_all_tasks();
    },

    // Dev Status selection methods
    selectDevStatus(devStatus, event) {
      this.selectedDevStatus = devStatus;
      console.log("Selected dev status:", devStatus);

      // Add ripple effect
      if (event && event.target) {
        this.addRippleEffect(event.target);
      }

      // Trigger filtering when dev status changes
      // The filtering will be handled by the computed property
    },

    // Task Status selection methods
    selectTaskStatus(taskStatus, event) {
      this.selectedTaskStatus = taskStatus;
      console.log("Selected task status:", taskStatus);

      // Add ripple effect
      if (event && event.target) {
        this.addRippleEffect(event.target);
      }

      // Trigger filtering when task status changes
      // The filtering will be handled by the computed property
    },

    // User selection methods
    async selectUser(user, event) {
      this.selectedUser = user;
      console.log("Selected user:", user);

      // Add ripple effect
      if (event && event.target) {
        this.addRippleEffect(event.target);
      }

      // When a user is selected, filter projects to show only those related to this user
      if (user) {
        // Always reload all projects first to ensure we have the complete list
        await this.get_all_projects(false); // false = don't reset selection
        // Then filter by the selected user
        this.filterProjectsByUser(user);
        // Reset project selection when user changes
        this.selectedProject = null;
      } else {
        // If "All Users" is selected, show all projects
        await this.get_all_projects(true); // true = reset selection
      }
    },

    // Filter projects to show only those where the selected user has tasks
    filterProjectsByUser(user) {
      if (!this.all?.data || !user) return;

      // Get unique project IDs where this user has tasks
      const userProjectIds = new Set();
      this.all.data.forEach((task) => {
        if (task.user && task.user.id === user.id && task.project_id) {
          // Handle project_id as object structure: {id: 2, name: "Project Name", ...}
          if (typeof task.project_id === "object" && task.project_id.id) {
            userProjectIds.add(task.project_id.id);
          }
          // Handle project_id as direct number
          else if (typeof task.project_id === "number") {
            userProjectIds.add(task.project_id);
          }
        }
      });

      console.log(
        `User ${user.name} has tasks in project IDs:`,
        Array.from(userProjectIds)
      );

      // Filter projects to show only those where user has tasks
      const allProjects = this.projects.data || this.projects;
      const userProjects = allProjects.filter((project) =>
        userProjectIds.has(project.id)
      );

      // Update projects to show only user-related projects
      this.projects = { ...this.projects, data: userProjects };

      console.log(
        `Filtered ${userProjects.length} projects for user ${user.name}:`,
        userProjects.map((p) => p.name)
      );
    },

    // Get project name for a task - handles your project object structure
    getTaskProjectName(task) {
      // Check if task has project_id as an object (your structure)
      if (
        task.project_id &&
        typeof task.project_id === "object" &&
        task.project_id.name
      ) {
        return task.project_id.name;
      }

      // Check if task has separate project object
      if (task.project && task.project.name) {
        return task.project.name;
      }

      // If task has project_id as number, find the project name from projects list
      if (task.project_id && typeof task.project_id === "number") {
        const allProjects = this.projects.data || this.projects;
        const project = allProjects.find((p) => p.id === task.project_id);
        if (project) {
          return project.name;
        }
      }

      // Fallback to selected project if available
      if (this.selectedProject) {
        return this.selectedProject.name;
      }

      return null;
    },

    // Get users assigned to tasks in current project
    async getAssignedUsers() {
      console.log("🔍 Getting users assigned to tasks...");
      console.log("Available tasks:", this.all?.data?.length || 0);

      if (!this.all?.data || this.all.data.length === 0) {
        this.assignedUsers = [];
        this.userTaskCounts = { all: 0 };
        console.log("❌ No tasks available, no users to extract");
        return;
      }

      // Extract unique users from tasks
      const userMap = new Map();
      const taskCounts = { all: this.all.data.length };

      this.all.data.forEach((task) => {
        // Check if task has user assignment data
        if (task.user && task.user.id) {
          const userId = task.user.id;

          if (!userMap.has(userId)) {
            userMap.set(userId, {
              id: userId,
              name: task.user.name,
              email: task.user.email,
              avatar: task.user.avatar,
              profile_image: task.user.profile_image,
              image: task.user.image,
              role: task.user.role,
              designation: task.user.designation,
              status: task.user.status || "active",
              // Add any other user properties you need
            });
            taskCounts[userId] = 0;
          }

          taskCounts[userId]++;
        }
      });

      this.assignedUsers = Array.from(userMap.values());
      this.userTaskCounts = taskCounts;

      console.log("✅ Assigned users extracted:", this.assignedUsers);
      console.log("✅ Task counts calculated:", this.userTaskCounts);

      // Log sample task structure for debugging
      if (this.all.data.length > 0) {
        console.log("📋 Sample task structure:", {
          firstTask: this.all.data[0],
          hasUser: !!this.all.data[0].user,
          userStructure: this.all.data[0].user,
        });
      }
    },

    // Update task counts for all users
    updateUserTaskCounts() {
      if (!this.all?.data || this.all.data.length === 0) {
        this.userTaskCounts = {};
        return;
      }

      const taskCounts = { all: this.all.data.length };

      // Initialize all users with 0 task count
      this.assignedUsers.forEach((user) => {
        taskCounts[user.id] = 0;
      });

      // Count tasks for each user
      this.all.data.forEach((task) => {
        if (task.user && task.user.id) {
          const userId = task.user.id;
          if (taskCounts[userId] !== undefined) {
            taskCounts[userId]++;
          }
        }
      });

      this.userTaskCounts = taskCounts;
      console.log("Updated task counts:", this.userTaskCounts);
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
      this.descriptionText = this.convertHtmlToPlainText(
        task.description || ""
      );
      this.showDescriptionModalFlag = true;
    },

    closeDescriptionModal() {
      this.showDescriptionModalFlag = false;
      this.selectedTaskForDescription = null;
      this.descriptionText = "";
      this.isSavingDescription = false;
    },

    // Convert HTML to plain text for display in textarea
    convertHtmlToPlainText(html) {
      if (!html) return "";

      // Create a temporary div element to parse HTML
      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = html;

      // Extract text content and replace common HTML elements with appropriate text
      let plainText = tempDiv.textContent || tempDiv.innerText || "";

      // Additionally, manually handle some common HTML patterns if needed
      plainText = html
        .replace(/<p[^>]*>/gi, "") // Remove opening p tags
        .replace(/<\/p>/gi, "\n") // Replace closing p tags with line breaks
        .replace(/<br[^>]*>/gi, "\n") // Replace br tags with line breaks
        .replace(/<[^>]*>/g, "") // Remove any remaining HTML tags
        .replace(/&nbsp;/gi, " ") // Replace non-breaking spaces
        .replace(/&amp;/gi, "&") // Replace encoded ampersands
        .replace(/&lt;/gi, "<") // Replace encoded less than
        .replace(/&gt;/gi, ">") // Replace encoded greater than
        .replace(/&quot;/gi, '"') // Replace encoded quotes
        .trim(); // Remove leading/trailing whitespace

      return plainText;
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

    // Task Review Modal Methods
    async openTaskReviewModal(task) {
      this.selectedTaskForReview = task;
      this.reviewData = {
        task_status: task.task_status,
        rating: task.rating || 0,
        comments: task.review_comments || "",
      };
      this.hoveredRating = 0;
      this.showTaskReviewModalFlag = true;
      // Fetch task reviews from API
      this.taskReviews = [];
      if (task && task.id) {
        try {
          const response = await axios.get(
            `/task/task-reviews-by-task-id/${task.id}`
          );
          console.log("Task reviews response:", response.data);
          if (response.data.status === "success") {
            this.taskReviews = response.data.data;
          } else {
            this.taskReviews = [];
          }
        } catch (error) {
          this.taskReviews = [];
          console.error("Failed to load task reviews:", error);
        }
      }
    },

    closeTaskReviewModal() {
      this.showTaskReviewModalFlag = false;
      this.selectedTaskForReview = null;
      this.reviewData = {
        task_status: "",
        rating: 0,
        comments: "",
      };
      this.hoveredRating = 0;
      this.isSavingReview = false;
    },

    setRating(rating) {
      this.reviewData.rating = rating;
    },

    getRatingText(rating) {
      const ratingTexts = {
        0: "No rating",
        1: "Poor",
        2: "Fair",
        3: "Good",
        4: "Very Good",
        5: "Excellent",
      };
      return ratingTexts[rating] || "No rating";
    },

    async saveTaskReview() {
      if (!this.selectedTaskForReview || !this.selectedTaskForReview.slug) {
        window.s_warning("Cannot save review. Task not found.");
        return;
      }

      this.isSavingReview = true;

      try {
        const payload = {
          slug: this.selectedTaskForReview.slug,
          title: this.selectedTaskForReview.title,
          task_group_id: this.selectedTaskForReview.task_group_id,
          priority: this.selectedTaskForReview.priority,
          task_status: this.reviewData.task_status,
          start_date: this.selectedTaskForReview.start_date,
          end_date: this.selectedTaskForReview.end_date,
          rating: this.reviewData.rating,
          review_comments: this.reviewData.comments.trim(),
          description: this.selectedTaskForReview.description || "",
        };

        const response = await axios.post(
          `/task/update/${this.selectedTaskForReview.slug}`,
          payload
        );

        if (response.data.status === "success") {
          // Update the task in local data
          const taskIndex = this.all.data.findIndex(
            (t) => t.id === this.selectedTaskForReview.id
          );
          if (taskIndex !== -1) {
            this.all.data[taskIndex].task_status = this.reviewData.task_status;
            this.all.data[taskIndex].rating = this.reviewData.rating;
            this.all.data[taskIndex].review_comments =
              this.reviewData.comments.trim();
          }

          // Update the selected task object
          this.selectedTaskForReview.task_status = this.reviewData.task_status;
          this.selectedTaskForReview.rating = this.reviewData.rating;
          this.selectedTaskForReview.review_comments =
            this.reviewData.comments.trim();

          // Show success indicator
          this.recentlyUpdated[this.selectedTaskForReview.id] = true;
          setTimeout(() => {
            this.recentlyUpdated[this.selectedTaskForReview.id] = false;
          }, 3000);

          window.s_alert("Task review saved successfully!");
          this.closeTaskReviewModal();
        } else {
          window.s_warning(
            response.data?.message || "Failed to save task review"
          );
        }
      } catch (error) {
        console.error("Error saving task review:", error);

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
          window.s_warning("Error saving task review");
        }
      } finally {
        this.isSavingReview = false;
      }
    },

    // Dropdown control methods
    toggleDropdown(taskId) {
      if (this.activeDropdown === taskId) {
        this.activeDropdown = null;
      } else {
        this.activeDropdown = taskId;
      }
    },

    closeDropdown() {
      this.activeDropdown = null;
    },

    // Handle scroll events to reposition dropdown
    handleScroll() {
      if (this.activeDropdown) {
        // Reposition the active dropdown
        this.$nextTick(() => {
          const button = document.querySelector(
            `[data-task-id="${this.activeDropdown}"] .dropdown-toggle`
          );
          const dropdown = document.querySelector(
            `[data-task-id="${this.activeDropdown}"] .custom-dropdown-menu`
          );

          if (button && dropdown) {
            const buttonRect = button.getBoundingClientRect();
            // dropdown.style.top = buttonRect.bottom + 2 + "px";
            // dropdown.style.left = buttonRect.left - 150 + "px";

            // Check if dropdown is still visible
            if (buttonRect.bottom < 0 || buttonRect.top > window.innerHeight) {
              this.activeDropdown = null;
            }
          }
        });
      }
    },

    // Handle window resize to reposition dropdown
    handleResize() {
      if (this.activeDropdown) {
        this.handleScroll(); // Reuse scroll logic for repositioning
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
          this.get_all_projects(),
          this.get_all_task_groups(),
          this.get_all_tasks(),
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

      // Close dropdowns when clicking outside
      const clickedDropdown = event.target.closest(".custom-dropdown");
      if (!clickedDropdown) {
        this.activeDropdown = null;
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

    // Calculate time difference between start and end dates
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

        // If end date is before start date
        if (end.getTime() < start.getTime()) {
          return "Invalid range";
        }

        // Calculate difference in milliseconds
        const diffMs = end.getTime() - start.getTime();

        // Convert to different units
        const diffMinutes = Math.floor(diffMs / (1000 * 60));
        const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

        // Return appropriate format based on duration
        if (diffDays > 0) {
          const remainingHours = diffHours % 24;
          if (remainingHours > 0) {
            const remainingMinutes = diffMinutes % 60;
            if (remainingMinutes > 0) {
              return `${diffDays}d ${remainingHours}h ${remainingMinutes}m`;
            }
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
          return `${diffMinutes} minute${diffMinutes > 1 ? "s" : ""}`;
        } else {
          return "0 minutes";
        }
      } catch (error) {
        console.error("Error calculating time difference:", error);
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

    // Date collapse/expand - collapses/expands all groups under the date
    toggleDate(dateKey) {
      this.collapsedDates[dateKey] = !this.collapsedDates[dateKey];
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

    getReviewerDisplayName(review) {
      if (!review) return "Anonymous";

      // If we have the current user ID and it matches the reviewer
      if (
        this.auth_info?.id &&
        review.user_id &&
        review.user_id == this.auth_info.id
      ) {
        return "Me";
      }

      // Otherwise show "Author" or the reviewer name if available
      return review.reviewer_name || "User";
    },

    formatReviewDate(dateString) {
      if (!dateString) return "";

      try {
        const date = new Date(dateString);
        return date.toLocaleString("en-US", {
          year: "numeric",
          month: "short",
          day: "numeric",
          hour: "2-digit",
          minute: "2-digit",
        });
      } catch (error) {
        return dateString;
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
        this.flatGroups[groupIndex]?.name
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

    // Helper: compute flat index from nested date/group indices
    getFlatGroupIndex(dateIndex, groupIndex) {
      const dates = this.groupedTasks || [];
      let idx = 0;
      for (let i = 0; i < dateIndex; i++) {
        idx += (dates[i].groups || []).length;
      }
      return idx + groupIndex;
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
      const groups = [...this.flatGroups];

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
          this.flatGroups.map((g) => ({ id: g.id, name: g.name }))
        );
      });
    },

    // Test method for debugging group reorder
    testGroupReorder() {
      console.log("🧪 Testing group reorder functionality");
      console.log(
        "Current groups:",
        this.flatGroups.map((g) => ({ id: g.id, name: g.name }))
      );
      console.log("Current custom order:", this.customGroupOrder);

      if (this.flatGroups.length >= 2) {
        // Test reordering: move first group to second position
        console.log("🔄 Moving first group to second position");
        this.reorderGroups(0, 1);
        console.log(
          "After reorder:",
          this.flatGroups.map((g) => ({ id: g.id, name: g.name }))
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

    // User interface helper methods
    getInitials(name) {
      if (!name) return "??";
      return name
        .split(" ")
        .map((word) => word.charAt(0).toUpperCase())
        .slice(0, 2)
        .join("");
    },

    getUserStatus(user) {
      // Return status class based on user status or random for demo
      const statuses = ["online", "away", "busy", "offline"];
      if (user.status === "active") return "online";
      if (user.status === "inactive") return "offline";
      // Default to online for active users
      return "online";
    },

    handleImageError(event) {
      // Fallback when user image fails to load
      const img = event.target;
      const userCard = img.closest(".user-card");
      const userName =
        userCard?.querySelector(".user-name")?.textContent || "User";

      // Create a placeholder with initials
      const placeholder = document.createElement("div");
      placeholder.className = "user-avatar-placeholder";
      placeholder.innerHTML = `<span>${this.getInitials(userName)}</span>`;

      // Replace the failed image with the placeholder
      img.parentNode.replaceChild(placeholder, img);
    },

    viewUserProfile(user) {
      // Handle user profile view - you can implement this based on your requirements
      console.log("View profile for user:", user);
      window.s_alert(
        `View profile for ${user.name} - Feature coming soon!`,
        "info"
      );
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
    ...mapState(auth_store, {
      auth_info: "auth_info",
    }),
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

    // Get filtered task count for display
    filteredTaskCount() {
      return this.filteredTasks ? this.filteredTasks.length : 0;
    },

    // Tasks filtered by search, date, and user
    filteredTasks() {
      let tasks = this.originalAllTasks || this.all?.data || [];

      // Filter by date if date is selected (legacy single date filter)
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

      // Filter by date range if start or end date filters are selected
      if (this.filterStartDate || this.filterEndDate) {
        tasks = tasks.filter((task) => {
          const taskStartDate = new Date(task.start_date);
          const taskEndDate = new Date(task.end_date);

          // Set time for accurate comparison
          taskStartDate.setHours(0, 0, 0, 0);
          taskEndDate.setHours(23, 59, 59, 999);

          let withinRange = true;

          // Check start date filter
          if (this.filterStartDate) {
            const filterStart = new Date(this.filterStartDate);
            filterStart.setHours(0, 0, 0, 0);
            // Task must start on or after the filter start date
            withinRange = withinRange && taskStartDate >= filterStart;
          }

          // Check end date filter
          if (this.filterEndDate) {
            const filterEnd = new Date(this.filterEndDate);
            filterEnd.setHours(23, 59, 59, 999);
            // Task must end on or before the filter end date
            withinRange = withinRange && taskEndDate <= filterEnd;
          }

          return withinRange;
        });
      }

      // Filter by selected user if user is selected
      if (this.selectedUser) {
        tasks = tasks.filter((task) => {
          return task.user && task.user.id === this.selectedUser.id;
        });
      }

      // Filter by selected dev status if dev status is selected
      if (this.selectedDevStatus) {
        tasks = tasks.filter((task) => {
          return task.task_user_status === this.selectedDevStatus;
        });
      }

      // Filter by selected task status if task status is selected
      if (this.selectedTaskStatus) {
        tasks = tasks.filter((task) => {
          return task.task_status === this.selectedTaskStatus;
        });
      }

      // Filter by search query if needed
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        tasks = tasks.filter((task) => {
          // Search in task title
          const titleMatch =
            task.title && task.title.toLowerCase().includes(query);

          // Search in task description
          const descriptionMatch =
            task.description && task.description.toLowerCase().includes(query);

          // Search in priority
          const priorityMatch =
            task.priority && task.priority.toLowerCase().includes(query);

          // Search in task status
          const taskStatusMatch =
            task.task_status && task.task_status.toLowerCase().includes(query);

          // Search in dev status
          const devStatusMatch =
            task.task_user_status &&
            task.task_user_status.toLowerCase().includes(query);

          // Search in assigned user name
          const userMatch =
            task.user &&
            task.user.name &&
            task.user.name.toLowerCase().includes(query);

          // Search in project name
          const projectMatch =
            task.project &&
            task.project.name &&
            task.project.name.toLowerCase().includes(query);

          // Search in task ID
          const idMatch = task.id && task.id.toString().includes(query);

          return (
            titleMatch ||
            descriptionMatch ||
            priorityMatch ||
            taskStatusMatch ||
            devStatusMatch ||
            userMatch ||
            projectMatch ||
            idMatch
          );
        });
      }

      return tasks;
    },

    groupedTasks() {
      // Start with filtered tasks
      const tasks = (this.filteredTasks || []).slice();

      // Helper to get local YYYY-MM-DD from a date string
      const toDateKey = (dt) => {
        if (!dt) return "No Date";
        const d = new Date(dt);
        if (isNaN(d.getTime())) return "No Date";
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, "0");
        const day = String(d.getDate()).padStart(2, "0");
        return `${y}-${m}-${day}`;
      };

      // Build composite groups keyed by date + task_group
      const composite = {};

      tasks.forEach((task) => {
        const dateKey = toDateKey(task.start_date);

        // determine task group id/name
        const rawGroupId = task.task_group_id || "ungrouped";
        let groupName = "Ungrouped Tasks";
        if (task.task_group_id) {
          const existing = this.task_groups.find(
            (g) => g.id == task.task_group_id
          );
          if (existing)
            groupName =
              task.task_group_name || task.task_group?.name || existing.name;
        }

        const compositeKey = `${dateKey}::${rawGroupId}`;

        if (!composite[compositeKey]) {
          composite[compositeKey] = {
            id: compositeKey,
            // Keep the group name without the date prefix; date is rendered as a separate header
            name: groupName,
            date: dateKey,
            groupId: rawGroupId,
            tasks: [],
            collapsed: this.collapsedGroups[compositeKey] || false,
          };
        }

        composite[compositeKey].tasks.push(task);
      });

      // Convert to array
      const allGroups = Object.values(composite);

      // Sort tasks inside each composite group by start_date desc
      allGroups.forEach((g) => {
        g.tasks.sort((a, b) => {
          const aT = a && a.start_date ? new Date(a.start_date).getTime() : 0;
          const bT = b && b.start_date ? new Date(b.start_date).getTime() : 0;
          return bT - aT;
        });
      });

      // Sort groups by date (newest first), with 'No Date' last, then by group name
      allGroups.sort((a, b) => {
        const aDate = a.date === "No Date" ? -1 : new Date(a.date).getTime();
        const bDate = b.date === "No Date" ? -1 : new Date(b.date).getTime();
        if (aDate !== bDate) return bDate - aDate;
        // secondary: group name
        return (a.name || "").localeCompare(b.name || "");
      });

      // Build nested date -> groups structure
      const dateMap = {};
      allGroups.forEach((g) => {
        const dateKey = g.date || "No Date";
        if (!dateMap[dateKey]) dateMap[dateKey] = { date: dateKey, groups: [] };
        dateMap[dateKey].groups.push(g);
      });

      // Convert to array and sort dates (newest first, 'No Date' last)
      const dateGroups = Object.values(dateMap).sort((a, b) => {
        const aDate = a.date === "No Date" ? -1 : new Date(a.date).getTime();
        const bDate = b.date === "No Date" ? -1 : new Date(b.date).getTime();
        return bDate - aDate;
      });

      return dateGroups;
    },

    // Flattened view of groups for drag/reorder operations and legacy compatibility
    flatGroups() {
      const dates = this.groupedTasks || [];
      return dates.flatMap((d) => d.groups || []);
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

/* Date Filter Styles */
.date-controls-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 12px;
}

.filter-controls-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 8px;
}

.action-buttons-group {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.date-filter-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 120px;
  flex: 1;
}

@media (max-width: 768px) {
  .date-controls-row {
    gap: 8px;
    margin-bottom: 16px;
  }

  .filter-controls-row {
    justify-content: space-between;
    gap: 6px;
  }

  .date-filter-group {
    min-width: 100px;
    flex: 1;
  }

  .search-filter-group {
    min-width: 150px;
    flex: 2;
  }

  .action-buttons-group {
    width: 100%;
    justify-content: center;
    margin-top: 8px;
  }
}

@media (max-width: 576px) {
  .date-controls-row {
    flex-direction: column;
    gap: 12px;
  }

  .filter-controls-row {
    width: 100%;
    justify-content: space-around;
  }

  .date-filter-group {
    min-width: 80px;
  }

  .search-filter-group {
    width: 100%;
    order: -1;
    margin-bottom: 8px;
  }
}

.search-filter-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 200px;
}

.search-filter-group .input-group {
  position: relative;
}

.search-filter-group .input-group-text {
  background-color: #f8f9fa;
  border: 1px solid #ced4da;
  border-right: none;
  padding: 0.375rem 0.75rem;
  color: #6c757d;
}

.search-filter-group .search-input {
  border-left: none;
  flex: 1;
}

.search-filter-group .search-input:focus {
  border-color: #86b7fe;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.search-filter-group .btn-clear-search {
  border-left: none;
  background-color: transparent;
  border-color: #ced4da;
  color: #6c757d;
  padding: 0.375rem 0.75rem;
}

.search-filter-group .btn-clear-search:hover {
  background-color: #f8f9fa;
  color: #dc3545;
}

.date-picker {
  font-size: 13px;
  padding: 6px 8px;
  border-radius: 6px;
  border: 1px solid #ced4da;
  transition: all 0.2s ease;
}

.date-picker:focus {
  border-color: #1e3c72;
  box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
}

.btn-clear-date {
  padding: 6px 10px;
  font-size: 13px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.btn-clear-date:hover {
  background-color: #dc3545;
  border-color: #dc3545;
  color: white;
}

/* Professional User Tabs Section */
.professional-user-tabs {
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.user-section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  flex-wrap: wrap;
  gap: 12px;
}

@media (max-width: 768px) {
  .user-section-header {
    flex-direction: column;
    align-items: stretch;
    gap: 16px;
  }

  .section-actions {
    width: 100%;
  }

  .section-title-container {
    text-align: center;
  }
}

@media (max-width: 576px) {
  .user-section-header {
    padding-bottom: 16px;
    margin-bottom: 20px;
  }
}

.section-title-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.section-icon {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 18px;
}

.section-title-content .section-title {
  color: white;
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.section-title-content .section-subtitle {
  color: rgba(255, 255, 255, 0.8);
  font-size: 13px;
}

.section-actions .reload-btn {
  border-color: rgba(255, 255, 255, 0.3);
  color: white;
  transition: all 0.3s ease;
}

.section-actions .reload-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: white;
}

.section-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

.section-actions .collapse-btn {
  border-color: rgba(255, 255, 255, 0.3);
  color: white;
  transition: all 0.3s ease;
  padding: 4px 8px;
  min-width: auto;
}

.section-actions .collapse-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: white;
  transform: scale(1.05);
}

.section-actions .collapse-btn i {
  font-size: 14px;
}

/* Project Dropdown Styles */
.section-actions .project-dropdown-btn {
  border-color: rgba(255, 255, 255, 0.3);
  color: white;
  transition: all 0.3s ease;
  font-size: 13px;
  padding: 10px 12px;
  min-width: 140px;
  text-align: left;
}

.section-actions .project-dropdown-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: white;
}

.section-actions .project-dropdown-btn:focus {
  background: rgba(255, 255, 255, 0.2);
  border-color: white;
  box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
}

/* Dev Status Dropdown Styles */
.section-actions .dev-status-dropdown-btn {
  border-color: rgba(255, 255, 255, 0.3);
  color: white;
  transition: all 0.3s ease;
  font-size: 13px;
  padding: 10px 12px;
  min-width: 140px;
  text-align: left;
  background: linear-gradient(
    135deg,
    rgba(52, 152, 219, 0.1) 0%,
    rgba(41, 128, 185, 0.1) 100%
  );
  position: relative;
  overflow: hidden;
}

.section-actions .dev-status-dropdown-btn::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.2),
    transparent
  );
  transition: left 0.5s;
}

.section-actions .dev-status-dropdown-btn:hover::before {
  left: 100%;
}

.section-actions .dev-status-dropdown-btn:hover {
  background: linear-gradient(
    135deg,
    rgba(52, 152, 219, 0.2) 0%,
    rgba(41, 128, 185, 0.2) 100%
  );
  border-color: #3498db;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.section-actions .dev-status-dropdown-btn:focus {
  background: linear-gradient(
    135deg,
    rgba(52, 152, 219, 0.2) 0%,
    rgba(41, 128, 185, 0.2) 100%
  );
  border-color: #3498db;
  box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
}

/* Task Status Dropdown Styles */
.section-actions .task-status-dropdown-btn {
  border-color: rgba(255, 255, 255, 0.3);
  color: white;
  transition: all 0.3s ease;
  font-size: 13px;
  padding: 10px 12px;
  min-width: 140px;
  text-align: left;
  background: linear-gradient(
    135deg,
    rgba(46, 204, 113, 0.1) 0%,
    rgba(39, 174, 96, 0.1) 100%
  );
  position: relative;
  overflow: hidden;
}

.section-actions .task-status-dropdown-btn::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.2),
    transparent
  );
  transition: left 0.5s;
}

.section-actions .task-status-dropdown-btn:hover::before {
  left: 100%;
}

.section-actions .task-status-dropdown-btn:hover {
  background: linear-gradient(
    135deg,
    rgba(46, 204, 113, 0.2) 0%,
    rgba(39, 174, 96, 0.2) 100%
  );
  border-color: #2ecc71;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
}

.section-actions .task-status-dropdown-btn:focus {
  background: linear-gradient(
    135deg,
    rgba(46, 204, 113, 0.2) 0%,
    rgba(39, 174, 96, 0.2) 100%
  );
  border-color: #2ecc71;
  box-shadow: 0 0 0 0.25rem rgba(46, 204, 113, 0.25);
}

.project-dropdown-menu {
  background: white;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  max-height: 300px;
  overflow-y: auto;
  min-width: 200px;
}

/* Dev Status Dropdown Menu */
.dev-status-dropdown-menu {
  background: white;
  border: 1px solid rgba(52, 152, 219, 0.2);
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(52, 152, 219, 0.15);
  max-height: 300px;
  overflow-y: auto;
  min-width: 200px;
}

/* Task Status Dropdown Menu */
.task-status-dropdown-menu {
  background: white;
  border: 1px solid rgba(46, 204, 113, 0.2);
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(46, 204, 113, 0.15);
  max-height: 300px;
  overflow-y: auto;
  min-width: 200px;
}

.project-dropdown-menu .dropdown-item,
.dev-status-dropdown-menu .dropdown-item,
.task-status-dropdown-menu .dropdown-item {
  padding: 8px 16px;
  font-size: 14px;
  transition: all 0.2s ease;
  border: none;
  background: none;
}

.project-dropdown-menu .dropdown-item:hover,
.dev-status-dropdown-menu .dropdown-item:hover,
.task-status-dropdown-menu .dropdown-item:hover {
  background: #f8f9fa;
  color: #1e3c72;
}

.project-dropdown-menu .dropdown-item.active {
  background: #1e3c72;
  color: white;
}

.dev-status-dropdown-menu .dropdown-item.active {
  background: #3498db;
  color: white;
}

.task-status-dropdown-menu .dropdown-item.active {
  background: #2ecc71;
  color: white;
}

.project-dropdown-menu .dropdown-item.active:hover {
  background: #2a5298;
  color: white;
}

.dev-status-dropdown-menu .dropdown-item.active:hover {
  background: #2980b9;
  color: white;
}

.task-status-dropdown-menu .dropdown-item.active:hover {
  background: #27ae60;
  color: white;
}

.project-dropdown-menu .dropdown-divider,
.dev-status-dropdown-menu .dropdown-divider,
.task-status-dropdown-menu .dropdown-divider {
  margin: 4px 0;
  border-color: rgba(0, 0, 0, 0.1);
}

/* User Cards Container */
.user-cards-container {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  max-height: 300px;
  overflow-y: auto;
  padding: 4px;
  transition: all 0.3s ease;
  overflow: hidden;
}

@media (max-width: 768px) {
  .user-cards-container {
    gap: 8px;
    max-height: 250px;
    padding: 8px 0;
  }
}

@media (max-width: 576px) {
  .user-cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 12px;
    max-height: none;
    overflow-y: visible;
    padding: 8px 0;
  }
}

/* Individual User Card */
.user-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 10px;
  padding: 12px;
  min-width: 220px;
  max-width: 280px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

@media (max-width: 768px) {
  .user-card {
    min-width: 180px;
    max-width: 220px;
    padding: 10px;
    gap: 6px;
  }
}

@media (max-width: 576px) {
  .user-card {
    min-width: 100%;
    max-width: 100%;
    padding: 12px;
    gap: 8px;
  }
}

.user-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  border-color: rgba(255, 255, 255, 0.3);
}

.user-card.active {
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
  color: white;
  border-color: #4caf50;
  box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
}

.user-card.active .user-name,
.user-card.active .user-role,
.user-card.active .user-email {
  color: white;
}

.user-card.active .count-number {
  color: white;
}

.user-card.active .count-label {
  color: rgba(255, 255, 255, 0.9);
}

/* All Users Card Special Styling */
.all-users-card {
  background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.all-users-card .user-name,
.all-users-card .user-role {
  color: white;
}

.all-users-card .count-number {
  color: white;
}

.all-users-card .count-label {
  color: rgba(255, 255, 255, 0.9);
}

.all-users-card:hover {
  background: linear-gradient(135deg, #5b4cdb 0%, #9088fc 100%);
}

.all-users-card.active {
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
  border-color: #4caf50;
}

/* User Avatar Container */
.user-avatar-container {
  position: relative;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 4px;
}

.user-avatar {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid rgba(255, 255, 255, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0f0f0;
  flex-shrink: 0;
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-avatar-placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: white;
  font-size: 16px;
}

.all-users-avatar {
  background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
  color: #6c5ce7;
  font-size: 20px;
}

/* Status Indicator */
.user-status-indicator {
  position: absolute;
  top: -2px;
  right: -2px;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 2px solid white;
}

.user-status-indicator.online {
  background: #4caf50;
}

.user-status-indicator.away {
  background: #ff9800;
}

.user-status-indicator.busy {
  background: #f44336;
}

.user-status-indicator.offline {
  background: #9e9e9e;
}

/* User Info - positioned to the right of avatar */
.user-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-height: 45px;
}

.user-name {
  font-weight: 600;
  font-size: 15px;
  color: #2c3e50;
  margin-bottom: 4px;
  line-height: 1.2;
}

.user-role {
  font-size: 12px;
  color: #7f8c8d;
  line-height: 1.2;
  font-style: italic;
}

/* User Stats */
.user-stats {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.user-card.active .user-stats {
  border-top-color: rgba(255, 255, 255, 0.3);
}

.all-users-card .user-stats {
  border-top-color: rgba(255, 255, 255, 0.3);
}

.task-count {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.count-number {
  font-size: 18px;
  font-weight: bold;
  color: #2c3e50;
  line-height: 1;
}

.count-label {
  font-size: 10px;
  color: #7f8c8d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* User Actions */
.user-actions {
  display: flex;
  gap: 4px;
}

.user-action-btn {
  width: 24px;
  height: 24px;
  border: none;
  background: rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  color: #7f8c8d;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  font-size: 10px;
}

.user-action-btn:hover {
  background: rgba(0, 0, 0, 0.2);
  color: #2c3e50;
}

.user-card.active .user-action-btn {
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

.user-card.active .user-action-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

/* Empty State */
.empty-users-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  text-align: center;
  color: rgba(255, 255, 255, 0.8);
  width: 100%;
}

/* Responsive Dropdown Styles */
@media (max-width: 768px) {
  .project-dropdown-btn,
  .dev-status-dropdown-btn,
  .task-status-dropdown-btn {
    font-size: 12px;
    padding: 4px 8px;
    min-width: 80px;
  }

  .dropdown-menu {
    font-size: 13px;
  }

  .professional-user-tabs {
    padding: 12px;
    margin: 0 8px;
  }
}

@media (max-width: 576px) {
  .project-dropdown-btn,
  .dev-status-dropdown-btn,
  .task-status-dropdown-btn {
    font-size: 11px;
    padding: 3px 6px;
    min-width: 70px;
    flex: 1;
    text-align: center;
  }

  .professional-user-tabs {
    padding: 8px;
    margin: 0 4px;
    border-radius: 8px;
  }

  .section-title {
    font-size: 16px;
  }

  .section-subtitle {
    font-size: 12px;
  }
}

/* Utility class for mobile text truncation */
@media (max-width: 576px) {
  .mobile-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 80px;
  }
}

.empty-icon {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.6;
}

.empty-content h6 {
  color: white;
  margin-bottom: 8px;
  font-size: 16px;
}

.empty-content p {
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 16px;
  font-size: 14px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .user-cards-container {
    flex-direction: column;
    max-height: 250px;
  }

  .user-card {
    min-width: 100%;
    max-width: 100%;
  }

  .user-section-header {
    flex-direction: column;
    gap: 12px;
    align-items: flex-start;
  }

  .section-title-container {
    width: 100%;
  }

  .section-actions {
    width: 100%;
    justify-content: flex-start;
    flex-wrap: wrap;
  }

  .section-actions .project-dropdown-btn,
  .section-actions .dev-status-dropdown-btn,
  .section-actions .task-status-dropdown-btn {
    min-width: 120px;
    font-size: 12px;
    padding: 6px 10px;
  }
}

@media (max-width: 480px) {
  .professional-user-tabs {
    padding: 12px;
  }

  .user-card {
    padding: 10px;
  }

  .user-avatar {
    width: 40px;
    height: 40px;
    margin-right: 10px;
  }

  .user-name {
    font-size: 13px;
  }

  .user-role {
    font-size: 11px;
  }
}

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

/* Assigned User Column Styles */
.assigned-user-cell {
  display: flex;
  align-items: center;
  gap: 6px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  color: #2c3e50;
}

.user-info i {
  color: #3498db;
  font-size: 0.85rem;
}

.no-user-assigned {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
  font-style: italic;
}

.no-user-assigned i {
  color: #95a5a6;
}

/* Project Column Styles */
.project-cell {
  display: flex;
  align-items: center;
  gap: 6px;
}

.project-info {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  color: #2c3e50;
}

.project-info i {
  color: #27ae60;
  font-size: 0.85rem;
}

.no-project-assigned {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
  font-style: italic;
}

.no-project-assigned i {
  color: #95a5a6;
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

/* Task Review Modal Styles */
.review-modal .modal-content {
  max-width: 500px;
}

.rating-container {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.star-rating {
  display: flex;
  gap: 2px;
}

.star {
  font-size: 1.5rem;
  color: #ddd;
  transition: color 0.2s ease;
  cursor: pointer;
}

.star.clickable:hover,
.star.hover {
  color: #ffc107;
}

.star.filled {
  color: #ffc107;
}

.rating-text {
  font-weight: 500;
  color: #495057;
}

.dark-mode .rating-text {
  color: #e9ecef;
}

/* Actions cell specific styling */
.actions-cell {
  position: relative;
  overflow: visible !important;
}

/* Dropdown Menu Styles */
.custom-dropdown {
  position: relative;
  display: inline-block;
  z-index: 9999;
}

.custom-dropdown-menu {
  position: absolute;
  top: 20px;
  left: -250px;
  z-index: 99999;
  min-width: 200px;
  padding: 0.5rem 0;
  margin: 0.125rem 0 0;
  background-color: #fff;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 0.375rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  display: none;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: opacity 0.15s ease, visibility 0.15s ease, transform 0.15s ease;
}

.custom-dropdown-menu.show {
  display: block !important;
  opacity: 1 !important;
  visibility: visible !important;
  transform: translateY(0) !important;
}

.dropdown-item {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 0.5rem 1rem;
  clear: both;
  font-weight: 400;
  color: #212529;
  text-align: inherit;
  text-decoration: none;
  white-space: nowrap;
  background-color: transparent;
  border: 0;
  transition: background-color 0.15s ease-in-out;
  cursor: pointer;
}

.dropdown-item:hover,
.dropdown-item:focus {
  background-color: #f8f9fa;
  color: #16181b;
  text-decoration: none;
}

.dropdown-item.text-danger:hover {
  background-color: #f8d7da;
  color: #721c24 !important;
}

.dropdown-item i {
  width: 20px;
  margin-right: 8px;
  text-align: center;
  flex-shrink: 0;
}

.dropdown-item span {
  flex: 1;
}

.dropdown-divider {
  height: 0;
  margin: 0.25rem 0;
  overflow: hidden;
  border-top: 1px solid rgba(0, 0, 0, 0.15);
}

/* Dark mode dropdown styles */
.dark-mode .custom-dropdown-menu {
  background-color: #2c3e50;
  border-color: #34495e;
}

.dark-mode .dropdown-item {
  color: #ecf0f1;
}

.dark-mode .dropdown-item:hover,
.dark-mode .dropdown-item:focus {
  background-color: #34495e;
  color: #ecf0f1;
}

.dark-mode .dropdown-item.text-danger:hover {
  background-color: #e74c3c;
  color: #fff !important;
}

.dark-mode .dropdown-divider {
  border-top-color: #34495e;
}

.task-actions .dropdown-toggle::after {
  display: none;
}

.task-actions .btn {
  border: none;
  background: transparent;
  color: #6c757d;
  transition: all 0.2s ease;
}

.task-actions .btn:hover {
  color: #495057;
  background-color: #f8f9fa;
}

.dark-mode .task-actions .btn {
  color: #adb5bd;
}

.dark-mode .task-actions .btn:hover {
  color: #e9ecef;
  background-color: #495057;
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

/* Fixed Layout - Only Table Scrollable */
.google-sheets-container {
  height: 100vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.sheets-toolbar {
  flex-shrink: 0;
  position: sticky;
  top: 0;
  z-index: 100;
  background: inherit;
  border-bottom: 1px solid #dee2e6;
}

.table-container {
  flex: 1;
  overflow-y: scroll;
  overflow-x: auto;
  position: relative;
  min-height: 0; /* Important for flex child to shrink */
}

/* Ensure professional user tabs are scrollable with content */
.professional-user-tabs {
  position: sticky;
  top: -9px;
  z-index: 10000;
  background: inherit;
  margin-bottom: 1rem !important;
  padding: 0.5rem 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

/* Ensure project tabs are scrollable with content */
.sheet-tabs {
  position: sticky;
  top: 80px; /* Adjust based on user tabs height */
  z-index: 98;
  background: inherit;
  margin-bottom: 1rem !important;
  padding: 0.5rem 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

/* Ensure sort indicator is scrollable with content */
.sort-indicator {
  position: sticky;
  top: 140px; /* Adjust based on previous sections height */
  z-index: 97;
  background: inherit;
  margin-bottom: 1rem !important;
}

/* Table responsive container */
.table-responsive {
  overflow: visible;
  position: relative;
  flex: 1;
}

/* Table itself should fill available space */
.table {
  margin-bottom: 0;
}

/* Dark mode adjustments */
.dark-mode .sheets-toolbar {
  background: #2c3e50;
  border-bottom-color: #34495e;
}

.dark-mode .professional-user-tabs,
.dark-mode .sheet-tabs {
  background: #2c3e50;
  border-bottom-color: rgba(255, 255, 255, 0.1);
}

.dark-mode .sort-indicator {
  background: #2c3e50;
}

/* Filter Badge Styles */
.project-filter-badge,
.user-filter-badge,
.all-filter-badge {
  color: #ffffff;
  font-weight: 500;
}

.dev-status-filter-badge {
  color: #3498db;
  font-weight: 500;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.task-status-filter-badge {
  color: #2ecc71;
  font-weight: 500;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.search-filter-badge {
  color: #e74c3c;
  font-weight: 500;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  font-style: italic;
}

.filter-active-indicator {
  color: #f39c12;
  font-style: italic;
  font-weight: 600;
  background: rgba(243, 156, 18, 0.1);
  padding: 2px 6px;
  border-radius: 12px;
  margin-left: 4px;
  font-size: 0.8em;
}

/* Clear Filters Button Styling */
.btn-clear-filters {
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  border-color: #e74c3c;
  color: white;
  position: relative;
  overflow: hidden;
}

.btn-clear-filters::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.3),
    transparent
  );
  transition: left 0.5s;
}

.btn-clear-filters:hover::before {
  left: 100%;
}

.btn-clear-filters:hover {
  background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
  border-color: #c0392b;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
}
</style>
