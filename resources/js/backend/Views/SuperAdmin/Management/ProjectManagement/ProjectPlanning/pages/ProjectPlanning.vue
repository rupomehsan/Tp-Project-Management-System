<template>
  <div class="planning-layout mx-5">
    <div class="planner-container mb-5">
      <div class="header">
        <div class="title">
          <span class="daily">Project Planning</span>
        </div>
        <div>
          <select
            class="project-select"
            v-model="selectedProjectId"
            aria-label="Select project"
          >
            <option value="">Select Project</option>
            <option :value="value.id" v-for="value in projects" :key="value.id">
              {{ value.name }}
            </option>
          </select>
        </div>
      </div>
      <div class="action-plan">
        <h5 class="action-plan-header">{{ selected_project_name }}</h5>
        <table class="action-table">
          <thead>
            <tr>
              <th class="sl-column">Sl</th>
              <th>Module List</th>
              <th class="date-column">Start Date</th>
              <th class="date-column">End Date</th>
              <th class="done-column">Done</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(value, index) in project_modules" :key="value.id">
              <td class="sl-column">{{ index + 1 }}</td>
              <td
                @click="selectModule(value)"
                :style="{
                  background: project_modules.some(
                    (item) => item.id !== value.id && item.is_show
                  )
                    ? 'transparent'
                    : value.is_show
                    ? '#28a745'
                    : 'transparent',
                }"
              >
                <h5 class="text-dark cursor-pointer">
                  {{ value.module_name }}
                </h5>
              </td>
              <td class="date-column">
                <input
                  type="date"
                  class="date-input"
                  v-model="value.start_date"
                />
              </td>
              <td class="date-column">
                <input
                  type="date"
                  class="date-input"
                  v-model="value.end_date"
                />
              </td>
              <td class="done-column">
                <input
                  type="checkbox"
                  class="done-checkbox"
                  v-model="value.is_done"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Task modal -->
      <div v-if="showTaskModal" class="modal-backdrop">
        <div class="modal-panel" role="dialog" aria-modal="true">
          <div class="modal-header">
            <h4 class="modal-title text-dark">
              {{ module_name }}
            </h4>
          </div>
          <div class="modal-body">
            <div class="form-row">
              <label>Management Name *</label>
              <input
                type="text"
                v-model="modalTitle"
                placeholder="Enter name"
              />
            </div>
            <div class="form-row">
              <label>Date</label>
              <input type="date" v-model="modalDue" />
            </div>

            <div v-if="modalError" class="modal-error">{{ modalError }}</div>
          </div>
          <div class="modal-footer">
            <button class="btn-link" @click="closeTaskModal">Cancel</button>
            <button class="add-btn" @click="submitTaskModal">
              {{ editingTaskId ? "Save" : "Create" }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="module-tasks" v-if="module_name">
      <div class="module-tasks-header">
        <h5>{{ module_name }}</h5>
        <div class="task-controls">
          <input
            type="text"
            v-model="newTaskTitle"
            @keyup.enter="addModuleTask"
            class="task-add-input"
            placeholder="Quick add (press Enter)"
          />
          <button type="button" class="add-btn" @click="openTaskModal">
            Add
          </button>
        </div>
      </div>
      <div class="row m-3">
        <div class="col-12">
          <div
            v-if="!module_management || module_management.length === 0"
            class="empty-state"
          >
            No management items. Add one above or click Add.
          </div>

          <div v-else>
            <div
              class="action-plan"
              v-for="group in groupedModuleManagement"
              :key="group.dateKey"
              style="margin-bottom: 18px"
            >
              <div
                class="action-plan-header"
                style="
                  display: flex;
                  justify-content: space-between;
                  align-items: center;
                "
              >
                <div>Date : {{ group.dateLabel }}</div>
                <div class="group-actions">
                  <button
                    class="icon-btn"
                    type="button"
                    @click="openGroupEditModal(group)"
                    title="Edit group"
                  >
                    <svg
                      width="16"
                      height="16"
                      viewBox="0 0 24 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"
                        fill="#2d5a3d"
                      />
                      <path
                        d="M20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"
                        fill="#2d5a3d"
                      />
                    </svg>
                  </button>
                  <button
                    class="icon-btn"
                    type="button"
                    @click="deleteGroup(group)"
                    title="Delete group"
                  >
                    <svg
                      width="16"
                      height="16"
                      viewBox="0 0 24 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M6 7h12v13a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7z"
                        stroke="#c33"
                        stroke-width="1.2"
                        fill="none"
                      />
                      <path
                        d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"
                        stroke="#c33"
                        stroke-width="1.2"
                        fill="none"
                      />
                    </svg>
                  </button>
                </div>
              </div>
              <table class="action-table">
                <thead>
                  <tr>
                    <th class="sl-column">Sl</th>
                    <th>Management Name</th>
                    <th class="done-column">Done</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(task, idx) in group.tasks" :key="task.id">
                    <td class="sl-column">{{ idx + 1 }}</td>
                    <td>
                      <div
                        style="
                          display: flex;
                          align-items: center;
                          justify-content: space-between;
                          gap: 10px;
                        "
                      >
                        <div
                          style="flex: 1; cursor: text"
                          @dblclick="openEditModal(task)"
                        >
                          <p class="text-dark" style="margin: 0">
                            {{ task.title }}
                          </p>
                          <small v-if="task.note" class="text-muted">{{
                            task.note
                          }}</small>
                        </div>
                        <div
                          style="display: flex; gap: 6px; align-items: center"
                        >
                          <button
                            class="icon-btn"
                            type="button"
                            @click.stop="openEditModal(task)"
                            title="Edit item"
                          >
                            <svg
                              width="14"
                              height="14"
                              viewBox="0 0 24 24"
                              fill="none"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path
                                d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"
                                fill="#2d5a3d"
                              />
                              <path
                                d="M20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"
                                fill="#2d5a3d"
                              />
                            </svg>
                          </button>
                          <button
                            class="icon-btn"
                            type="button"
                            @click.stop="confirmDeleteSingle(task)"
                            title="Delete item"
                          >
                            <svg
                              width="14"
                              height="14"
                              viewBox="0 0 24 24"
                              fill="none"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path
                                d="M6 7h12v13a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7z"
                                stroke="#c33"
                                stroke-width="1.2"
                                fill="none"
                              />
                              <path
                                d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"
                                stroke="#c33"
                                stroke-width="1.2"
                                fill="none"
                              />
                            </svg>
                          </button>
                        </div>
                      </div>
                    </td>
                    <td class="done-column">
                      <input
                        type="checkbox"
                        class="done-checkbox"
                        v-model="task.is_done"
                        @change="toggleDone(task)"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div style="display: flex; justify-content: center; margin-top: 16px">
      <button class="add-btn" type="button" @click="submitPlanning">
        Submit Planning
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProjectPlanning",
  data() {
    return {
      selectedDate: new Date().toISOString().split("T")[0],
      showDatePicker: false,
      weekDays: [],
      timeSlots: [],
      goals: [],
      notes: [],
      actionPlans: [],
      isInitialized: false,
      projects: [],
      selectedProjectId: null,
      selected_project_name: "",
      columns: [1, 2, 3],
      project_modules: [],
      // module task UI state
      module_management: [],
      // currently active module id for module_management items
      selectedModuleId: null,
      // group edit state
      editingGroupKey: null,
      module_name: "",
      newTaskTitle: "",
      // modal state
      showTaskModal: false,
      modalTitle: "",
      modalDue: null,
      modalNote: "",
      modalError: null,
      editingTaskId: null,
    };
  },

  created: async function () {
    await this.get_all_projects();
    // restore last selected project from localStorage (if present and valid)
    try {
      const saved = localStorage.getItem("project_planning_selected_project");
      if (saved) {
        const proj = this.projects.find((p) => String(p.id) === String(saved));
        if (proj) {
          this.selectedProjectId = proj.id;
        }
      }
    } catch (e) {
      // ignore localStorage errors
      console.warn("Could not restore selected project from localStorage", e);
    }
  },
  methods: {
    // select a module: toggle UI and load its management items into module_management
    selectModule(module) {
      // toggle is_show on clicked module and hide others
      this.project_modules.forEach((item) => {
        if (item.id !== module.id) item.is_show = false;
      });
      module.is_show = !module.is_show;

      // set module name and active module id
      this.module_name = module.module_name;
      this.selectedModuleId = module.id;

      // populate module_management with the clicked module's management items (map to UI shape)
      const raw = module.module_management || [];
      this.module_management = (raw || []).map((m) => ({
        id: m.id,
        title: m.management_name || m.title || "Untitled",
        note: m.note || null,
        due: m.date || m.due || null,
        // handle backend values like "false" which are truthy in JS
        is_done:
          m.is_done === true ||
          m.is_done === 1 ||
          m.is_done === "1" ||
          m.is_done === "true"
            ? true
            : false,
        module_id: m.module_id || module.id,
      }));
    },
    async get_all_projects() {
      try {
        const response = await axios.get("/project?get_all=1");
        this.projects = response.data?.data;
      } catch (error) {
        console.error("Error fetching projects:", error);
      }
    },
    // fetch modules for currently selected project
    fetchProjectModules: async function () {
      if (!this.selectedProjectId) {
        this.project_modules = [];
        return;
      }
      try {
        const response = await axios.get(
          `/project-modules?get_all=1&project_id=${this.selectedProjectId}&with_management=true`
        );
        // ensure reactive array
        this.project_modules = (response.data?.data || []).map((m) => ({
          ...m,
          start_date: m.start_date || null,
          end_date: m.end_date || null,
          // normalize possible backend values: true, 1, "true", "1" -> true; everything else -> false
          is_done:
            m.is_done === true ||
            m.is_done === 1 ||
            m.is_done === "1" ||
            m.is_done === "true"
              ? true
              : false,
        }));
      } catch (e) {
        console.error("Error fetching project modules", e);
      }
    },
    // Module tasks methods
    addModuleTask() {
      let title = (this.newTaskTitle || "").trim();
      if (!title) {
        // allow adding an empty task but give it a sensible default title
        title = `New task ${new Date().toLocaleTimeString()}`;
      }
      const id = Date.now();
      const newTask = {
        id,
        title,
        note: "",
        due: null,
        is_done: false,
        module_id: this.selectedModuleId,
      };
      console.log("Adding module task", newTask);
      this.module_management.unshift(newTask);
      this.newTaskTitle = "";
      // focus the add input so user can continue quickly
      this.$nextTick(() => {
        const input = this.$el.querySelector(".task-add-input");
        if (input) input.focus();
      });
    },
    confirmDeleteTask(index) {
      const task = this.module_management[index];
      if (!task) return;
      const ok = confirm(`Delete "${task.title}"?`);
      if (ok) this.module_management.splice(index, 1);
    },
    onEditDescription(event, task) {
      task.note = event.target.innerText.trim();
    },
    setDue(task, value) {
      task.due = value || null;
    },
    // Modal handlers
    openTaskModal() {
      this.editingTaskId = null;
      this.modalTitle = this.newTaskTitle || "";
      this.modalDue = null;
      this.modalNote = "";
      this.modalError = null;
      this.showTaskModal = true;
      this.$nextTick(() => {
        const el = this.$el.querySelector(".modal-panel input[type=text]");
        if (el) el.focus();
      });
    },
    closeTaskModal() {
      this.showTaskModal = false;
    },
    submitTaskModal() {
      // If we're editing a group's date, apply modalDue to all tasks in that group
      if (this.editingGroupKey) {
        const newDate = this.modalDue || null;
        this.module_management.forEach((t) => {
          const key = t.due
            ? new Date(t.due).toISOString().split("T")[0]
            : "__no_date__";
          if (key === this.editingGroupKey) t.due = newDate;
        });
        this.editingGroupKey = null;
        this.closeTaskModal();
        return;
      }

      const title = (this.modalTitle || "").trim();
      if (!title) {
        this.modalError = "Management name is required";
        return;
      }
      if (this.editingTaskId) {
        const idx = this.module_management.findIndex(
          (t) => t.id === this.editingTaskId
        );
        if (idx !== -1) {
          const t = this.module_management[idx];
          t.title = title;
          t.note = this.modalNote || "";
          t.due = this.modalDue || null;
        }
      } else {
        const id = Date.now();
        const newTask = {
          id,
          title,
          note: this.modalNote || "",
          due: this.modalDue || null,
          is_done: false,
          module_id: this.selectedModuleId,
        };
        this.module_management.unshift(newTask);
      }
      this.newTaskTitle = "";
      this.closeTaskModal();
    },
    // submit all planning data to server
    async submitPlanning() {
      if (!this.selectedProjectId) {
        alert("Please select a project before submitting.");
        return;
      }

      // build modules payload
      const modulesPayload = (this.project_modules || []).map((m) => ({
        project_id: this.selectedProjectId,
        module_id: m.id,
        start_date: m.start_date || null,
        end_date: m.end_date || null,
        // send is_done as boolean
        is_done: !!m.is_done,
      }));

      // build module management payload (attach module_id where available)
      const managementPayload = (this.module_management || []).map((t) => ({
        module_id: t.module_id || this.selectedModuleId || null,
        title: t.title,
        note: t.note || null,
        due: t.due || null,
        // send is_done as boolean
        is_done: !!t.is_done,
        local_id: t.id,
      }));

      const payload = {
        modules: modulesPayload,
        module_management: managementPayload,
      };

      try {
        const res = await axios.post("/project-plans/store", payload);
        // You can customize success handling
        console.log("Project planning saved", res.data);
        alert("Project planning saved successfully.");
      } catch (err) {
        console.error("Error submitting project planning", err);
        alert("Failed to save project planning. See console for details.");
      }
    },

    openEditModal(task) {
      if (!task) return;
      this.editingTaskId = task.id;
      this.modalTitle = task.title || "";
      this.modalDue = task.due || null;
      this.modalNote = task.note || "";
      this.modalError = null;
      this.showTaskModal = true;
      this.$nextTick(() => {
        const el = this.$el.querySelector(".modal-panel input[type=text]");
        if (el) el.focus();
      });
    },
    openGroupEditModal(group) {
      this.editingGroupKey = group.dateKey;
      this.editingTaskId = null;
      this.modalTitle = "";
      this.modalNote = "";
      this.modalDue = group.dateKey === "__no_date__" ? null : group.dateKey;
      this.modalError = null;
      this.showTaskModal = true;
      this.$nextTick(() => {
        const el = this.$el.querySelector(".modal-panel input[type=date]");
        if (el) el.focus();
      });
    },
    deleteGroup(group) {
      const confirmMsg =
        group.dateKey === "__no_date__"
          ? "Delete all tasks without a date?"
          : `Delete all tasks for ${group.dateLabel}?`;
      const ok = confirm(confirmMsg);
      if (!ok) return;
      this.module_management = this.module_management.filter((t) => {
        const key = t.due
          ? new Date(t.due).toISOString().split("T")[0]
          : "__no_date__";
        return key !== group.dateKey;
      });
    },
    confirmDeleteSingle(task) {
      if (!task) return;
      const ok = confirm(`Delete "${task.title}"?`);
      if (!ok) return;
      const idx = this.module_management.findIndex((t) => t.id === task.id);
      if (idx !== -1) this.module_management.splice(idx, 1);
    },
    toggleDone(task) {
      // keep reactivity simple — if you need to persist, call API here
      // normalize to use `is_done` across the codebase
      task.is_done = !task.is_done;
    },
    editTask(task) {
      // focus first editable element for quick keyboard editing
      this.$nextTick(() => {
        const el = this.$el.querySelector(
          `[aria-label=\"Edit title for ${task.title}\"]`
        );
        if (el) el.focus();
      });
    },
    onEditTitle(event, task) {
      task.title = event.target.innerText.trim();
    },
    formatDate(d) {
      if (!d) return "";
      try {
        return new Date(d).toLocaleDateString();
      } catch (e) {
        return d;
      }
    },
  },
  computed: {
    groupedModuleManagement() {
      const groups = {};
      // only include management items for the currently selected module
      if (!this.selectedModuleId) return [];
      const items = (this.module_management || []).filter(
        (t) => String(t.module_id) === String(this.selectedModuleId)
      );
      items.forEach((t) => {
        // normalize date key (use YYYY-MM-DD) or 'No date'
        const key = t.due
          ? new Date(t.due).toISOString().split("T")[0]
          : "__no_date__";
        if (!groups[key])
          groups[key] = {
            dateKey: key,
            dateLabel: t.due ? this.formatDate(t.due) : "No date",
            tasks: [],
          };
        groups[key].tasks.push(t);
      });
      // convert to array and sort by date (no date goes last)
      const arr = Object.values(groups).sort((a, b) => {
        if (a.dateKey === "__no_date__") return 1;
        if (b.dateKey === "__no_date__") return -1;
        return a.dateKey < b.dateKey ? -1 : 1;
      });
      return arr;
    },
  },
  watch: {
    selectedProjectId(newVal) {
      // automatically fetch modules when selection changes
      // persist last selection
      try {
        if (newVal)
          localStorage.setItem(
            "project_planning_selected_project",
            String(newVal)
          );
        else localStorage.removeItem("project_planning_selected_project");
      } catch (e) {
        console.warn("Could not persist selected project to localStorage", e);
      }
      if (!newVal) {
        this.selected_project_name = "";
        this.project_modules = [];
        return;
      }
      // handle id type differences (string vs number)
      const proj = this.projects.find((p) => String(p.id) === String(newVal));
      this.selected_project_name = proj ? proj.name : "";
      this.fetchProjectModules();
    },
  },
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.planner-container {
  margin: 0 auto;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  padding: 20px;
}

.planning-layout .planner-container {
  width: 100%;
}
.planning-layout .module-tasks {
  width: 100%;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  border-bottom: 2px solid #e0e0e0;
  padding-bottom: 15px;
}

.title {
  display: flex;
  align-items: baseline;
  gap: 10px;
}

.daily {
  font-size: 28px;
  font-weight: bold;
  color: #2d5a3d;
}

.planner {
  font-size: 20px;
  color: #ff6b35;
  font-weight: bold;
}

.date {
  font-size: 16px;
  color: #666;
  margin-left: 20px;
}

.calendar-widget {
  display: flex;
  align-items: center;
  gap: 10px;
}

.calendar-icon {
  width: 20px;
  height: 20px;
  background: #4a7c59;
  border-radius: 3px;
}

.week-days {
  display: flex;
  gap: 3px;
}

.day {
  width: 20px;
  height: 20px;
  border: 1px solid #ccc;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  background: white;
}

.day.today {
  background: #4a7c59;
  color: white;
}

.main-content {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 20px;
  margin-bottom: 20px;
}

.task-section {
  background: #f9f9f9;
  border-radius: 5px;
  padding: 15px;
}

.task-header {
  display: flex;
  justify-content: space-between;
  font-weight: bold;
  margin-bottom: 15px;
  color: #333;
}

.task-list {
  padding: 12px 0;
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}

.task-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 5px 0;
  border-bottom: 1px solid #e0e0e0;
}

