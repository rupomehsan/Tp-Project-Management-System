<template>
  <div>
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5 class="text-capitalize">
            {{
              param_id
                ? `${setup.edit_page_title}`
                : `${setup.create_page_title}`
            }}
          </h5>
          <div>
            <router-link
              class="btn btn-outline-warning btn-sm"
              :to="{ name: `All${setup.route_prefix}` }"
            >
              {{ setup.all_page_title }}
            </router-link>
          </div>
        </div>
        <div class="card-body card_body_fixed_height" v-if="is_loaded">
          <div class="row">
            <TaskGroupDropDownEl
              :name="'task_group_id'"
              :multiple="false"
              :value="item.task_group_id"
            />

            <ProjectDropDownEl
              :name="'project_id'"
              :multiple="false"
              :value="item.project_id"
            />

            <user-drop-down-el
              :name="'assigned_to'"
              :multiple="false"
              :value="item.assigned_to"
            />

            <!-- <div class="col-md-6">
              <div class="form-group">
                <label for="">assigned_to</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.assigned_to"
                    class="form-control"
                    name="assigned_to"
                    id="assigned_to"
                  >
                    <option value="">Selet--- Engineer</option>
                    <option
                      v-for="item in userData?.data"
                      :key="item.id"
                      :value="item.id"
                    >
                      {{ item.name }}
                    </option>
                  </select>
                </div>
              </div>
            </div> -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Title</label>
                <div class="mt-1 mb-3">
                  <input
                    class="form-control form-control-square mb-2"
                    type="text"
                    name="title"
                    id="title"
                    v-model="form_fields.title"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Priority</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.priority"
                    class="form-control form-control-square mb-2"
                    name="priority"
                    id="priority"
                  >
                    <option value="low">Low</option>
                    <option value="normal">Normal</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Task status</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.task_status"
                    class="form-control form-control-square mb-2"
                    name="task_status"
                    id="task_status"
                  >
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Not Completed">Not Completed</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Developer status</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.task_user_status"
                    class="form-control form-control-square mb-2"
                    name="task_user_status"
                    id="task_user_status"
                  >
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="Not Completed">Not Completed</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="start_date">Start Date</label>
                <div class="mt-1 mb-3">
                  <input
                    type="datetime-local"
                    class="form-control form-control-square mb-2"
                    name="start_date"
                    id="start_date"
                    v-model="form_fields.start_date"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="end_date">End Date</label>
                <div class="mt-1 mb-3">
                  <input
                    type="datetime-local"
                    class="form-control form-control-square mb-2"
                    name="end_date"
                    id="end_date"
                    v-model="form_fields.end_date"
                  />
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label for="">description</label>
                <div class="mt-1 mb-3">
                  <text-editor
                    :name="'description'"
                    :value="form_fields.description"
                    @input="form_fields.description = $event"
                  />
                  <!-- <textarea 
                    v-model="form_fields.description"
                    name="description"
                    id="description" 
                    rows="5" 
                    cols="55">
                  </textarea> -->
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-light btn-square px-5">
            <i class="icon-lock"></i> Submit
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { store } from "../store";
import setup from "../setup";
import TextEditor from "../../../../../../GlobalComponents/FormComponents/TextEditor.vue";
import TaskGroupDropDownEl from "../../../TasksManagement/TaskGroup/components/dropdown/DropDownEl.vue";
import ProjectDropDownEl from "../../../ProjectManagement/Project/components/dropdown/DropDownEl.vue";
import UserDropDownEl from "../../../UserManagement/User/components/dropdown/DropDownEl.vue";

