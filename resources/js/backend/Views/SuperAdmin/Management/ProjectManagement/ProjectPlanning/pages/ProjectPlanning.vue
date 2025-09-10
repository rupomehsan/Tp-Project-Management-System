<template>
  <div class="card" id="planning">
    <div class="card-header d-flex justify-content-between">
      <h5 class="text-capitalize">Project Planning</h5>

      <div>
        <button class="btn btn-primary p-2" @click="refreshData">
          <i class="fa fa-refresh"></i> Refresh
        </button>
        <button
          class="btn btn-primary p-2"
          @click="printPlanning"
          aria-label="Print planning"
        >
          <i class="fa fa-print"></i> Print
        </button>
      </div>
    </div>
    <div class="card-body card_body_fixed_height">
      <div class="planning-layout">
        <div class="row justify-content-center">
          <div
            class="col-md-4 left-panel p-1"
            ref="leftCol"
            :style="{
              width: leftWidth ? leftWidth + 'px' : null,
              flex: leftWidth ? '0 0 auto' : null,
            }"
            style="box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06)"
          >
            <div class="planner-container mb-5">
              <div class="header header--compact">
                <div class="header-controls">
                  <svg
                    width="28"
                    height="28"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                  >
                    <rect
                      x="3"
                      y="4"
                      width="18"
                      height="6"
                      rx="1"
                      fill="#2d5a3d"
                    />
                    <rect
                      x="3"
                      y="14"
                      width="10"
                      height="6"
                      rx="1"
                      fill="#4a7c59"
                    />
                  </svg>

                  <p class="text-dark">Projects</p>
                  <div class="select-wrap">
                    <select
                      id="project-select"
                      class="project-select"
                      v-model="selectedProjectId"
                      aria-label="Select project"
                    >
                      <option value="">Select Project</option>
                      <option
                        :value="value.id"
                        v-for="value in projects"
                        :key="value.id"
                      >
                        {{ value.name }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="action-plan">
                <h5 class="action-plan-header">{{ selected_project_name }}</h5>
                <div class="table-responsive">
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
                      <tr
                        v-for="(value, index) in project_modules"
                        :key="value.id"
                      >
                        <td class="sl-column">{{ index + 1 }}</td>
                        <td
                          @click="selectModule(value)"
                          :class="[
                            'module-cell',
                            { 'module-cell-active': value.is_show },
                          ]"
                          role="button"
                          tabindex="0"
                          @keydown.enter.prevent="selectModule(value)"
                          @keydown.space.prevent="selectModule(value)"
                        >
                          <div
                            class="module-name"
                            aria-label="Open module {{ value.module_name }}"
                            :title="value.module_name"
                          >
                            {{ value.module_name }}
                          </div>
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
                            @change="toggleModuleDone(value)"
                          />
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
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

                    <div v-if="modalError" class="modal-error">
                      {{ modalError }}
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button class="btn-link" @click="closeTaskModal">
                      Cancel
                    </button>
                    <button class="add-btn" @click="submitTaskModal">
                      {{ editingTaskId ? "Save" : "Create" }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- draggable resizer: drag to resize the left planner panel -->
          <div
            class="resizer"
            role="separator"
            aria-orientation="vertical"
            title="Drag to resize left panel"
            @mousedown.prevent="startResize"
            @touchstart.prevent="startResize"
          ></div>

          <div
            class="col-md-7 mx-3"
            style="box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06)"
          >
            <div class="module-tasks p-1">
              <div class="module-tasks-header">
                <h5>
                  {{ module_name || "All Module Management list" }}
                  <span v-if="hasUnsavedChanges" class="unsaved-indicator" title="You have unsaved changes">
                    ⚠️ Unsaved Changes
                  </span>
                </h5>
                <div class="task-controls">
                  <input
                    type="date"
                    v-model="newTaskTitle"
                    @keyup.enter="addModuleTask"
                    class="task-add-input"
                    placeholder="Quick add (press Enter)"
                  />
                  <button
                    :disabled="!canAdd"
                    :aria-disabled="!canAdd"
                    :tabindex="canAdd ? 0 : -1"
                    :class="[{ 'btn-secondary': !canAdd }, 'add-btn']"
                    type="button"
                    @click="canAdd && openTaskModal()"
                    :title="
                      canAdd ? 'Add management item' : 'Select a module to add'
                    "
                  >
                    Add
                  </button>
                </div>
              </div>
              <div class="row g-3 justify-content-start">
                <template
                  v-for="module in moduleGroups"
                  :key="module.moduleId || module.moduleName"
                >
                  <div class="col-12">
                    <div class="action-plan">
                      <div class="action-plan-header">
                        {{ module.moduleName }}
                      </div>
                    </div>
                  </div>

                  <div
                    class="col-md-10 mx-3 group-column"
                    v-for="group in module.groups"
                    :key="
                      (module.moduleId || module.moduleName) +
                      '::' +
                      group.dateKey
                    "
                  >
                    <div class="action-plan">
                      <div
                        class="action-plan-header action-plan-header--with-actions p-1"
                      >
                        <div>Date : {{ group.dateLabel }}</div>
                        <div class="group-actions">
                          <button
                            class="icon-btn"
                            type="button"
                            @click="openGroupEditModal(group)"
                            title="Edit group"
                          >
                            <!-- edit icon -->
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
                            <!-- delete icon -->
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
                      <div class="table-responsive">
                        <table class="action-table">
                          <thead>
                            <tr>
                              <th class="sl-column">Sl</th>
                              <th>Management Name</th>
                              <th class="done-column">Done</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr
                              v-for="(task, idx) in group.tasks"
                              :key="task.id"
                            >
                              <td class="sl-column">{{ idx + 1 }}</td>
                              <td>
                                <div class="task-row-inner">
                                  <div
                                    class="table-task-main"
                                    @dblclick="openEditModal(task)"
                                  >
                                    <p
                                      :title="task.title"
                                      class="text-dark no-margin"
                                    >
                                      {{ task.title }}
                                    </p>
                                    <small
                                      v-if="task.note"
                                      class="text-muted"
                                      >{{ task.note }}</small
                                    >
                                  </div>
                                  <div class="task-actions">
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
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button
        @click="submitPlanning"
        type="submit"
        class="btn btn-light btn-square px-5"
      >
        <i class="icon-lock"></i> Submit
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
      // resizer state
      leftWidth: null,
      isResizing: false,
      resizeStartX: 0,
      resizeStartWidth: 0,
      // track original state for unsaved changes detection
      originalModuleManagement: [],
      originalProjectModules: [],
      lastSubmittedState: null,
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
    // If none was restored and we have projects, auto-select the first so
    // the page shows modules/management items on initial load.
    if (
      !this.selectedProjectId &&
      Array.isArray(this.projects) &&
      this.projects.length
    ) {
      // set first project and fetch its modules
      this.selectedProjectId = this.projects[0].id;
      this.selected_project_name = this.projects[0].name || "";
      // ensure modules are fetched and module management aggregated
      await this.fetchProjectModules();
    }
  },
  mounted() {
    // initialize leftWidth to current rendered width of the left column
    this.$nextTick(() => {
      const el = this.$refs.leftCol;
      if (el && el.getBoundingClientRect) {
        this.leftWidth = Math.round(el.getBoundingClientRect().width);
      }
    });
    // bind move/end listeners on document for robust dragging
    document.addEventListener("mousemove", this.onMouseMove);
    document.addEventListener("mouseup", this.stopResize);
    document.addEventListener("touchmove", this.onMouseMove, {
      passive: false,
    });
    document.addEventListener("touchend", this.stopResize);
    
    // Add beforeunload handler for unsaved changes warning
    window.addEventListener("beforeunload", this.handleBeforeUnload);
  },
  beforeUnmount() {
    // cleanup listeners
    document.removeEventListener("mousemove", this.onMouseMove);
    document.removeEventListener("mouseup", this.stopResize);
    document.removeEventListener("touchmove", this.onMouseMove);
    document.removeEventListener("touchend", this.stopResize);
    window.removeEventListener("beforeunload", this.handleBeforeUnload);
  },
  methods: {
    // helper to group an array of tasks by a normalized date key
    groupByDate(items) {
      const groups = {};
      (items || []).forEach((t) => {
        // accept multiple possible date fields coming from backend/UI
        const rawDate = t.due || t.date || null;
        const key = rawDate
          ? new Date(rawDate).toISOString().split("T")[0]
          : "__no_date__";

        if (!groups[key])
          groups[key] = {
            dateKey: key,
            dateLabel: rawDate ? this.formatDate(rawDate) : "",
            tasks: [],
          };

        // push a normalized copy so downstream code can rely on `due` and boolean is_done
        const normalized = Object.assign({}, t, {
          due: rawDate,
          is_done:
            t.is_done === true ||
            t.is_done === 1 ||
            t.is_done === "1" ||
            t.is_done === "true"
              ? true
              : false,
        });

        groups[key].tasks.push(normalized);
      });

      // convert and sort: place '__no_date__' last, otherwise earliest-first
      return Object.values(groups).sort((a, b) => {
        if (a.dateKey === "__no_date__") return 1;
        if (b.dateKey === "__no_date__") return -1;
        return a.dateKey < b.dateKey ? -1 : 1;
      });
    },
    printPlanning() {
      // Build printable HTML: first page is module list with date, then module-wise pages
      const escape = (s) => this._escapeHtml(String(s || ""));

      const today = this.formatDate(new Date().toISOString());

      // First page: module list table
      const moduleRows =
        (this.project_modules || [])
          .map((m, idx) => {
            const start = m.start_date ? this.formatDate(m.start_date) : "";
            const end = m.end_date ? this.formatDate(m.end_date) : "";
            const done = m.is_done ? "Yes" : "No";
            return `
            <tr>
              <td style="padding:8px;border:1px solid #ddd;text-align:center">${
                idx + 1
              }</td>
              <td style="padding:8px;border:1px solid #ddd">${escape(
                m.module_name
              )}</td>
              <td style="padding:8px;border:1px solid #ddd;text-align:center">${escape(
                start
              )}</td>
              <td style="padding:8px;border:1px solid #ddd;text-align:center">${escape(
                end
              )}</td>
              <td style="padding:8px;border:1px solid #ddd;text-align:center">${escape(
                ""
              )}</td>
            </tr>`;
          })
          .join("\n") ||
        `<tr><td colspan=5 style="padding:8px;border:1px solid #ddd;text-align:center">No modules found</td></tr>`;

      // Per-module pages
      const modulesPages = (this.project_modules || [])
        .map((m) => {
          const groups = this.groupByDate(m.module_management || []);
          const groupsHtml =
            (groups || [])
              .map((g) => {
                const items = g.tasks || [];

                if ((items || []).length) {
                  const rows = items
                    .map((t, i) => {
                      const title = t.management_name || t.title || "Untitled";
                      const note = t.note
                        ? `<div style=\"color:#555;font-size:12px;margin-top:4px\">${escape(
                            t.note
                          )}</div>`
                        : "";
                      const done =
                        t.is_done === true ||
                        t.is_done === 1 ||
                        t.is_done === "1" ||
                        t.is_done === "true"
                          ? "Yes"
                          : "No";

                      if (i === 0) {
                        return `<tr>
                          <td style=\"padding:6px;border:1px solid #eee;width:140px;vertical-align:center;font-size:12px;text-align: center;\" rowspan=\"${
                            items.length
                          }\">${escape(g.dateLabel)}</td>
                          <td style=\"padding:6px;border:1px solid #eee;text-align:start;font-size:12px\">${
                            i + 1
                          }</td>
                          <td style=\"padding:6px;border:1px solid #eee\;font-size:12px">${escape(
                            title
                          )}${note}</td>
                          <td style=\"padding:6px;border:1px solid #eee;text-align:center\">${escape(
                            ""
                          )}</td>
                        </tr>`;
                      }

                      return `<tr>
                        <td style=\"padding:6px;border:1px solid #eee;text-align:start\">${
                          i + 1
                        }</td>
                        <td style=\"padding:6px;border:1px solid #eee\;font-size:12px">${escape(
                          title
                        )}${note}</td>
                        <td style=\"padding:6px;border:1px solid #eee;text-align:center\">${escape(
                          ""
                        )}</td>
                      </tr>`;
                    })
                    .join("");

                  return `
                <div style="margin-bottom:18px;text-color:black">
                  <table style="width:100%;border-collapse:collapse;margin-bottom:6px">
                    <thead>
                      <tr>
                        <th style="padding:6px;border:1px solid #ddd;width:140px;font-size:12px">Date</th>
                        <th style="padding:6px;border:1px solid #ddd;width:60px;font-size:12px">Sl</th>
                        <th style="padding:6px;border:1px solid #ddd;font-size:12px">Management Name</th>
                        <th style="padding:6px;border:1px solid #ddd;width:80px;text-align:center;font-size:12px">Done</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${rows}
                    </tbody>
                  </table>
                </div>`;
                }

                // no items: render date with a "No tasks" row
                return `
                <div style="margin-bottom:18px">
                  <table style="width:100%;border-collapse:collapse;margin-bottom:6px">
                    <thead>
                      <tr>
                        <th style="padding:6px;border:1px solid #ddd;width:140px">Date</th>
                        <th style="padding:6px;border:1px solid #ddd;width:60px">Sl</th>
                        <th style="padding:6px;border:1px solid #ddd">Management Name</th>
                        <th style="padding:6px;border:1px solid #ddd;width:80px;text-align:start">Done</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td style=\"padding:8px;border:1px solid #eee;vertical-align:center\">${escape(
                          g.dateLabel
                        )}</td>
                        <td colspan=\"3\" style=\"padding:8px;border:1px solid #eee;text-align:center\">No tasks</td>
                      </tr>
                    </tbody>
                  </table>
                </div>`;
              })
              .join("\n") ||
            '<div style="color:#666">No management items</div>';

          return `
            <div class="print-module-page">
              <h3 style="margin-bottom:6px">${escape(m.module_name)} :</h3>
              ${groupsHtml}
            </div>`;
        })
        .join("\n");

      const html = `
        <!doctype html>
        <html>
        <head>
          <meta charset="utf-8" />
          <title>Project Planning Print</title>
          <style>
            body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,'Helvetica Neue',Arial;padding:20px;color:#000000}
            .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
            table{width:100%;border-collapse:collapse;margin-top:8px}
            th{background:white;padding:10px;border:1px solid #ddd;text-align:left}
            td{padding:8px;border:1px solid #ddd}
            .page-break{page-break-after:always}
            @media print{ .no-print{display:none} }
          </style>
        </head>
        <body>
          <div style="text-align:center;margin-bottom:20px">

            <div >
              <h1 style="margin:0;font-size:25px">Project Planning</h1>
              <div style="color:#666;font-size:18px;font-weight:bold;margin-top:10px">Project: ${escape(
                this.selected_project_name || ""
              )}</div>
            </div>
           
          </div>

          <!-- First page: module list -->
          <div class="print-cover">
            <h3 style="margin-top:6px">Module List</h3>
            <table>
              <thead>
                <tr>
                  <th style="width:60px">Sl</th>
                  <th>Module</th>
                  <th style="width:120px;text-align:center">Start Date</th>
                  <th style="width:120px;text-align:center">End Date</th>
                  <th style="width:80px;text-align:center">Done</th>
                </tr>
              </thead>
              <tbody>
                ${moduleRows}
              </tbody>
            </table>
          </div>

          <div style="height:10px" class="page-break"></div>

          <!-- Module-wise pages -->
          ${modulesPages}

        </body>
        </html>
      `;

      const w = window.open("", "_blank");
      if (!w) {
        alert("Please allow popups to print");
        return;
      }
      w.document.open();
      w.document.write(html);
      w.document.close();
      // give browser a moment to render then trigger print
      setTimeout(() => {
        try {
          w.focus();
          w.print();
          // keep window open for user to inspect; close after print dialog in some browsers
          // w.close();
        } catch (e) {
          console.error("Print failed", e);
        }
      }, 500);
    },
    _escapeHtml(str) {
      return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    },
    // select a module: toggle UI and load its management items into module_management
    selectModule(module) {
      // Check for unsaved changes before switching modules
      if (this.hasUnsavedChanges && this.selectedModuleId && this.selectedModuleId !== module.id) {
        const confirmed = confirm(
          "You have unsaved changes in the current module. Do you want to discard these changes and switch to the new module?"
        );
        if (!confirmed) {
          return; // User cancelled, stay on current module
        }
      }

      // If clicked module is already selected, deselect and show aggregated view
      if (
        this.selectedModuleId &&
        String(this.selectedModuleId) === String(module.id)
      ) {
        // hide any active flags
        this.project_modules.forEach((item) => (item.is_show = false));
        this.selectedModuleId = null;
        this.module_name = "";
        this.originalModuleManagement = [];
        this.originalProjectModules = [];
        // restore aggregated management items from all modules
        const aggregated = [];
        (this.project_modules || []).forEach((mod) => {
          (mod.module_management || []).forEach((it) => {
            aggregated.push({
              id: it.id || Date.now() + Math.random(),
              title: it.management_name || it.title || "Untitled",
              note: it.note || null,
              due: it.date || it.due || null,
              is_done:
                it.is_done === true ||
                it.is_done === 1 ||
                it.is_done === "1" ||
                it.is_done === "true"
                  ? true
                  : false,
              module_id: it.module_id || mod.id,
            });
          });
        });
        this.module_management = aggregated;
        return;
      }

      // otherwise select this module
      this.project_modules.forEach((item) => {
        if (item.id !== module.id) item.is_show = false;
      });
      module.is_show = true;
      this.module_name = module.module_name;
      this.selectedModuleId = module.id;

      const raw = module.module_management || [];
      const managementItems = (raw || []).map((m) => ({
        id: m.id,
        title: m.management_name || m.title || "Untitled",
        note: m.note || null,
        due: m.date || m.due || null,
        is_done:
          m.is_done === true ||
          m.is_done === 1 ||
          m.is_done === "1" ||
          m.is_done === "true"
            ? true
            : false,
        module_id: m.module_id || module.id,
      }));
      
      this.module_management = managementItems;
      // Store original state for comparison
      this.originalModuleManagement = JSON.parse(JSON.stringify(managementItems));
      this.originalProjectModules = JSON.parse(JSON.stringify(this.project_modules));
    },
    // Resizer handlers
    startResize(e) {
      // support touch and mouse
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      this.isResizing = true;
      this.resizeStartX = clientX;
      const el = this.$refs.leftCol;
      this.resizeStartWidth = el
        ? el.getBoundingClientRect().width
        : this.leftWidth || 320;
    },
    onMouseMove(e) {
      if (!this.isResizing) return;
      // prevent page scrolling while resizing on touch
      if (e.cancelable) e.preventDefault();
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const dx = clientX - this.resizeStartX;
      let newWidth = Math.round(this.resizeStartWidth + dx);
      // constraints: min 260px, max 70% of window width
      const min = 260;
      const max = Math.round(window.innerWidth * 0.72);
      if (newWidth < min) newWidth = min;
      if (newWidth > max) newWidth = max;
      this.leftWidth = newWidth;
    },
    stopResize() {
      if (!this.isResizing) return;
      this.isResizing = false;
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
      
      // Store current selection to restore after refresh
      const currentSelectedModuleId = this.selectedModuleId;
      const currentModuleName = this.module_name;
      
      try {
        const response = await axios.get(
          `/project-modules?get_all=1&project_id=${this.selectedProjectId}&with_management=true`
        );
        // ensure reactive array and attach module_management
        const modules = (response.data?.data || []).map((m) => ({
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
          module_management: m.module_management || [],
          // Restore is_show state if this was the previously selected module
          is_show: currentSelectedModuleId && String(m.id) === String(currentSelectedModuleId)
        }));

        this.project_modules = modules;

        // If there was a previously selected module, restore its selection and management items
        if (currentSelectedModuleId) {
          const selectedModule = modules.find(m => String(m.id) === String(currentSelectedModuleId));
          if (selectedModule) {
            this.selectedModuleId = currentSelectedModuleId;
            this.module_name = currentModuleName || selectedModule.module_name;
            
            // Load management items for the selected module
            const raw = selectedModule.module_management || [];
            const managementItems = raw.map((m) => ({
              id: m.id,
              title: m.management_name || m.title || "Untitled",
              note: m.note || null,
              due: m.date || m.due || null,
              is_done:
                m.is_done === true ||
                m.is_done === 1 ||
                m.is_done === "1" ||
                m.is_done === "true"
                  ? true
                  : false,
              module_id: m.module_id || selectedModule.id,
            }));
            
            this.module_management = managementItems;
            // Update original state for change tracking
            this.originalModuleManagement = JSON.parse(JSON.stringify(managementItems));
            this.originalProjectModules = JSON.parse(JSON.stringify(this.project_modules));
            return; // Exit early to avoid loading aggregated view
          }
        }

        // If no specific module was selected, show aggregated management items from all modules
        const aggregated = [];
        modules.forEach((mod) => {
          (mod.module_management || []).forEach((it) => {
            aggregated.push({
              id: it.id || Date.now() + Math.random(),
              title: it.management_name || it.title || "Untitled",
              note: it.note || null,
              due: it.date || it.due || null,
              is_done:
                it.is_done === true ||
                it.is_done === 1 ||
                it.is_done === "1" ||
                it.is_done === "true"
                  ? true
                  : false,
              module_id: it.module_id || mod.id,
            });
          });
        });

        this.module_management = aggregated;
        this.selectedModuleId = null;
        this.module_name = "";
        this.originalModuleManagement = [];
        this.originalProjectModules = JSON.parse(JSON.stringify(this.project_modules));
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
    // Handle beforeunload to warn about unsaved changes
    handleBeforeUnload(event) {
      if (this.hasUnsavedChanges) {
        const message = "You have unsaved changes. Are you sure you want to leave?";
        event.returnValue = message;
        return message;
      }
    },
    // Refresh data from server
    async refreshData() {
      try {
        // First refresh projects list
        await this.get_all_projects();
        
        // If a project is selected, refresh its modules (this will preserve current module selection)
        if (this.selectedProjectId) {
          await this.fetchProjectModules();
        }
        
        console.log("Data refreshed successfully");
      } catch (error) {
        console.error("Error refreshing data:", error);
        alert("Failed to refresh data. Please try again.");
      }
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
        console.log('Submitting planning data:', payload);
        const res = await axios.post("/project-plans/store", payload);
        console.log('Planning save response:', res);
        // You can customize success handling
        console.log("Project planning saved", res.data);
        alert("Project planning saved successfully.");
        
        // Refresh data from server after successful save
        await this.fetchProjectModules();
        
        // Reset original state after successful submission and refresh
        if (this.selectedModuleId) {
          const currentItems = this.module_management.filter(
            (item) => String(item.module_id) === String(this.selectedModuleId)
          );
          this.originalModuleManagement = JSON.parse(JSON.stringify(currentItems));
        }
        this.originalProjectModules = JSON.parse(JSON.stringify(this.project_modules));
      } catch (err) {
        console.error("Error submitting project planning", err);
        console.error("Error response:", err.response);
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
    async confirmDeleteSingle(task) {
      if (!task) return;
      const ok = confirm(`Delete "${task.title}"?`);
      if (!ok) return;
      try {
        const { data } = await axios.post(
          `/project-plans/project-module-management/delete/${task.id}`
        );
        if (data?.status === "success") {
          const id = task.id;

          // Remove from aggregated module_management array (if present)
          const idx = this.module_management.findIndex(
            (t) => String(t.id) === String(id)
          );
          if (idx !== -1) this.module_management.splice(idx, 1);

          // Also remove from any module.module_management nested arrays so module-based view updates
          (this.project_modules || []).forEach((mod) => {
            if (!Array.isArray(mod.module_management)) return;
            const mIdx = mod.module_management.findIndex((t) => {
              // backend items may use id or local_id; compare as strings to be safe
              return (
                String(t.id) === String(id) || String(t.local_id) === String(id)
              );
            });
            if (mIdx !== -1) mod.module_management.splice(mIdx, 1);
          });

          // If the selected module's detailed array is derived from project_modules, ensure module_management reactive state is consistent.
          if (this.selectedModuleId) {
            // If module was loaded into this.module_management from its parent module, also remove any matching item here
            const selIdx = this.module_management.findIndex(
              (t) => String(t.id) === String(id)
            );
            if (selIdx !== -1) this.module_management.splice(selIdx, 1);
          }
        }
      } catch (error) {
        console.error(error);
      }
    },
    toggleDone(task) {
      // keep reactivity simple — if you need to persist, call API here
      // normalize to use `is_done` across the codebase
      task.is_done = !task.is_done;
    },
    toggleModuleDone(module) {
      // Toggle module done status
      console.log('Toggling module done:', module.id, 'from', module.is_done, 'to', !module.is_done);
      module.is_done = !module.is_done;
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
        const date = new Date(d);
        if (isNaN(date.getTime())) return d;
        const day = String(date.getDate()).padStart(2, '0');
        const months = [
          'jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'
        ];
        const month = months[date.getMonth()];
        const year = String(date.getFullYear()).slice(-2);
        return `${day} ${month} ${year}`;
      } catch (e) {
        return d;
      }
    },
  },
  computed: {
    groupedModuleManagement() {
      const groups = {};
      // if a module is selected, only include management items for that module
      let items = this.module_management || [] || [];
      if (this.selectedModuleId) {
        items = items.filter(
          (t) => String(t.module_id) === String(this.selectedModuleId)
        );
      }

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
    // When no specific module is selected, present module-level groups:
    // [{ moduleId, moduleName, groups: [ {dateKey, dateLabel, tasks: []} ] }]
    moduleGroups() {
      // if a module is explicitly selected, reuse groupedModuleManagement as a single virtual module
      if (this.selectedModuleId) {
        return [
          {
            moduleId: this.selectedModuleId,
            moduleName: this.module_name || "Module",
            groups: this.groupedModuleManagement,
          },
        ];
      }

      // otherwise build groups per module from project_modules and module_management fallback
      const mods = this.project_modules || [];
      // If there are explicit module objects with module_management, use them
      if (mods.length) {
        return mods.map((m) => ({
          moduleId: m.id,
          moduleName: m.module_name || m.name || `Module ${m.id}`,
          groups: this.groupByDate(
            (m.module_management || []).map((it) => ({
              id: it.id || Date.now() + Math.random(),
              title: it.management_name || it.title || "Untitled",
              note: it.note || null,
              due: it.date || it.due || null,
              is_done:
                it.is_done === true ||
                it.is_done === 1 ||
                it.is_done === "1" ||
                it.is_done === "true",
              module_id: it.module_id || m.id,
            }))
          ),
        }));
      }

      // Fallback: group the aggregated module_management array by module id
      const byModule = {};
      (this.module_management || []).forEach((t) => {
        const mid = t.module_id || "__no_module__";
        if (!byModule[mid])
          byModule[mid] = {
            moduleId: mid,
            moduleName: `Module ${mid}`,
            items: [],
          };
        byModule[mid].items.push(t);
      });
      return Object.values(byModule).map((m) => ({
        moduleId: m.moduleId,
        moduleName: m.moduleName,
        groups: this.groupByDate(m.items),
      }));
    },
    // whether the Add button should be active
    canAdd() {
      // require a selected module (prefer explicit id) or a module name
      // this centralizes the condition and allows future extensions
      return Boolean(this.selectedModuleId || this.module_name);
    },
    // check if there are unsaved changes in management items or modules
    hasUnsavedChanges() {
      // Check for module changes
      if (this.project_modules.length !== this.originalProjectModules.length) {
        return true;
      }
      
      for (let i = 0; i < this.project_modules.length; i++) {
        const current = this.project_modules[i];
        const original = this.originalProjectModules.find(module => module.id === current.id);
        
        if (!original) {
          return true;
        }
        
        if (
          current.start_date !== original.start_date ||
          current.end_date !== original.end_date ||
          !!current.is_done !== !!original.is_done
        ) {
          return true;
        }
      }
      
      if (!this.selectedModuleId) {
        return false;
      }
      
      // Compare current module_management with original state
      const currentItems = this.module_management.filter(
        (item) => String(item.module_id) === String(this.selectedModuleId)
      );
      
      // If there's no original state but we have current items, that means unsaved changes
      if (this.originalModuleManagement.length === 0 && currentItems.length > 0) {
        return true;
      }
      
      // Check if lengths are different
      if (currentItems.length !== this.originalModuleManagement.length) {
        return true;
      }
      
      // Check if any item has been modified
      for (let i = 0; i < currentItems.length; i++) {
        const current = currentItems[i];
        const original = this.originalModuleManagement.find(item => item.id === current.id);
        
        if (!original) {
          // New item added
          return true;
        }
        
        // Check if any field has changed
        if (
          current.title !== original.title ||
          current.note !== original.note ||
          current.due !== original.due ||
          current.is_done !== original.is_done
        ) {
          return true;
        }
      }
      
      return false;
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
/* Reset only inside this component */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Outer card: clean surface with subtle border and shadow */
.card {
  /* slightly darker, soft green-tinged surface that reads as a single panel */
  background: #eef7ee; /* darker than plain white */
  border-radius: 12px;
  border: 1px solid #dfeee0;
  box-shadow: 0 10px 36px rgba(12, 34, 18, 0.08);
  overflow: hidden;
  height: 88vh;
}

.planner-container {
  margin: 0 auto;
  background: transparent; /* card handles surface */
  border-radius: 8px;
  padding: 0;
}

/* left panel when resized */
.left-panel {
  min-width: 260px; /* same as JS minimum */
  max-width: 72vw;
  transition: width 120ms ease;
}

/* draggable vertical resizer/gutter */
.resizer {
  width: 10px;
  cursor: col-resize;
  background: transparent;
  display: inline-block;
  position: relative;
  margin: 0 6px;
}
.resizer::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 2px;
  height: 48px;
  background: rgba(45, 90, 61, 0.12);
  border-radius: 2px;
}
.resizer:active::before,
.resizer:hover::before {
  background: rgba(45, 90, 61, 0.22);
}

.planning-layout .planner-container {
  width: 100%;
}
.planning-layout .module-tasks {
  width: 100%;
}

/* Card header inside the outer card */
#planning .card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 16px 20px;
  background: linear-gradient(90deg, #e9f6e9 0%, #eef7ee 100%);
  border-bottom: 1px solid #dbeee0;
}

#planning .card-header .text-capitalize {
  margin: 0;
  font-size: 18px;
  color: #16331f;
  font-weight: 700;
}

#planning .card-header > div:last-child {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Prevent children from overflowing the container */
/* internal compact header for left-panel widgets */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  padding: 18px;
  background: #ffffff;
}

.title {
  display: flex;
  align-items: baseline;
  gap: 10px;
}

/* allow the title to shrink on small screens so controls don't overflow */
.title {
  flex: 1 1 auto;
  min-width: 0; /* allow flex children to shrink properly */
}

.daily {
  font-size: 20px;
  font-weight: 700;
  color: #2d5a3d;
  letter-spacing: 0.2px;
}

.header-subtitle {
  font-size: 12px;
  color: #6b766b;
  margin-top: 2px;
}

.header-controls {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 12px;
}

/* keep controls fixed size and avoid pushing outside */
.header-controls {
  flex: 0 0 auto;
}

.select-wrap {
  position: relative;
  display: inline-flex;
  align-items: center;
  background: #ffffff;
  border-radius: 8px;
  padding: 6px 10px;
  border: 1px solid #e6ece6;
  box-shadow: 0 2px 8px rgba(41, 56, 41, 0.04);
}

.select-wrap {
  max-width: 260px;
  width: 100%;
}

.select-wrap .project-select {
  width: 100%;
  background: transparent;
  border: none;
  padding: 6px 4px;
}

.select-wrap:focus-within {
  box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);
  border-color: rgba(45, 90, 61, 0.18);
}

.sr-only {
  position: absolute !important;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

.planner {
  font-size: 20px;
  color: #ff6b35;
  font-weight: 700;
}

/* helper classes for layout */
.header--compact {
  padding: 8px 0 18px 0;
}
.header-title-row {
  display: flex;
  align-items: center;
  gap: 10px;
}
.group-column {
  margin-bottom: 18px;
}
.action-plan-header--with-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.task-row-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}
.table-task-main {
  flex: 1;
  cursor: text;
  min-width: 0;
}
.no-margin {
  margin: 0;
}
.submit-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 16px;
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
  margin-top: 18px;
}