.task-number {
  width: 30px;
  color: #666;
  font-size: 14px;
}

.task-input {
  flex: 1;
  border: none;
  background: transparent;
  padding: 5px;
  font-size: 14px;
}

.task-checkbox {
  width: 18px;
  height: 18px;
}

.right-panel {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.panel-section {
  background: #f9f9f9;
  border-radius: 5px;
  overflow: hidden;
}

.panel-header {
  background: #4a7c59;
  color: white;
  padding: 10px 15px;
  font-weight: bold;
  font-size: 14px;
}

.panel-content {
  padding: 15px;
  min-height: 120px;
  background: white;
}

.action-plan {
  margin-top: 20px;
}

.action-plan-header {
  background: #4a7c59;
  color: white;
  padding: 10px 15px;
  font-weight: bold;
  border-radius: 5px 5px 0 0;
}

.action-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}

.action-table th {
  background: #e8f5e8;
  padding: 10px;
  text-align: left;
  font-size: 12px;
  font-weight: bold;
  color: #2d5a3d;
  border: 1px solid #d0d0d0;
}

.action-table td {
  padding: 10px;
  border: 1px solid #d0d0d0;
  font-size: 12px;
}

.action-table tr:nth-child(even) {
  background: #f9f9f9;
}

.sl-column {
  width: 40px;
  text-align: center;
  font-weight: bold;
  color: #2d5a3d;
}

