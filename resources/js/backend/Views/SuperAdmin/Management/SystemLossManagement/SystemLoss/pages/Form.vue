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
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Project Id</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.project_id"
                    class="form-control"
                    name="project_id"
                    id="project_id"
                  >
                    <option value="">Selet-- Project</option>
                    <option
                      v-for="item in projectData?.data"
                      :key="item.id"
                      :value="item.id"
                    >
                      {{ item.name }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="">Loss Category Id</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.loss_category_id"
                    class="form-control"
                    name="loss_category_id"
                    id="loss_category_id"
                  >
                    <option value="">Selet-- Loss Category</option>
                    <option
                      v-for="item in lossCategoryData?.data"
                      :key="item.id"
                      :value="item.id"
                    >
                      {{ item.title }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="loss_date">Loss Date</label>
                <div class="mt-1 mb-3">
                  <input
                    type="date"
                    class="form-control form-control-square mb-2"
                    name="loss_date"
                    id="loss_date"
                    v-model="form_fields.loss_date"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="start_time">Start Time</label>
                <div class="mt-1 mb-3">
                  <input
                  type="time"
                  class="form-control form-control-square mb-2"
                  name="start_time"
                  id="start_time"
                  v-model="form_fields.start_time"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="end_time">End Time</label>
                <div class="mt-1 mb-3">
                  <input
                  type="time"
                  class="form-control form-control-square mb-2"
                  name="end_time"
                  id="end_time"
                  v-model="form_fields.end_time"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label for="">Loss Type</label>
                <div class="mt-1 mb-3">
                  <select
                  v-model="form_fields.loss_type"
                  class="form-control form-control-square mb-2"
                  name="loss_type"
                  id="loss_type"
                  >
                  <option value="">Select-- Meeting Type</option>
                  <option value="planned">planned</option>
                  <option value="unplanned">unplanned</option>
                  </select>
                </div>
                </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="">remarks</label>
                <div class="mt-1 mb-3">
                  <text-editor :name="'remarks'" />
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
export default {
  components: { TextEditor },
  data: () => ({
    setup,
    param_id: null,
    form_fields: {
      project_id: "",
      loss_category_id: "",
      loss_date: "",
      start_time: "",
      end_time: "",
      loss_type: "",
      remarks: "",
    },
    projectData: [],
    lossCategoryData: [],
  }),
  created: async function () {
    let id = (this.param_id = this.$route.params.id);
    let project_id = this.$route.query.project_id;
    if (project_id) {
      this.form_fields.project_id = project_id;
    }

    let loss_category_id = this.$route.query.loss_category_id;
    if (loss_category_id) {
      this.form_fields.loss_category_id = loss_category_id;
    }

    if (id) {
      this.set_fields(id);
    }

    await this.get_project_data();
    await this.get_loss_category_data();
  },
  methods: {
    ...mapActions(store, {
      create: "create",
      update: "update",
      details: "details",
      get_all: "get_all",
      set_only_latest_data: "set_only_latest_data",
    }),

    async get_project_data() {
      try {
        let res = await axios.get("/project");
        this.projectData = res.data.data; // ✅ Assign data properly
      } catch (error) {
        console.error("Error fetching project", error);
      }
    },

    async get_loss_category_data() {
      try {
        let res = await axios.get("/system-loss-category");
        this.lossCategoryData = res.data.data; // ✅ Assign data properly
      } catch (error) {
        console.error("Error fetching loss category group", error);
      }
    },

    set_fields: async function (id) {
      this.param_id = id;
      await this.details(id);
      if (this.item) {
        this.form_fields.project_id = this.item.project_id?.id;
        this.form_fields.loss_category_id = this.item.loss_category_id?.id;
        this.form_fields.loss_date = this.item.loss_date;
        this.form_fields.start_time = this.item.start_time;
        this.form_fields.loss_type = this.item.loss_type;
        this.form_fields.end_time = this.item.end_time;
        $("#remarks").summernote("code", this.item.remarks);
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
            name: `All${this.setup.route_prefix}`,
          });
        }
      } else {
        this.setSummerEditor();
        let response = await this.create($event);
        // await this.get_all();
        if ([200, 201].includes(response.status)) {
          window.s_alert("Data Successfully Created");
          this.$router.push({
            name: `All${this.setup.route_prefix}`,
          });
        }
      }
    },
    setSummerEditor() {
      var markupStr = $("#remarks").summernote("code");
      var target = document.createElement("input");
      target.setAttribute("name", "remarks");
      target.value = markupStr;
      document.getElementById("remarks").appendChild(target);
    },
  },
  computed: {
    ...mapState(store, {
      item: "item",
    }),
  },
};
</script>