export default {
  components: {
    TextEditor,
    TaskGroupDropDownEl,
    ProjectDropDownEl,
    UserDropDownEl,
  },
  data: () => ({
    setup,
    param_id: null,
    form_fields: {
      title: "",
      description: "",
      start_date: "",
      end_date: "",
      task_status: "",
      task_user_status: "",
      priority: "",
    },
    userProject: [],
    userData: [],
    is_loaded: false,
  }),
  created: async function () {
    let id = (this.param_id = this.$route.params.id);
    
    // Initialize item properties safely
    if (!this.item) {
      // If item doesn't exist, initialize with empty object
      this.set_item_defaults();
    } else {
      // Ensure the dropdown properties are arrays
      if (!Array.isArray(this.item.task_group_id)) {
        this.item.task_group_id = [];
      }
      if (!Array.isArray(this.item.project_id)) {
        this.item.project_id = [];
      }
      if (!Array.isArray(this.item.assigned_to)) {
        this.item.assigned_to = [];
      }
    }

    if (id) {
      this.set_fields(id);
    }

    await this.get_user_data();

    this.is_loaded = true; // ✅ Set is_loaded to true after fetching data
  },
  methods: {
    ...mapActions(store, {
      create: "create",
      update: "update",
      details: "details",
      get_all: "get_all",
      set_only_latest_data: "set_only_latest_data",
      set_item: "set_item",
    }),

    set_item_defaults: function() {
      // Initialize item with default values to prevent null access errors
      if (!this.item || Object.keys(this.item).length === 0) {
        // Use the store's set_item action to properly initialize
        this.set_item({
          task_group_id: [],
          project_id: [],
          assigned_to: []
        });
      }
    },

    // async get_project_data() {
    //   try {
    //     let res = await axios.get("/project");
    //     this.userProject = res.data.data; // ✅ Assign data properly
    //   } catch (error) {
    //     console.error("Error fetching project group", error);
    //   }
    // },
    async get_user_data() {
      try {
        let res = await axios.get("/users");
        this.userData = res.data.data; // ✅ Assign data properly
      } catch (error) {
        console.error("Error fetching user", error);
      }
    },

    set_fields: async function (id) {
      this.param_id = id;
      await this.details(id);
      if (this.item) {
        console.log('Original item data:', JSON.parse(JSON.stringify(this.item)));
        
        // Initialize dropdown arrays safely - handle both objects and primitive values
        
        // Handle task_group_id
        if (!Array.isArray(this.item.task_group_id)) {
          if (this.item.task_group_id && typeof this.item.task_group_id === 'object') {
            // If it's an object, wrap it in an array
            this.item.task_group_id = [this.item.task_group_id];
          } else if (this.item.task_group_id) {
            // If it's a primitive value, wrap it in an array
            this.item.task_group_id = [this.item.task_group_id];
          } else {
            this.item.task_group_id = [];
          }
        }
        
        // Handle project_id  
        if (!Array.isArray(this.item.project_id)) {
          if (this.item.project_id && typeof this.item.project_id === 'object') {
            // If it's an object, wrap it in an array
            this.item.project_id = [this.item.project_id];
          } else if (this.item.project_id) {
            // If it's a primitive value, wrap it in an array
            this.item.project_id = [this.item.project_id];
          } else {
            this.item.project_id = [];
          }
        }
        
        // Handle assigned_to
        if (!Array.isArray(this.item.assigned_to)) {
          if (this.item.assigned_to && typeof this.item.assigned_to === 'object') {
            // If it's an object, wrap it in an array
            this.item.assigned_to = [this.item.assigned_to];
          } else if (this.item.assigned_to && this.item.user) {
            // If assigned_to is an ID and we have the user object, use the user object
            this.item.assigned_to = [this.item.user];
          } else if (this.item.assigned_to) {
            // If it's a primitive value, wrap it in an array
            this.item.assigned_to = [this.item.assigned_to];
          } else {
            this.item.assigned_to = [];
          }
        }
        
        console.log('Processed item data:', {
          task_group_id: this.item.task_group_id,
          project_id: this.item.project_id,
          assigned_to: this.item.assigned_to
        });
        
        // Set form fields
        // this.form_fields.assigned_to = this.item.assigned_to;
        this.form_fields.title = this.item.title;
        this.form_fields.description = this.item.description; // Set description in form_fields
        this.form_fields.start_date = this.item.start_date;
        this.form_fields.end_date = this.item.end_date;
        this.form_fields.task_status = this.item.task_status;
        this.form_fields.task_user_status = this.item.task_user_status;
        this.form_fields.priority = this.item.priority;
      }
    },

    submitHandler: async function ($event) {
      // this.set_only_latest_data(true);
      if (this.param_id) {
        this.setSummerEditor();
        let response = await this.update($event);
        // await this.get_all();
        if ([200, 201].includes(response.status)) {
          window.s_alert("Data successfully updated");
          this.$router.push({
            name: `Details${this.setup.route_prefix}`,
          });
        }
      } else {
        this.setSummerEditor();
        let response = await this.create($event);
        // await this.get_all();
        if ([200, 201].includes(response.status)) {
          window.s_alert("Data Successfully Created");
          // this.$router.push({
          //   name: `All${this.setup.route_prefix}`,
          // });
        }
      }
    },
    setSummerEditor() {
      var markupStr = $("#description").summernote("code");
      var target = document.createElement("input");
      target.setAttribute("name", "description");
      target.value = markupStr;
      document.getElementById("description").appendChild(target);
    },
    async onProjectChange() {
      await this.get_user_by_project_id();
      this.form_fields.assigned_to = "";
    },
    async get_user_by_project_id() {
      if (!this.form_fields.project_id) {
        this.userData = [];
        return;
      }
      try {
        let res = await axios.get(
          `users/get-users-by-project-id/${this.form_fields.project_id}`
        );
        this.userData = res.data;
      } catch (error) {
        this.userData = [];
        console.error("Error fetching users by project id", error);
      }
    },
  },
  computed: {
    ...mapState(store, {
      item: "item",
    }),
  },
  watch: {
    "form_fields.project_id": {
      handler(newVal) {
        if (newVal) {
          this.get_user_by_project_id();
        }
      },
    },
  },
};
</script>