.date-column {
  width: 100px;
  color: #999;
  text-align: center;
}

.done-column {
  width: 50px;
  text-align: center;
}

@media (max-width: 768px) {
  .main-content {
    grid-template-columns: 1fr;
  }

  .header {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .planning-layout {
    grid-template-columns: 1fr;
  }
}

/* Module tasks (Google Tasks like) */
.module-tasks {
  margin: 20px auto;
  background: white;
  border-radius: 8px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
  overflow: hidden;
}
.module-tasks-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 18px;
  border-bottom: 1px solid #eef2ef;
}
.module-tasks-header h5 {
  margin: 0;
  color: #263b2e;
}
.task-controls {
  display: flex;
  gap: 8px;
  align-items: center;
}
.task-add-input {
  padding: 8px 12px;
  border-radius: 20px;
  border: 1px solid #dcdfe0;
  min-width: 260px;
}
.add-btn {
  background: #2d5a3d;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
}
.task-list {
  padding: 12px;
}
.task-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 8px;
  transition: background 0.12s ease, transform 0.06s ease;
  border: 1px solid transparent;
}
.task-item + .task-item {
  margin-top: 8px;
}
.task-item:hover {
  background: #f6faf6;
  transform: translateY(-1px);
  border-color: #eef6ed;
}
.task-left {
  width: 28px;
  text-align: center;
}
.task-main {
  flex: 1;
  min-width: 0;
}
.task-title {
  font-size: 15px;
  color: #1e3727;
  outline: none;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.task-meta {
  font-size: 12px;
  color: #7a7a7a;
  margin-top: 4px;
}
.task-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}
.btn-link {
  background: transparent;
  border: none;
  color: #2d5a3d;
  cursor: pointer;
}
.btn-link.text-danger {
  color: #c33;
}
.task-item.done .task-title {
  text-decoration: line-through;
  color: #8aa08b;
}
.empty-state {
  color: #888;
  padding: 12px;
  text-align: center;
  grid-column: 1 / -1;
}