.action-plan-header {
  background: #4a7c59;
  color: white;
  padding: 10px 14px;
  font-weight: 700;
  border-radius: 6px 6px 0 0;
}

.action-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}

/* Responsive table wrapper */
.table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.action-table {
  table-layout: fixed; /* help prevent overflow */
}

.action-table td,
.action-table th {
  overflow-wrap: break-word;
  word-break: break-word;
}

/* Ensure sl/date/done columns keep minimal width but wrap content if needed */
.sl-column,
.date-column,
.done-column {
  white-space: nowrap;
}

.action-table th {
  background: #f0fbf3;
  padding: 10px 12px;
  text-align: left;
  font-size: 13px;
  font-weight: 700;
  color: #2d5a3d;
  border: 1px solid #e3e9e3;
}

.action-table td {
  padding: 10px 12px;
  border: 1px solid #edf3ed;
  font-size: 13px;
  color: #263b2e;
}

.action-table tr:nth-child(even) {
  background: #fbfdfb;
}

.sl-column {
  width: 40px;
  text-align: center;
  font-weight: bold;
  color: #2d5a3d;
}

.date-column {
  width: 130px;
  color: #999;
  text-align: center;
}

.done-column {
  width: 50px;
  text-align: center;
}

/* Module cell improvements */
.module-cell {
  cursor: pointer;
  transition: background-color 0.16s ease, color 0.12s ease,
    transform 0.06s ease, box-shadow 0.12s ease;
  vertical-align: middle;
  padding: 12px 10px;
  background: transparent;

}
.module-cell:focus {
  outline: 2px solid rgba(45, 90, 61, 0.12);
  outline-offset: 2px;
}

