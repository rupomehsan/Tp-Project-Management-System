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
        <div class="card-body card_body_fixed_height">
          <div class="row">
            <!-- <div class="col-md-6">
              <div class="form-group">
                <label for="">Project group Name</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.project_group_id"
                    class="form-control"
                    name="project_group_id"
                    id="project_group_id"
                  >
                    <option value="">Selet-- Project group Name</option>
                    <option
                      v-for="item in userProjectGroup?.data"
                      :key="item.id"
                      :value="item.id"
                    >
                      {{ item.name }}
                    </option>
                  </select>
                </div>
              </div>
            </div> -->
            <ProjectGroupDropDownEl
              :name="'project_group_id'"
              :multiple="false"
              :value="[item.project_group_id]"
            />
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Project Name</label>
                <div class="mt-1 mb-3">
                  <input
                    class="form-control form-control-square mb-2"
                    type="text"
                    name="name"
                    id="name"
                    v-model="form_fields.name"
                  />
                </div>
              </div>
            </div>

            <user-drop-down-el
              :name="'project_users'"
              :multiple="true"
              :value="item.project_users"
            />

            <div class="col-md-6">
              <div class="form-group">
                <label for="start_date">Start Date</label>
                <div class="mt-1 mb-3">
                  <input
                    type="date"
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
                    type="date"
                    class="form-control form-control-square mb-2"
                    name="end_date"
                    id="end_date"
                    v-model="form_fields.end_date"
                  />
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="project_link">Project Link</label>
                <div class="mt-1 mb-3">
                  <input
                    type="url"
                    class="form-control form-control-square mb-2"
                    name="project_link"
                    id="project_link"
                    v-model="form_fields.project_link"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="project_progress">Project Progress (%)</label>
                <div class="mt-1 mb-3">
                  <input
                    type="number"
                    class="form-control form-control-square mb-2"
                    name="project_progress"
                    id="project_progress"
                    v-model="form_fields.project_progress"
                    min="0"
                    max="100"
                  />
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="project_document">Project Document</label>
                <span v-if="form_fields.project_document">
                  <a
                    class="btn btn-info btn-sm mx-2"
                    :href="form_fields.project_document"
                    target="_blank"
                  >
                    Click here
                  </a>
                </span>
                <div class="mt-1 mb-3">
                  <input
                    type="file"
                    class="form-control form-control-square mb-2"
                    name="project_document"
                    id="project_document"
                    @change="handleFileUpload($event, 'project_document')"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="project_agrement_file">Project Agrement File</label>
                <span v-if="form_fields.project_agrement_file">
                  <a
                    class="btn btn-info btn-sm mx-2"
                    :href="form_fields.project_agrement_file"
                    target="_blank"
                  >
                    Click here
                  </a>
                </span>
                <div class="mt-1 mb-3">
                  <input
                    type="file"
                    class="form-control form-control-square mb-2"
                    name="project_agrement_file"
                    id="project_agrement_file"
                    @change="handleFileUpload($event, 'project_agrement_file')"
                  />
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="">Project status</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.project_status"
                    class="form-control form-control-square mb-2"
                    name="project_status"
                    id="project_status"
                  >
                    <option value="">Select--- Project Status</option>
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="On Hold">On Hold</option>
                    <option value="Upcoming">Upcoming</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="">Delivery Status</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.delivery_status"
                    class="form-control form-control-square mb-2"
                    name="delivery_status"
                    id="delivery_status"
                  >
                    <option value="">Select--- Delivery Status</option>
                    <option value="Yet to meeting">Yet to meeting</option>
                    <option value="Testing Phase">Testing Phase</option>
                    <option value="Successfully Delivered">
                      Successfully Delivered
                    </option>
                    <option value="Delivery process not started">
                      Delivery process not started
                    </option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="client_rating">Client Rating (out of 10)</label>
                <div class="mt-1 mb-3">
                  <input
                    type="number"
                    class="form-control form-control-square mb-2"
                    id="client_rating"
                    name="client_rating"
                    v-model="form_fields.client_rating"
                    min="0"
                    max="10"
                    placeholder="Enter client rating out of 10"
                  />
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label for="">description</label>
                <div class="mt-1 mb-3">
                  <text-editor :name="'description'" />
                </div>
              </div>
            </div>
            <multiple-input-field :name="'project_document_files'" />
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
import UserDropDownEl from "../../../UserManagement/User/components/dropdown/DropDownEl.vue";
import ProjectGroupDropDownEl from "../../../ProjectManagement/ProjectGroup/components/dropdown/DropDownEl.vue";
import multipleInputField from "../components/meta_component/MultipleInputField.vue";

export default {
  components: {
    TextEditor,
    UserDropDownEl,
    ProjectGroupDropDownEl,
    multipleInputField,
  },
  data: () => ({
    setup,
    param_id: null,
    form_fields: {
      name: "",
      description: "",
      start_date: "",
      end_date: "",
      project_status: "",
      delivery_status: "",
      client_rating: "",
      project_link: "",
      project_progress: "",
      project_agrement_file: null,
      project_document: null,
    },
    userProjectGroup: [],
    userData: [],
  }),
  created: async function () {
    let id = (this.param_id = this.$route.params.id);

    if (id) {
      this.set_fields(id);
    }

    await this.get_project_group_data();
    await this.get_user_data();
  },
  methods: {
    ...mapActions(store, {
      create: "create",
      update: "update",
      details: "details",
      get_all: "get_all",
      set_only_latest_data: "set_only_latest_data",
    }),

    async get_project_group_data() {
      try {
        let res = await axios.get("/project-group");
        this.userProjectGroup = res.data.data; // ✅ Assign data properly
      } catch (error) {
        console.error("Error fetching project group", error);
      }
    },
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
        this.form_fields.name = this.item.name;
        this.form_fields.start_date = this.item.start_date;
        this.form_fields.end_date = this.item.end_date;
        this.form_fields.project_status = this.item.project_status;
        this.form_fields.delivery_status = this.item.delivery_status;
        this.form_fields.client_rating = this.item.client_rating;
        this.form_fields.project_link = this.item.project_link;
        this.form_fields.project_progress = this.item.project_progress;
        this.form_fields.project_agrement_file =
          this.item.project_agrement_file;
        this.form_fields.project_document = this.item.project_document;
        $("#description").summernote("code", this.item.description);
      }
    },
    handleFileUpload(event, field) {
      this.form_fields[field] = event.target.files[0];
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
  },
  computed: {
    ...mapState(store, {
      item: "item",
    }),
  },
};
</script>

<!-- 
 <multi-chip :name="`tags`" />
<blog-category-drop-down-el :name="'blog_categories'" :multiple="true" :value="[item.blog_categories]" />
<blog-writer-drop-down-el :name="'writer'" :multiple="false" :value="item.writer" />
<multiple-image-uploader :name="'images'" :accept="'image/*'" :images="item.images" />
<multiple-input-field :name="'contributors'" />
 -->