/* Card styles for each module task */
.task-card {
  background: #ffffff;
  border: 1px solid #eef5ee;
  border-radius: 10px;
  padding: 12px;
  box-shadow: 0 4px 10px rgba(34, 34, 34, 0.03);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative; /* allow absolute-positioned action icons */
}
.task-card-header {
  display: flex;
  align-items: center;
  gap: 12px;
}
.task-card-title {
  flex: 1;
  font-weight: 600;
  font-size: 15px;
  color: #16331f;
  min-width: 0;
  outline: none;
}
.task-card-actions {
  display: flex;
  gap: 8px;
  position: absolute;
  top: 8px;
  right: 8px;
  z-index: 5;
}
.task-card .task-card-actions {
  opacity: 0;
  transform: translateY(-6px);
  transition: opacity 0.14s ease, transform 0.12s ease;
}
.task-card:hover .task-card-actions {
  opacity: 1;
  transform: translateY(0);
}
.due-badge {
  background: #f0f6f0;
  color: #2d5a3d;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  margin-left: 8px;
}
.icon-btn {
  background: transparent;
  border: none;
  padding: 6px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.icon-btn:hover {
  background: rgba(45, 90, 61, 0.06);
}
.icon-btn {
  border: 1px solid transparent;
  width: 36px;
  height: 36px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  cursor: pointer;
}
.icon-btn i {
  color: #ffffff;
}
.icon-btn.delete i {
  color: rgb(255, 255, 255);
}
.icon-btn:hover {
  background: rgb(45, 90, 61);
}
.icon-btn.delete:hover {
  background: rgb(195, 51, 51);
}
.task-card-body {
  padding-top: 8px;
  border-top: 1px dashed #eef6ef;
  margin-top: 8px;
}
.task-desc {
  min-height: 28px;
  color: #4b4b4b;
  outline: none;
}
.task-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
  gap: 12px;
}
.task-footer .due label {
  margin-right: 8px;
  color: #666;
}
.task-footer input[type="date"] {
  padding: 6px;
  border-radius: 6px;
  border: 1px solid #e3e7e5;
}
.task-card.done {
  opacity: 0.75;
}
/* Modal styles */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(18, 23, 16, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 60;
}
.modal-panel {
  background: white;
  width: 420px;
  border-radius: 10px;
  box-shadow: 0 20px 40px rgba(8, 12, 8, 0.25);
  overflow: hidden;
}
.modal-header {
  padding: 14px 18px;
  border-bottom: 1px solid #eef5ee;
}
.modal-body {
  padding: 12px 18px;
}
.form-row {
  margin-bottom: 10px;
  display: flex;
  flex-direction: column;
}
.form-row label {
  font-size: 13px;
  color: #444;
  margin-bottom: 6px;
}
.form-row input,
.form-row textarea {
  padding: 8px 10px;
  border: 1px solid #e0e4e1;
  border-radius: 6px;
}
.modal-footer {
  padding: 10px 18px;
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  border-top: 1px solid #eef5ee;
}
.modal-error {
  color: #c33;
  font-size: 13px;
  margin-top: 6px;
}

/* utility form classes added for improved reusability */
.project-select {
  padding: 6px 10px;
  border-radius: 6px;
  border: 1px solid #dcdfe0;
  background: white;
}
.project-select:focus {
  outline: 2px solid rgba(45, 90, 61, 0.15);
}
.input-transparent {
  border: none;
  width: 100%;
  background: transparent;
  padding: 6px 4px;
  font-size: 13px;
}
.input-transparent:focus {
  outline: none;
  background: #f6faf6;
}
.date-input {
  border: none;
  background: transparent;
  width: 100%;
  padding: 6px 4px;
  font-size: 13px;
}
.date-input:focus {
  outline: none;
  background: #f6faf6;
}
.done-checkbox {
  width: 18px;
  height: 18px;
}
</style>