.module-cell-active {
  background: linear-gradient(180deg, #00c500 0%, #00ff00 100%);
  color: #0f3d1f;
  box-shadow: 0 4px 12px rgba(34, 50, 34, 0.06);
}
.module-name {
  font-size: 14px;
  font-weight: 600;
  color: #16331f;
  white-space: normal;
  overflow-wrap: anywhere;
}

@media (min-width: 992px) {
  /* keep single-line module name on larger screens */
  .module-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}

/* Table refinements */
.action-table th,
.action-table td {
  padding: 10px 12px;
}
.action-table thead th {
  background: #f0fbf0;
}
.action-table tbody tr {
  transition: background 0.12s ease, box-shadow 0.12s ease;
}
.action-table tbody tr:hover {
  background: #fbfffb;
}

/* Make date inputs and checkboxes clearer */
.date-input {
  border-radius: 6px;
  padding: 6px 8px;
  border: 1px solid #e6ece6;
  background: #fff;
  width: 100%;
}
.date-input:focus {
  outline: 2px solid rgba(45, 90, 61, 0.12);
}
.done-checkbox {
  width: 18px;
  height: 18px;
  accent-color: #2d5a3d;
}

/* Tighter card styles */
.action-plan {
  margin-top: 18px;
}
.module-tasks {
  padding: 0;
}
.module-tasks-header {
  padding: 10px 12px;
}
.task-add-input {
  min-width: 200px;
}

@media (max-width: 992px) {
  .task-add-input {
    min-width: 160px;
  }
  .action-table th,
  .action-table td {
    padding: 8px 10px;
    font-size: 13px;
  }
  /* allow horizontal scroll for the table on smaller screens */
  .table-responsive {
    overflow-x: auto;
  }
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
  border-radius: 8px;
  cursor: pointer;
  box-shadow: 0 6px 18px rgba(45, 90, 61, 0.08);
}
/* visually disabled state for Add button */
.add-btn[disabled],
.add-btn[aria-disabled="true"],
.add-btn.btn-secondary {
  background: #9aa89a; /* muted green */
  color: #ffffff;
  opacity: 0.6;
  cursor: not-allowed;
  pointer-events: none; /* avoid hover effects */
  box-shadow: none;
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
  background: rgba(255, 255, 255, 0.425);
}
.icon-btn {
  border: 1px solid #00000047;
  width: 36px;
  height: 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  cursor: pointer;
  background: #ededed;
  margin: 0px 2px;
}
.icon-btn i {
  color: #ffffff;
}
.icon-btn.delete i {
  color: rgb(255, 255, 255);
}
.icon-btn:hover {
  background: rgb(255, 255, 255);
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
  z-index: 999999;
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

/* Card body surface inside the outer .card */
.card-body {
  padding: 18px 20px;
  /* deeper, unified panel background to contrast with page background */
  background: linear-gradient(180deg, #f3faf3 0%, #eef7ee 100%);
}

/* Footer: align actions to the right with clear spacing */
.card-footer {
  padding: 14px 20px;
  background: linear-gradient(180deg, #eef7ee 0%, #eaf6ea 100%);
  border-top: 1px solid #dbeee0;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.card-footer .btn {
  background: #2d5a3d;
  color: #fff;
  border-radius: 8px;
  padding: 8px 18px;
  border: none;
  box-shadow: 0 8px 20px rgba(45, 90, 61, 0.08);
  transition: transform 0.12s ease, box-shadow 0.12s ease;
}

.card-footer .btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(45, 90, 61, 0.12);
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

/* Unsaved changes indicator */
.unsaved-indicator {
  color: #ff6b35;
  font-size: 12px;
  font-weight: 600;
  margin-left: 10px;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: 0.6; }
  100% { opacity: 1; }
}
</style>
