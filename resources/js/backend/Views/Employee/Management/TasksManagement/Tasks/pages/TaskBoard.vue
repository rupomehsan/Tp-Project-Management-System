<template>
  <div class="google-sheets-container mb-5" :class="{ 'dark-mode': isDarkMode }">
    <!-- Header Toolbar -->
    <div class="sheets-toolbar">
      <div class="toolbar-left">
        <button class="btn-icon mobile-menu-toggle" @click="toggleMobileMenu">
          <i class="fa fa-bars"></i>
        </button>
        <div class="file-info">
          <h2 class="sheet-title">
            Task Board
            <span v-if="selectedProject" class="project-filter-badge"> - {{ selectedProject.name }} </span>
          </h2>
          <small v-if="filteredTaskCount !== undefined" class="task-count-info">
            Showing {{ filteredTaskCount }} task{{ filteredTaskCount !== 1 ? "s" : "" }}
          </small>
        </div>
      </div>
      
      <!-- Middle Section - Working Hours Counter -->
      <div class="toolbar-middle">
        <div class="working-hours-counter" 
             :title="workingHoursTooltip">
          <div class="hours-display">
            <i class="fa fa-clock-o"></i>
            <div class="hours-content">
              <div class="hours-main">
                <span class="hours-label">Today's Work:</span>
                <span class="hours-value">{{ todayWorkingHours }}</span>
              </div>
              <div class="hours-breakdown" v-if="todayCompletedTasks > 0">
                <small class="text-muted">
                  {{ todayCompletedTasks }} task{{ todayCompletedTasks !== 1 ? 's' : '' }} completed
                </small>
              </div>
              <div class="hours-breakdown" v-else>
                <small class="text-muted">No completed tasks today</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="toolbar-right">
        <!-- Mobile Action Toggle -->
        <button class="btn-icon mobile-actions-toggle d-md-none" @click="toggleMobileActions">
          <i class="fa fa-ellipsis-v"></i>
        </button>

        <!-- Action Buttons Container -->
        <div class="action-buttons-container" :class="{ 'mobile-actions-open': isMobileActionsOpen }">
          <!-- Clear Filters Button -->
          <button
            v-if="searchQuery || startDateFilter || endDateFilter || projectStatusFilter || devStatusFilter || priorityFilter"
            class="btn btn-outline-secondary btn-clear-filters"
            @click="clearAllFilters"
            title="Clear all filters"
          >
            <i class="fa fa-times"></i>
            <span class="btn-text">Clear</span>
          </button>

          <button class="btn btn-outline-info btn-refresh" @click="refreshComponent" title="Refresh Tasks and Groups">
            <i class="fa fa-refresh"></i>
            <span class="btn-text">Refresh</span>
          </button>

          <button class="btn btn-success btn-add-group" @click="showAddTaskGroupModal" title="Add New Task Group">
            <i class="fa fa-layer-group"></i>
            <span class="btn-text d-none d-lg-inline">Add Group</span>
            <span class="btn-text d-lg-none">Group</span>
          </button>

          <button class="btn btn-primary btn-add-task" @click="showAddTaskModal" title="Add New Task">
            <i class="fa fa-plus"></i>
            <span class="btn-text d-none d-lg-inline">Add Task</span>
            <span class="btn-text d-lg-none">Task</span>
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
      <!-- Sheet Tabs -->
      <div class="sheet-tabs mb-3">
        <!-- Mobile Filter Toggle -->
        <div class="mobile-filter-toggle d-md-none mb-3">
          <button class="btn btn-outline-primary w-100" @click="toggleMobileFilters">
            <i class="fa fa-filter"></i>
            Filters & Search
            <i class="fa fa-chevron-down" :class="{ 'rotate-180': isMobileFiltersOpen }"></i>
          </button>
        </div>

        <div class="tab-controls" :class="{ 'mobile-filters-open': isMobileFiltersOpen }">
          <!-- Project and Search Container -->
          <div class="project-search-container">
            <!-- Project Dropdown with Label -->
            <div class="project-dropdown-section">
              <label class="project-label">
                <i class="fa fa-folder"></i>
                <span class="d-none d-sm-inline">Project</span>
              </label>
              <select class="form-control project-dropdown" v-model="selectedProject" @change="onProjectChange" @click.stop title="Select a project">
                <option value="">Select Project</option>
                <option v-for="project in projects.data || projects" :key="project.id" :value="project">
                  {{ project.name }}
                </option>
              </select>
            </div>

            <!-- Search Input -->
            <div class="search-section">
              <label class="search-label">
                <i class="fa fa-search"></i>
                <span class="d-none d-sm-inline">Search</span>
              </label>
              <div class="search-input-wrapper">
                <input
                  type="text"
                  class="form-control search-input"
                  v-model="searchQuery"
                  @input="filterTasks"
                  @focus="onSearchFocus"
                  @blur="onSearchBlur"
                  placeholder="Search tasks..."
                  title="Search by task title, description, or status"
                />
                <div class="search-actions">
                  <button v-if="searchQuery" class="search-clear-btn" @click="clearSearch" title="Clear search">
                    <i class="fa fa-times"></i>
                  </button>
                  <div class="search-divider" v-if="searchQuery"></div>
                  <i class="fa fa-search search-icon"></i>
                </div>
              </div>
              <div v-if="searchResultsCount !== null" class="search-results-info">
                {{ searchResultsCount }} result{{ searchResultsCount !== 1 ? "s" : "" }} found
              </div>
            </div>
          </div>

          <!-- Date Filters Container -->
          <div class="date-filters-container">
            <div class="date-filter-group">
              <label class="date-label">
                <i class="fa fa-calendar"></i>
                <span class="d-none d-sm-inline">From</span>
              </label>
              <input type="date" class="form-control date-picker" v-model="startDateFilter" @change="filterTasks" title="Filter by start date" />
            </div>
            <div class="date-filter-group">
              <label class="date-label">
                <i class="fa fa-calendar"></i>
                <span class="d-none d-sm-inline">To</span>
              </label>
              <input type="date" class="form-control date-picker" v-model="endDateFilter" @change="filterTasks" title="Filter by end date" />
            </div>
          </div>

          <!-- Filter Controls -->
          <div class="filter-controls">
            <div class="filters-container">
              <div class="filter-group">
                <label class="filter-label">
                  <i class="fa fa-flag"></i>
                  <span class="d-none d-lg-inline">Project Status</span>
                  <span class="d-lg-none">Status</span>
                </label>
                <select class="form-control filter-select" v-model="projectStatusFilter" @change="filterTasks" title="Filter by project status">
                  <option value="">All</option>
                  <option value="active">🟢 Active</option>
                  <option value="pending">🟡 Pending</option>
                  <option value="completed">✅ Completed</option>
                  <option value="on_hold">⏸️ On Hold</option>
                  <option value="cancelled">❌ Cancelled</option>
                </select>
              </div>

              <div class="filter-group">
                <label class="filter-label">
                  <i class="fa fa-code"></i>
                  <span class="d-none d-lg-inline">Dev Status</span>
                  <span class="d-lg-none">Dev</span>
                </label>
                <select class="form-control filter-select" v-model="devStatusFilter" @change="filterTasks" title="Filter by development status">
                  <option value="">All</option>
                  <option value="Pending">📝 Pending</option>
                  <option value="In Progress">⚡ In Progress</option>
                  <option value="Completed">✅ Completed</option>
                  <option value="Not Completed">❌ Not Completed</option>
                </select>
              </div>

              <div class="filter-group">
                <label class="filter-label">
                  <i class="fa fa-exclamation-triangle"></i>
                  <span class="d-none d-sm-inline">Priority</span>
                </label>
                <select class="form-control filter-select" v-model="priorityFilter" @change="filterTasks" title="Filter by priority">
                  <option value="">All</option>
                  <option value="low">⚫ Low</option>
                  <option value="normal">🟢 Normal</option>
                  <option value="high">🟡 High</option>
                  <option value="urgent">🔴 Urgent</option>
                </select>
              </div>
            </div>
          </div>
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
      <div v-if="sortField" class="sort-indicator mb-2 p-2 bg-info text-white border rounded d-flex align-items-center justify-content-between">
        <small>
          <i class="fa fa-sort me-1"></i>
          <strong>Sorted by:</strong>
          {{ getSortFieldDisplayName(sortField) }}
          ({{ sortDirection === "asc" ? "A-Z" : "Z-A" }})
        </small>
        <button @click="clearSort" class="btn btn-sm btn-outline-light" title="Clear sorting"><i class="fa fa-times"></i> Clear Sort</button>
      </div>

      <div class="table-responsive">
        <table class="table table-hover table-bordered task-board-table">
          <thead class="table-header">
            <tr>
              <th class="w-10 d-none d-sm-table-cell" @dblclick="toggleEditMode">ID</th>
              <th class="sortable-header" @click="sortBy('title')" :class="{ sorted: isSorted('title') }" title="Click to sort by Task Title">
                <span class="d-none d-md-inline">Task Title</span>
                <span class="d-md-none">Task</span>
                <i class="fa ms-1" :class="getSortIcon('title')"></i>
              </th>
              <th class="sortable-header d-none d-lg-table-cell" @click="sortBy('priority')" :class="{ sorted: isSorted('priority') }" title="Click to sort by Priority">
                Priority
                <i class="fa ms-1" :class="getSortIcon('priority')"></i>
              </th>
              <th
                class="sortable-header"
                @click="sortBy('task_user_status')"
                :class="{ sorted: isSorted('task_user_status') }"
                title="Click to sort by Dev Status"
              >
                <span class="d-none d-md-inline">Dev Status</span>
                <span class="d-md-none">Status</span>
                <i class="fa ms-1" :class="getSortIcon('task_user_status')"></i>
              </th>
              <th
                class="sortable-header d-none d-md-table-cell"
                @click="sortBy('task_status')"
                :class="{ sorted: isSorted('task_status') }"
                title="Click to sort by Task Status"
              >
                Task Status
                <i class="fa ms-1" :class="getSortIcon('task_status')"></i>
              </th>
              <th
                class="sortable-header d-none d-lg-table-cell"
                @click="sortBy('start_date')"
                :class="{ sorted: isSorted('start_date') }"
                title="Click to sort by Start Date"
              >
                Start Date
                <i class="fa ms-1" :class="getSortIcon('start_date')"></i>
              </th>

              <th class="sortable-header d-none d-lg-table-cell" @click="sortBy('end_date')" :class="{ sorted: isSorted('end_date') }" title="Click to sort by End Date">
                End Date
                <i class="fa ms-1" :class="getSortIcon('end_date')"></i>
              </th>
              <th
                class="sortable-header d-none d-xl-table-cell"
                @click="sortBy('actual_time')"
                :class="{ sorted: isSorted('actual_time') }"
                title="Click to sort by Actual Time"
              >
                Actual Time
                <i class="fa ms-1" :class="getSortIcon('actual_time')"></i>
              </th>
              <th class="d-none d-lg-table-cell">Rating</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(group, groupIndex) in groupedTasks" :key="'group-' + groupIndex">
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
                  <i :class="group.collapsed ? 'fa fa-chevron-right' : 'fa fa-chevron-down'"></i>
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
                      @click.stop="() => console.log('Drag handle clicked for group:', groupIndex)"
                      @mousedown="() => console.log('Drag handle mousedown for group:', groupIndex)"
                    >
                      <i class="fa fa-grip-vertical"></i>
                    </div>
                    <h5 class="group-title">
                      {{ group.name || "Ungrouped Tasks" }}
                      <span class="task-count">({{ group.tasks.length }} tasks)</span>
                      <small class="drag-instruction" v-if="groupedTasks.length > 1">
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
                  :class="`table_rows table_row_${task.id} ${editableRows[getTaskGlobalIndex(task)] ? 'editable-row' : ''} group-task-row`"
                  @click="handleRowClick(task)"
                  @dblclick="enableRowEdit(task)"
                >
                  <!-- ID -->
                  <td class="text-limit task-id-cell d-none d-sm-table-cell" :title="task.id">
                    {{ task.id }}
                    <i v-if="editableRows[getTaskGlobalIndex(task)]" class="fa fa-edit text-warning ml-1"></i>
                  </td>

                  <!-- Task Title -->
                  <td class="text-limit task-title-cell" :title="task.title">
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
                        <div class="task-title-wrapper">
                          <span @dblclick="enableTitleEdit(task)" class="editable-title" :class="{ 'task-assigned': isTaskAssigned(task) }">
                            {{ task.title }}
                          </span>
                          <!-- Show task ID on mobile inline with title -->
                          <span class="d-sm-none task-id-mobile">#{{ task.id }}</span>
                          <!-- Show priority on mobile inline with title -->
                          <div class="d-lg-none mobile-priority">
                            <span v-if="task.priority === 'low'" class="priority-badge priority-low"> <i class="fa fa-circle"></i> Low </span>
                            <span v-else-if="task.priority === 'normal'" class="priority-badge priority-normal"> <i class="fa fa-circle"></i> Normal </span>
                            <span v-else-if="task.priority === 'high'" class="priority-badge priority-high"> <i class="fa fa-circle"></i> High </span>
                            <span v-else-if="task.priority === 'urgent'" class="priority-badge priority-urgent"> <i class="fa fa-circle"></i> Urgent </span>
                            <span v-else class="priority-badge">{{ task.priority }}</span>
                          </div>
                          <div v-if="isTaskAssigned(task)" class="assignment-info" :title="getAssignmentTooltip(task)">
                            <i class="fa fa-user-tag assignment-icon"></i>
                            <span class="assignment-text">
                              {{ getAssignmentText(task) }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>

                  <!-- Priority -->
                  <td class="text-limit d-none d-lg-table-cell" :title="task.priority">
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
                      <span v-if="task.priority === 'low'" class="priority-badge priority-low"> <i class="fa fa-circle"></i> Low </span>
                      <span v-else-if="task.priority === 'normal'" class="priority-badge priority-normal"> <i class="fa fa-circle"></i> Normal </span>
                      <span v-else-if="task.priority === 'high'" class="priority-badge priority-high"> <i class="fa fa-circle"></i> High </span>
                      <span v-else-if="task.priority === 'urgent'" class="priority-badge priority-urgent"> <i class="fa fa-circle"></i> Urgent </span>
                      <span v-else class="priority-badge">{{ task.priority }}</span>
                    </span>
                  </td>

                  <!-- Dev Status -->
                  <td class="text-limit dev-status-cell" :class="getTaskStatusClass(task.task_user_status)" :title="task.task_user_status">
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
                      <span v-if="task.task_user_status === 'Pending'" class="status-badge status-pending">
                        <i class="fa fa-clock-o"></i> <span class="d-none d-md-inline">Pending</span>
                      </span>
                      <span v-else-if="task.task_user_status === 'In Progress'" class="status-badge status-progress">
                        <i class="fa fa-spinner"></i> <span class="d-none d-md-inline">In Progress</span>
                      </span>
                      <span v-else-if="task.task_user_status === 'Completed'" class="status-badge status-completed">
                        <i class="fa fa-check-circle"></i> <span class="d-none d-md-inline">Completed</span>
                      </span>
                      <span v-else-if="task.task_user_status === 'Not Completed'" class="status-badge status-not-completed">
                        <i class="fa fa-times-circle"></i> <span class="d-none d-md-inline">Not Completed</span>
                      </span>
                      <span v-else class="status-badge">{{ task.task_user_status }}</span>
                    </span>
                  </td>
                  
                  <!-- Task Status -->
                  <td class="d-none d-md-table-cell">
                    <span class="status-badge"
                      :class="{
                        'status-pending': task.task_status === 'Pending',
                        'status-progress': task.task_status === 'In Progress',
                        'status-completed': task.task_status === 'Completed',
                        'status-not-completed': task.task_status === 'Not Completed'
                      }"
                    >
                      <i
                        v-if="task.task_status === 'Pending'"
                        class="fa fa-clock-o"
                      ></i>
                      <i
                        v-else-if="task.task_status === 'In Progress'"
                        class="fa fa-spinner"
                      ></i>
                      <i
                        v-else-if="task.task_status === 'Completed'"
                        class="fa fa-check-circle"
                      ></i>
                      <i
                        v-else-if="task.task_status === 'Not Completed'"
                        class="fa fa-times-circle"
                      ></i>
                      {{ task.task_status || task.task_user_status }}
                    </span>
                  </td>

                  <!-- Start Date -->
                  <td class="text-limit d-none d-lg-table-cell" :title="formatDateTime(task.start_date)">
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
                  <td class="text-limit d-none d-lg-table-cell" :title="formatDateTime(task.end_date)">
                    <input
                      v-if="editableRows[getTaskGlobalIndex(task)]"
                      type="datetime-local"
                      v-model="task.end_date"
                      :min="(task.start_date || todayDate) + (task.start_date ? '' : 'T00:00')"
                      class="form-control form-control-sm"
                      @change="debouncedSaveTask(task, 500)"
                      @click.stop
                    />
                    <span v-else>{{ formatDateTime(task.end_date) }}</span>
                  </td>

                  <!-- Actual Time -->
                  <td class="d-none d-xl-table-cell">
                    {{ FindActualTime(task.start_date, task.end_date) }}
                  </td>

                  <!-- Rating -->
                  <td class="text-limit d-none d-lg-table-cell" :title="`Rating: ${task.rating || 0}/5`">
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

                      <!-- Mobile quick actions (visible on small screens) -->
                      <div class="mobile-quick-actions d-md-none">
                        <button
                          class="btn btn-sm btn-outline-info me-1"
                          @click="openDescriptionModal(task)"
                          title="Description"
                        >
                          <i class="fa fa-file-text"></i>
                        </button>
                      </div>

                      <!-- 3-dot toggle button (outside dropdown) -->
                      <button
                        class="btn btn-sm btn-outline-secondary dropdown-toggle"
                        type="button"
                        @click.stop="toggleDropdown(task.id)"
                        title="More actions"
                      >
                        <i class="fa fa-ellipsis-v"></i>
                      </button>

                      <!-- 3-dot menu dropdown container -->
                      <div class="custom-dropdown" :class="{ show: activeDropdown === task.id }">
                        <div class="dropdown-menu custom-dropdown-menu" :class="{ show: activeDropdown === task.id }">
                          <a
                            class="dropdown-item text-info"
                            href="#"
                            @click.prevent="
                              openDescriptionModal(task);
                              closeDropdown();
                            "
                            :title="
                              task.description
                                ? `Description: ${convertHtmlToPlainText(task.description).substring(0, 100)}${
                                    convertHtmlToPlainText(task.description).length > 100 ? '...' : ''
                                  }`
                                : 'Add Description'
                            "
                          >
                            <i class="fa fa-file-text"></i>
                            <span>Description</span>
                          </a>
                          <a
                            class="dropdown-item text-warning"
                            href="#"
                            @click.prevent="
                              openTaskReviewModal(task);
                              closeDropdown();
                            "
                          >
                            <i class="fa fa-star"></i>
                            <span>Review</span>
                          </a>

                          <a
                            class="dropdown-item text-danger"
                            href="#"
                            @click.prevent="
                              canRemoveTask(task) && removeTask(task);
                              closeDropdown();
                            "
                            :class="{
                              disabled: !canRemoveTask(task),
                            }"
                            :title="
                              !canRemoveTask(task)
                                ? getDeleteDisabledTooltip(task)
                                : 'Delete task'
                            "
                          >
                            <i class="fa fa-trash"></i>
                            <span>Delete</span>
                          </a>
                          <router-link target="_blank" class="dropdown-item text-primary" :to="`/tasks/details/${task.slug}`">
                            <i class="fa fa-pencil"></i>
                            <span>Details</span>
                          </router-link>
                        </div>
                      </div>
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
    <div v-if="showAddTaskModalFlag" class="modal-overlay" :class="{ 'dark-mode': isDarkMode }" @click="closeAddTaskModal">
      <div class="modal-content responsive-modal" :class="{ 'dark-mode': isDarkMode }" @click.stop>
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
              <input type="text" id="taskTitle" v-model="newTask.title" class="form-control" placeholder="Enter task title" required />
            </div>

            <div class="form-group">
              <label for="taskGroup">Task Group</label>
              <select id="taskGroup" v-model="newTask.task_group_id" class="form-control">
                <option value="">Select Task Group (Optional)</option>
                <option v-for="group in task_groups" :key="group.id" :value="group.id">
                  {{ group.name }}
                </option>
              </select>
            </div>

            <div class="form-row responsive-form-row">
              <div class="form-group">
                <label for="taskPriority">Priority</label>
                <select id="taskPriority" v-model="newTask.priority" class="form-control">
                  <option value="low">🟢 Low</option>
                  <option value="normal">🟡 Normal</option>
                  <option value="high">🔴 High</option>
                  <option value="urgent">⚫ Urgent</option>
                </select>
              </div>

              <div class="form-group">
                <label for="taskStatus">Status</label>
                <select id="taskStatus" v-model="newTask.task_user_status" class="form-control">
                  <option value="Pending">📝 Pending</option>
                  <option value="In Progress">⚡ In Progress</option>
                  <option value="Completed">✅ Completed</option>
                  <option value="Not Completed">❌ Not Completed</option>
                </select>
              </div>
            </div>

            <div class="form-row responsive-form-row">
              <div class="form-group">
                <label for="startDate">Start Date & Time</label>
                <input type="datetime-local" id="startDate" v-model="newTask.start_date" class="form-control text-dark" />
              </div>
              <div class="form-group">
                <label for="endDate">End Date & Time</label>
                <input type="datetime-local" id="endDate" v-model="newTask.end_date" :min="newTask.start_date" class="form-control text-dark" />
              </div>
            </div>

            <!-- Date validation error message -->
            <div
              v-if="isNewTaskDateInvalid"
              class="alert alert-danger"
              style="margin-top: 10px; padding: 8px 12px; border-radius: 4px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24"
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
          <button type="button" class="btn btn-secondary" @click="closeAddTaskModal">Cancel</button>
          <button type="button" class="btn btn-primary" @click="createNewTask" :disabled="!newTask.title || isCreatingTask || isNewTaskDateInvalid">
            <i v-if="isCreatingTask" class="fa fa-spinner fa-spin"></i>
            {{ isCreatingTask ? "Creating..." : "Create Task" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Add Task Group Modal -->
    <div v-if="showAddTaskGroupModalFlag" class="modal-overlay" :class="{ 'dark-mode': isDarkMode }" @click="closeAddTaskGroupModal">
      <div class="modal-content" :class="{ 'dark-mode': isDarkMode }" @click.stop>
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
              <input type="text" id="taskGroupName" v-model="newTaskGroup.name" class="form-control" placeholder="Enter task group name" required />
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
          <button type="button" class="btn btn-secondary" @click="closeAddTaskGroupModal">Cancel</button>
          <button type="button" class="btn btn-success" @click="createNewTaskGroup" :disabled="!newTaskGroup.name || isCreatingTaskGroup">
            <i v-if="isCreatingTaskGroup" class="fa fa-spinner fa-spin"></i>
            {{ isCreatingTaskGroup ? "Creating..." : "Create Group" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Task Description Modal -->
    <div v-if="showDescriptionModalFlag" class="modal-overlay" :class="{ 'dark-mode': isDarkMode }" @click="closeDescriptionModal">
      <div class="modal-content description-modal" :class="{ 'dark-mode': isDarkMode }" @click.stop>
        <div class="modal-header">
          <h3>
            <i class="fa fa-file-text me-2"></i>
            Task Description
          </h3>
          <div class="task-info">
            <span class="task-title">{{ selectedTaskForDescription?.title }}</span>
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
              <small class="character-count"> {{ descriptionText ? descriptionText.length : 0 }} characters </small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeDescriptionModal" :disabled="isSavingDescription">Cancel</button>
          <button type="button" class="btn btn-primary" @click="saveTaskDescription" :disabled="!descriptionText.trim() || isSavingDescription">
            <i v-if="isSavingDescription" class="fa fa-spinner fa-spin"></i>
            {{ isSavingDescription ? "Saving..." : "Save Description" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Task Review Modal -->
    <div v-if="showReviewModalFlag" class="modal-overlay" :class="{ 'dark-mode': isDarkMode }" @click="closeReviewModal">
      <div class="modal-content review-modal" :class="{ 'dark-mode': isDarkMode }" @click.stop>
        <div class="modal-header">
          <h3>
            <i class="fa fa-star me-2"></i>
            Task Reviews
          </h3>
          <div class="task-info">
            <span class="task-title">{{ selectedTaskForReview?.title }}</span>
            <span class="task-id">#{{ selectedTaskForReview?.id }}</span>
          </div>
          <button class="modal-close-btn" @click="closeReviewModal">
            <i class="fa fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <!-- Loading State -->
          <div v-if="isLoadingReviews" class="loading-state">
            <div class="text-center p-4">
              <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
              <p class="mt-2 text-muted">Loading reviews...</p>
            </div>
          </div>

          <!-- Error State -->
          <div v-else-if="reviewError" class="error-state">
            <div class="alert alert-danger">
              <i class="fa fa-exclamation-triangle"></i>
              {{ reviewError }}
            </div>
            <button class="btn btn-outline-primary" @click="loadTaskReviews">
              <i class="fa fa-refresh"></i>
              Try Again
            </button>
          </div>

          <!-- Reviews Content -->
          <div v-else class="reviews-content">
            <!-- No Reviews State -->
            <div v-if="taskReviews.length === 0" class="no-reviews-state">
              <div class="text-center p-4">
                <i class="fa fa-star-o fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Reviews Yet</h5>
                <p class="text-muted">This task hasn't been reviewed yet.</p>
              </div>
            </div>

            <!-- Reviews List -->
            <div v-else class="reviews-list">
              <div v-for="review in taskReviews" :key="review.id" class="review-item">
                <!-- Review Content -->
                <div class="review-content">
                  <div class="review-header">
                    <div class="reviewer-info">
                      <div class="reviewer-avatar">
                        <i class="fa fa-user-circle"></i>
                      </div>
                      <div class="reviewer-details">
                        <h6 class="reviewer-name">{{ getReviewerDisplayName(review) }}</h6>
                        <span class="review-date">{{ formatReviewDate(review.created_at) }}</span>
                      </div>
                    </div>
                   
                  </div>

                  <div class="review-text">
                    <p>{{ review.comment || "No comment provided." }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Add Comment Section -->
            <div class="add-review-section mt-4">
              <div class="section-divider">
                <hr />
                <span class="section-title">
                  <i class="fa fa-plus-circle"></i>
                  Add Your Comment
                </span>
              </div>

              <div class="review-form">
                <!-- Comment Input -->
                <div class="comment-input-group mb-3">
                  <label class="form-label">
                    <i class="fa fa-comment"></i>
                    Comment
                  </label>
                  <textarea
                    v-model="newReviewComment"
                    class="form-control review-textarea"
                    rows="4"
                    placeholder="Write your comment here..."
                    :disabled="isSavingReview"
                  ></textarea>
                  <div class="character-count">
                    <small class="text-muted">{{ newReviewComment.length }} characters</small>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="review-submit-section">
                  <button
                    type="button"
                    class="btn btn-success btn-submit-review"
                    @click="submitReview"
                    :disabled="!newReviewComment.trim() || isSavingReview"
                  >
                    <i v-if="isSavingReview" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-comment"></i>
                    {{ isSavingReview ? "Submitting..." : "Submit Comment" }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeReviewModal">Close</button>
          <button type="button" class="btn btn-outline-primary" @click="loadTaskReviews" :disabled="isLoadingReviews">
            <i class="fa fa-refresh" :class="{ 'fa-spin': isLoadingReviews }"></i>
            Refresh Reviews
          </button>
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

      // Task Review Modal
      showReviewModalFlag: false,
      selectedTaskForReview: null,
      taskReviews: [],
      isLoadingReviews: false,
      reviewError: null,

      // Review Form Data
      newReviewComment: "",
      isSavingReview: false,

      // Mobile/Responsive state
      isMobileMenuOpen: false,
      isMobileActionsOpen: false,
      isMobileFiltersOpen: false,

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
      startDateFilter: "", // Start date filter
      endDateFilter: "", // End date filter
      projectStatusFilter: "", // Project status filter
      devStatusFilter: "", // Development status filter
      priorityFilter: "", // Priority filter
      originalAllTasks: [], // Store original task data before filtering
      searchQuery: "", // Search query for filtering tasks
      isSearching: false, // Search loading state
      searchResultsCount: null, // Number of search results
      searchFocused: false, // Track if search input is focused

      // Dropdown state
      activeDropdown: null, // Track which task dropdown is active
      searchTimeout: null, // Search debounce timeout

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

      filePath: "resources/js/backend/Views/SuperAdmin/Management/TestModule/helpers/demo.csv",
    };
  },
  created: async function () {
    let projectId = this.$route.query.project_id || null;

    // First get projects
    await this.get_all_projects();

    // Priority order for project selection:
    // 1. Query parameter (project_id)
    // 2. localStorage (last selected project)
    // 3. First available project

    const projectsList = this.projects.data || this.projects;

    if (projectId) {
      // If project_id is provided in query params, find and select that project
      const foundProject = projectsList.find((p) => p.id == projectId || p.slug == projectId);
      if (foundProject) {
        this.selectedProject = foundProject;
        this.saveSelectedProjectToLocalStorage(foundProject);
        console.log("Selected project from query param:", this.selectedProject);
      } else {
        console.warn("Project not found for ID:", projectId);
        // Fallback to localStorage or first project
        this.loadProjectFromLocalStorageOrDefault(projectsList);
      }
    } else {
      // Try to load from localStorage first, then fallback to first project
      this.loadProjectFromLocalStorageOrDefault(projectsList);
    }

    // Get task groups for the dropdown
    await this.get_all_task_groups();
    // Then get tasks for the selected project
    await this.get_all_tasks();
    // Initialize dark mode from localStorage
    this.isDarkMode = true;
    this.applyDarkMode();
    // Add event listeners for resizing
    document.addEventListener("mousemove", this.handleMouseMove);
    document.addEventListener("mouseup", this.handleMouseUp);
    // Add event listener for Escape key to exit edit mode
    document.addEventListener("keydown", this.handleKeyDown);
    // Add event listener for clicking outside to exit edit mode
    document.addEventListener("click", this.handleOutsideClick);
    // Add event listener for window resize (responsive)
    window.addEventListener("resize", this.handleResize);

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
    window.removeEventListener("resize", this.handleResize);

    // Clean up save timeouts
    Object.values(this.saveTimeouts).forEach((timeout) => clearTimeout(timeout));
    this.saveTimeouts = {};

    // Clean up search timeout
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }
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

    // LocalStorage methods for project persistence
    saveSelectedProjectToLocalStorage(project) {
      try {
        localStorage.setItem(
          "taskboard_selected_project",
          JSON.stringify({
            id: project.id,
            name: project.name,
            slug: project.slug,
          })
        );
        console.log("Project saved to localStorage:", project.name);
      } catch (error) {
        console.error("Failed to save project to localStorage:", error);
      }
    },

    loadProjectFromLocalStorageOrDefault(projectsList) {
      try {
        const savedProject = localStorage.getItem("taskboard_selected_project");

        if (savedProject && projectsList && projectsList.length > 0) {
          const parsedProject = JSON.parse(savedProject);
          const foundProject = projectsList.find((p) => p.id === parsedProject.id);

          if (foundProject) {
            this.selectedProject = foundProject;
            console.log("Selected project from localStorage:", foundProject.name);
            return;
          } else {
            console.warn("Saved project no longer exists, selecting first available project");
          }
        }

        // Fallback to first project if localStorage fails or project not found
        if (projectsList && projectsList.length > 0) {
          this.selectedProject = projectsList[0];
          this.saveSelectedProjectToLocalStorage(projectsList[0]);
          console.log("Selected first available project:", projectsList[0].name);
        }
      } catch (error) {
        console.error("Failed to load project from localStorage:", error);
        // Fallback to first project on error
        if (projectsList && projectsList.length > 0) {
          this.selectedProject = projectsList[0];
          this.saveSelectedProjectToLocalStorage(projectsList[0]);
        }
      }
    },

    // Enhanced filtering methods
    filterTasks() {
      this.isSearching = true;

      // Debounce search for better performance
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        console.log("Applying filters:", {
          search: this.searchQuery,
          startDate: this.startDateFilter,
          endDate: this.endDateFilter,
        });

        // Update search results count
        this.updateSearchResultsCount();
        this.isSearching = false;
      }, 300);
    },

    updateSearchResultsCount() {
      if (this.searchQuery && this.filteredTasks) {
        this.searchResultsCount = this.filteredTasks.reduce((count, group) => {
          return count + group.tasks.length;
        }, 0);
      } else {
        this.searchResultsCount = null;
      }
    },

    onSearchFocus() {
      this.searchFocused = true;
    },

    onSearchBlur() {
      this.searchFocused = false;
    },

    clearSearch() {
      this.searchQuery = "";
      this.searchResultsCount = null;
      this.filterTasks();
    },

    clearAllFilters() {
      this.searchQuery = "";
      this.startDateFilter = "";
      this.endDateFilter = "";
      this.projectStatusFilter = "";
      this.devStatusFilter = "";
      this.priorityFilter = "";
      this.searchResultsCount = null;
      console.log("All filters cleared");
    },

    // Legacy method for backward compatibility
    filterTasksByDate() {
      this.filterTasks();
    },

    clearDateFilter() {
      this.startDateFilter = "";
      this.endDateFilter = "";
      this.filterTasks();
    },

    // Store original tasks when component loads
    storeOriginalTasks() {
      if (!this.originalAllTasks && this.all?.data) {
        this.originalAllTasks = [...this.all.data];
        console.log("Original tasks stored:", this.originalAllTasks.length);
      }
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
        console.log("Original tasks stored for filtering:", this.originalAllTasks.length);
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
      console.log("Projects loaded:", this.projects);
    },

    // Project selection methods
    selectProject(project, event) {
      this.selectedProject = project;
      this.saveSelectedProjectToLocalStorage(project);
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

    // Handle project dropdown change
    onProjectChange() {
      if (this.selectedProject) {
        console.log("Selected project from dropdown:", this.selectedProject);

        // Save selected project to localStorage
        this.saveSelectedProjectToLocalStorage(this.selectedProject);

        // Load group order for the new project
        this.loadGroupOrder();

        // Refetch tasks for the selected project
        this.get_all_tasks();
      }
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
            const group = this.task_groups.find((g) => g.id == createdTask.task_group_id);
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

          // Clear all filters to show the new task immediately
          this.clearAllFilters();

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
        if (error.response && error.response.data && error.response.data.errors) {
          const errorMessages = [];
          for (const [field, messages] of Object.entries(error.response.data.errors)) {
            errorMessages.push(`${field}: ${messages.join(", ")}`);
          }
          window.s_warning(`Validation errors: ${errorMessages.join("; ")}`);
        } else if (error.response && error.response.data && error.response.data.message) {
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
          window.s_warning(response.data?.message || "Failed to create task group");
        }
      } catch (error) {
        console.error("Error creating task group:", error);

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
      // Convert HTML to plain text for editing
      this.descriptionText = task.description ? this.convertHtmlToPlainText(task.description) : "";
      this.showDescriptionModalFlag = true;
    },

    closeDescriptionModal() {
      this.showDescriptionModalFlag = false;
      this.selectedTaskForDescription = null;
      this.descriptionText = "";
      this.isSavingDescription = false;
    },

    async saveTaskDescription() {
      if (!this.selectedTaskForDescription || !this.selectedTaskForDescription.slug) {
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

        const response = await axios.post(`/task/update/${this.selectedTaskForDescription.slug}`, payload);

        if (response.data.status === "success") {
          // Update the task in local data
          const taskIndex = this.all.data.findIndex((t) => t.id === this.selectedTaskForDescription.id);
          if (taskIndex !== -1) {
            this.all.data[taskIndex].description = this.descriptionText.trim();
          }

          // Update the selected task object
          this.selectedTaskForDescription.description = this.descriptionText.trim();

          window.s_alert("Description saved successfully!");
          this.closeDescriptionModal();
        } else {
          window.s_warning(response.data?.message || "Failed to save description");
        }
      } catch (error) {
        console.error("Error saving description:", error);

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
          window.s_warning("Error saving description");
        }
      } finally {
        this.isSavingDescription = false;
      }
    },

    // Utility method to convert HTML content to plain text
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

    // Dropdown menu methods
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

    // Task review modal methods
    async openTaskReviewModal(task) {
      console.log("Opening review modal for task:", task);
      this.selectedTaskForReview = task;
      this.showReviewModalFlag = true;
      this.reviewError = null;

      // Load reviews for this task
      await this.loadTaskReviews();
    },

    closeReviewModal() {
      this.showReviewModalFlag = false;
      this.selectedTaskForReview = null;
      this.taskReviews = [];
      this.reviewError = null;
      // Reset review form
      this.newReviewComment = "";
      this.isSavingReview = false;
    },

    async loadTaskReviews() {
      if (!this.selectedTaskForReview) return;

      this.isLoadingReviews = true;
      this.reviewError = null;

      try {
        // Load reviews
        const reviewsResponse = await axios.get(`/task/task-reviews-by-task-id/${this.selectedTaskForReview.id}`);

        if (reviewsResponse.data.status === "success") {
          this.taskReviews = reviewsResponse.data.data;
          console.log("Reviews loaded:", this.taskReviews);
        } else {
          this.reviewError = reviewsResponse.data.message || "Failed to load reviews";
        }
      } catch (error) {
        console.error("Error loading task reviews:", error);

        if (error.response && error.response.status === 404) {
          // No reviews found - this is normal, show empty state
          this.taskReviews = [];
        } else if (error.response && error.response.data && error.response.data.message) {
          this.reviewError = error.response.data.message;
        } else {
          this.reviewError = "Failed to load reviews. Please try again.";
        }
      } finally {
        this.isLoadingReviews = false;
      }
    },

    // Review Form Methods
    async submitReview() {
      if (!this.selectedTaskForReview || !this.newReviewComment.trim()) {
        window.s_warning("Please provide a comment.");
        return;
      }

      this.isSavingReview = true;

      try {
        const reviewData = {
          task_id: this.selectedTaskForReview.id,
          comment: this.newReviewComment.trim(),
        };

        const response = await axios.post("/task/task-comments", reviewData);

        if (response.data.status === "success") {
          window.s_alert("Comment submitted successfully!");

          // Reset form
          this.newReviewComment = "";

          // Reload reviews to show the new comment
          await this.loadTaskReviews();
        } else {
          window.s_warning(response.data.message || "Failed to submit comment");
        }
      } catch (error) {
        console.error("Error submitting comment:", error);

        if (error.response && error.response.data && error.response.data.message) {
          window.s_warning(error.response.data.message);
        } else {
          window.s_warning("Failed to submit comment. Please try again.");
        }
      } finally {
        this.isSavingReview = false;
      }
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

    // Get display name for reviewer (Me vs Author)
    getReviewerDisplayName(review) {
      if (!review) return "Anonymous";

      // If we have the current user ID and it matches the reviewer
      if (this.auth_info?.id && review.user_id && review.user_id == this.auth_info.id) {
        return "Me";
      }

      // Otherwise show "Author" or the reviewer name if available
      return review.reviewer_name || "Author";
    },

    // Refresh component data
    async refreshComponent() {
      try {
        // Show loading state (you can add a loading indicator if needed)
        window.s_alert("Refreshing data...", "info");

        // Refresh all data
        await Promise.all([this.get_all_tasks(), this.get_all_task_groups(), this.get_all_projects()]);

        // Clear any edit modes
        this.editableRows = {};

        // Clear any pending save timeouts
        Object.values(this.saveTimeouts).forEach((timeout) => clearTimeout(timeout));
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
        // Clear search first if there's an active search
        if (this.searchQuery) {
          this.clearSearch();
          event.preventDefault();
          return;
        }
        // Exit all edit modes when Escape is pressed
        this.editableRows = {};
      }

      // Ctrl/Cmd + K for quick search
      if ((event.ctrlKey || event.metaKey) && event.key === "k") {
        event.preventDefault();
        this.focusSearchInput();
      }

      // Ctrl/Cmd + Shift + C to clear all filters
      if ((event.ctrlKey || event.metaKey) && event.shiftKey && event.key === "C") {
        event.preventDefault();
        this.clearAllFilters();
      }
    },

    focusSearchInput() {
      this.$nextTick(() => {
        const searchInput = this.$el?.querySelector(".search-input");
        if (searchInput) {
          searchInput.focus();
          searchInput.select();
        }
      });
    },

    // Handle clicking outside to exit edit mode
    handleOutsideClick(event) {
      // Close dropdown if clicking outside of it
      const dropdown = event.target.closest(".custom-dropdown");
      const dropdownToggle = event.target.closest(".dropdown-toggle");

      if (!dropdown && !dropdownToggle) {
        this.closeDropdown();
      }

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
            const group = this.task_groups.find((g) => g.id == createdTask.task_group_id);
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
        let reason = "";
        if (task.task_user_status === "Completed") {
          reason = `Cannot delete task. Development status is "Completed". Only tasks with "Pending" development status can be deleted.`;
        } else if (task.task_user_status === "Not Completed") {
          reason = `Cannot delete task. Development status is "Not Completed". Only tasks with "Pending" development status can be deleted.`;
        } else if (task.task_status && task.task_status !== "Pending") {
          reason = `Cannot delete task. Task status is "${task.task_status}". Only tasks with "Pending" status can be deleted.`;
        } else {
          reason = `Cannot delete task. Only tasks with "Pending" status can be deleted.`;
        }
        
        window.s_warning(reason);
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
          const originalIndex = this.originalAllTasks.findIndex((t) => t.id === task.id);
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

      const assigneeInfo = task.assignee_name ? task.assignee_name : `User ID: ${task.assigned_to}`;
      const creatorInfo = task.creator_name ? task.creator_name : `Creator ID: ${task.creator}`;

      return `Assigned to: ${assigneeInfo}\nCreated by: ${creatorInfo}`;
    },

    // Check if a task can be removed (only Pending tasks can be removed)
    canRemoveTask(task) {
      // Prevent deletion if task_status is not Pending
      if (task.task_status && task.task_status !== "Pending") {
        return false;
      }
      
      // Prevent deletion if task_user_status (dev status) is Completed or Not Completed
      if (task.task_user_status === "Completed" || task.task_user_status === "Not Completed") {
        return false;
      }
      
      // Allow deletion only for Pending tasks
      return true;
    },

    // Get tooltip message for disabled delete button
    getDeleteDisabledTooltip(task) {
      if (task.task_user_status === "Completed") {
        return `Cannot delete - Development status is "Completed". Only tasks with "Pending" development status can be deleted.`;
      } else if (task.task_user_status === "Not Completed") {
        return `Cannot delete - Development status is "Not Completed". Only tasks with "Pending" development status can be deleted.`;
      } else if (task.task_status && task.task_status !== "Pending") {
        return `Cannot delete - Task status is "${task.task_status}". Only tasks with "Pending" status can be deleted.`;
      } else {
        return `Cannot delete - Only tasks with "Pending" status can be deleted.`;
      }
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
            const originalIndex = this.originalAllTasks.findIndex((t) => t.id === task.id);
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
      console.log("🚀 DRAG START - Group index:", groupIndex, "Group:", this.groupedTasks[groupIndex]?.name);

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
      document.querySelectorAll(".group-header-row.drag-over").forEach((row) => {
        row.classList.remove("drag-over");
      });

      console.log("✅ Drag ended successfully");
    },

    onGroupDragOver(event, groupIndex) {
      // Allow dropping by preventing default
      event.preventDefault();

      if (this.draggedGroupIndex === null || this.draggedGroupIndex === groupIndex) {
        return;
      }

      console.log("📍 DRAG OVER - From:", this.draggedGroupIndex, "To:", groupIndex);

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

      console.log("🎯 DROP EVENT - From:", this.draggedGroupIndex, "To:", dropIndex);

      if (this.draggedGroupIndex === null || this.draggedGroupIndex === dropIndex) {
        console.log("❌ Drop cancelled - same position or no drag source");
        return;
      }

      console.log(`✅ Moving group from index ${this.draggedGroupIndex} to ${dropIndex}`);

      // Reorder the groups in the customGroupOrder array
      this.reorderGroups(this.draggedGroupIndex, dropIndex);

      // Clean up drag state
      this.isDragging = false;
      this.draggedGroupIndex = null;
      this.dragOverGroupIndex = null;

      // Remove visual feedback classes
      document.querySelectorAll(".group-header-row.drag-over").forEach((row) => {
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

      if (fromIndex < 0 || fromIndex >= groups.length || toIndex < 0 || toIndex >= groups.length) {
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
        localStorage.setItem(`groupOrder_${this.selectedProject.id}`, JSON.stringify(this.customGroupOrder));
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
        const saved = localStorage.getItem(`groupOrder_${this.selectedProject.id}`);
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

    // Helper method to get task duration for tooltip
    getTaskDuration(task) {
      if (!task.start_date || !task.end_date) {
        return "No duration";
      }

      try {
        const startDate = new Date(task.start_date);
        const endDate = new Date(task.end_date);

        // Validate dates
        if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
          return "Invalid dates";
        }

        // Calculate difference in milliseconds
        const diffMs = endDate.getTime() - startDate.getTime();

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
          return "< 1 min";
        }
      } catch (error) {
        console.error("Error calculating task duration:", error);
        return "Error";
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
    ...mapState(auth_store, {
      auth_info: "auth_info",
    }),
    isAllSelected() {
      return this.all?.data?.length > 0 && this.all.data?.every((item) => this.selected.some((s) => s.id === item.id));
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

    // Get today's date in YYYY-MM-DD format
    todayDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, "0");
      const day = String(today.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },

    // Tasks filtered by search and date range
    filteredTasks() {
      let tasks = this.originalAllTasks || this.all?.data || [];

      // Filter by search query
      if (this.searchQuery && this.searchQuery.trim()) {
        const query = this.searchQuery.toLowerCase().trim();
        tasks = tasks.filter((task) => {
          return (
            task.title.toLowerCase().includes(query) ||
            (task.description && this.convertHtmlToPlainText(task.description).toLowerCase().includes(query)) ||
            task.priority.toLowerCase().includes(query) ||
            task.task_status.toLowerCase().includes(query) ||
            task.task_user_status.toLowerCase().includes(query) ||
            (task.task_group_name && task.task_group_name.toLowerCase().includes(query)) ||
            (task.assigned_users && task.assigned_users.some((user) => user.name.toLowerCase().includes(query)))
          );
        });
      }

      // Filter by start date
      if (this.startDateFilter) {
        const startFilter = new Date(this.startDateFilter);
        startFilter.setHours(0, 0, 0, 0);

        tasks = tasks.filter((task) => {
          if (!task.start_date) return false;
          const taskStartDate = new Date(task.start_date);
          taskStartDate.setHours(0, 0, 0, 0);
          return taskStartDate >= startFilter;
        });
      }

      // Filter by end date
      if (this.endDateFilter) {
        const endFilter = new Date(this.endDateFilter);
        endFilter.setHours(23, 59, 59, 999);

        tasks = tasks.filter((task) => {
          if (!task.end_date) return false;
          const taskEndDate = new Date(task.end_date);
          taskEndDate.setHours(23, 59, 59, 999);
          return taskEndDate <= endFilter;
        });
      }

      // Filter by project status
      if (this.projectStatusFilter) {
        tasks = tasks.filter((task) => {
          // Assuming project status is available in task.project.status or similar
          return task.project_status === this.projectStatusFilter || (task.project && task.project.status === this.projectStatusFilter);
        });
      }

      // Filter by development status
      if (this.devStatusFilter) {
        tasks = tasks.filter((task) => {
          return task.task_user_status === this.devStatusFilter;
        });
      }

      // Filter by priority
      if (this.priorityFilter) {
        tasks = tasks.filter((task) => {
          return task.priority === this.priorityFilter;
        });
      }

      return tasks;
    },

    // Get count of filtered tasks for display
    filteredTaskCount() {
      return this.filteredTasks ? this.filteredTasks.length : 0;
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
          const existingGroup = this.task_groups.find((g) => g.id == task.task_group_id);

          if (existingGroup) {
            groupId = task.task_group_id;
            // First try to use task_group_name from the task object
            groupName = task.task_group_name || task.task_group?.name || existingGroup.name;
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
      const cleanedCustomOrder = this.customGroupOrder.filter((groupId) => existingGroupIds.includes(groupId));

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
          localStorage.setItem(`groupOrder_${this.selectedProject.id}`, JSON.stringify(this.customGroupOrder));
        }
      }

      const result = allGroups.sort((a, b) => {
        // If we have custom group order, use it
        if (this.customGroupOrder.length > 0) {
          const aIndex = this.customGroupOrder.indexOf(a.id);
          const bIndex = this.customGroupOrder.indexOf(b.id);

          console.log(`Sorting: ${a.name}(${a.id}) (index: ${aIndex}) vs ${b.name}(${b.id}) (index: ${bIndex})`);

          // If both groups are in custom order, sort by their custom position
          if (aIndex !== -1 && bIndex !== -1) {
            console.log(`Both in custom order: ${a.name} at ${aIndex}, ${b.name} at ${bIndex}`);
            return aIndex - bIndex;
          }

          // If only a is in custom order, it comes first
          if (aIndex !== -1 && bIndex === -1) {
            console.log(`${a.name} in custom order, ${b.name} not - ${a.name} first`);
            return -1;
          }

          // If only b is in custom order, it comes first
          if (aIndex === -1 && bIndex !== -1) {
            console.log(`${b.name} in custom order, ${a.name} not - ${b.name} first`);
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

            if (this.sortField === "task_user_status" || this.sortField === "task_status") {
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
            } else if (this.sortField === "start_date" || this.sortField === "end_date") {
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

        console.log(`📊 Tasks sorted by ${this.sortField} (${this.sortDirection})`);
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

    // Calculate today's working hours from completed tasks
    todayWorkingHours() {
      const totalMinutes = this.todayWorkingMinutes;
      
      if (totalMinutes === 0) {
        return "0h 0m";
      }

      const hours = Math.floor(totalMinutes / 60);
      const minutes = totalMinutes % 60;

      if (hours === 0) {
        return `${minutes}m`;
      } else if (minutes === 0) {
        return `${hours}h`;
      } else {
        return `${hours}h ${minutes}m`;
      }
    },

    // Calculate total working minutes for today
    todayWorkingMinutes() {
      const todayTasks = this.todayCompletedTasksList;
      let totalMinutes = 0;

      todayTasks.forEach(task => {
        if (task.start_date && task.end_date) {
          const startDate = new Date(task.start_date);
          const endDate = new Date(task.end_date);
          
          // Only count if dates are valid and end is after start
          if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime()) && endDate > startDate) {
            const diffMs = endDate.getTime() - startDate.getTime();
            const diffMinutes = Math.floor(diffMs / (1000 * 60));
            totalMinutes += diffMinutes;
          }
        }
      });

      return totalMinutes;
    },

    // Get count of completed tasks today
    todayCompletedTasks() {
      return this.todayCompletedTasksList.length;
    },

    // Get list of tasks completed today
    todayCompletedTasksList() {
      const today = new Date();
      const todayStr = today.toISOString().split('T')[0]; // YYYY-MM-DD format
      
      const allTasks = this.filteredTasks || this.all?.data || [];
      
      return allTasks.filter(task => {
        // Check if task is completed
        if (task.task_user_status !== 'Completed') {
          return false;
        }

        // Check if task was completed today or has today's date range
        if (task.end_date) {
          const taskEndDate = new Date(task.end_date);
          const taskEndDateStr = taskEndDate.toISOString().split('T')[0];
          
          // Include tasks that end today
          if (taskEndDateStr === todayStr) {
            return true;
          }
        }

        // Also check if task was updated today (if updated_at field exists)
        if (task.updated_at) {
          const updatedDate = new Date(task.updated_at);
          const updatedDateStr = updatedDate.toISOString().split('T')[0];
          
          if (updatedDateStr === todayStr && task.task_user_status === 'Completed') {
            return true;
          }
        }

        return false;
      });
    },

    // Generate tooltip text for working hours counter
    workingHoursTooltip() {
      const tasks = this.todayCompletedTasksList;
      
      if (tasks.length === 0) {
        return "No tasks completed today";
      }

      let tooltip = `Today's completed tasks (${tasks.length}):\n\n`;
      
      tasks.forEach((task, index) => {
        if (index < 5) { // Show only first 5 tasks in tooltip
          const duration = this.getTaskDuration(task);
          tooltip += `• ${task.title} (${duration})\n`;
        }
      });

      if (tasks.length > 5) {
        tooltip += `\n... and ${tasks.length - 5} more tasks`;
      }

      tooltip += `\n\nTotal working time: ${this.todayWorkingHours}`;
      
      return tooltip;
    },

    // Mobile/Responsive Methods
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;
    },

    toggleMobileActions() {
      this.isMobileActionsOpen = !this.isMobileActionsOpen;
    },

    toggleMobileFilters() {
      this.isMobileFiltersOpen = !this.isMobileFiltersOpen;
    },

    closeMobileMenus() {
      this.isMobileMenuOpen = false;
      this.isMobileActionsOpen = false;
      this.isMobileFiltersOpen = false;
    },

    // Handle window resize
    handleResize() {
      // Close mobile menus on larger screens
      if (window.innerWidth >= 768) {
        this.closeMobileMenus();
      }
    },
  },

  watch: {
    // Watch for route query parameter changes
    "$route.query.project_id": {
      handler(newProjectId) {
        if (newProjectId) {
          const projectsList = this.projects.data || this.projects;
          const foundProject = projectsList.find((p) => p.id == newProjectId || p.slug == newProjectId);
          if (foundProject && foundProject.id !== this.selectedProject?.id) {
            this.selectedProject = foundProject;
            this.saveSelectedProjectToLocalStorage(foundProject);
            console.log("Project changed via route query:", this.selectedProject);
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
</style>
