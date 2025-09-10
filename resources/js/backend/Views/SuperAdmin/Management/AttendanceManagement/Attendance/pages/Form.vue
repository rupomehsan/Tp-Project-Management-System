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
                <label for="">User Name</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.user_id"
                    class="form-control"
                    name="user_id"
                    id="user_id"
                  >
                    <option value="">Selet-- User Name</option>
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
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="date">Date</label>
                <div class="mt-1 mb-3">
                  <input
                    type="date"
                    class="form-control form-control-square mb-2"
                    name="date"
                    id="date"
                    v-model="form_fields.date"
                    :max="todayDate"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Attendance status</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.attendance_status"
                    class="form-control form-control-square mb-2"
                    name="attendance_status"
                    id="attendance_status"
                  >
                    <option value="">Select---- Status</option>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                    <option value="Remote">Remote</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="check_in">Check In</label>
                <div class="mt-1 mb-3">
                  <input
                    type="datetime-local"
                    class="form-control form-control-square mb-2"
                    name="check_in"
                    id="check_in"
                    v-model="form_fields.check_in"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="check_out">Check Out</label>
                <div class="mt-1 mb-3">
                  <input
                    type="datetime-local"
                    class="form-control form-control-square mb-2"
                    name="check_out"
                    id="check_out"
                    v-model="form_fields.check_out"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="notes">Notes</label>
                <text-editor
                  id="notes"
                  v-model="form_fields.notes"
                  name="notes"
                ></text-editor>
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
      user_id: "",
      date: new Date().toLocaleDateString("en-CA"), // Default to today's date
      check_in: "",
      check_out: "",
      attendance_status: "",
      notes: "", // New field for notes
    },
    userProject: [],
    userData: [],
  }),
  created: async function () {
    let id = (this.param_id = this.$route.params.id);
    let project_id = this.$route.query.project_id;
    if (project_id) {
      this.form_fields.project_id = project_id;
    }

    if (id) {
      this.set_fields(id);
    }

    await this.get_project_data();
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

    async get_project_data() {
      try {
        let res = await axios.get("/project");
        this.userProject = res.data.data; // ✅ Assign data properly
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
        this.form_fields.user_id = this.item.user_id;
        this.form_fields.date = this.item.date;
        this.form_fields.check_in = this.item.check_in;
        this.form_fields.check_out = this.item.check_out;
        this.form_fields.attendance_status = this.item.attendance_status;
        this.form_fields.notes = this.item.notes || ""; // Ensure notes is set
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
      var markupStr = $("#notes").summernote("code");
      var target = document.createElement("input");
      target.setAttribute("name", "notes");
      target.value = markupStr;
      document.getElementById("notes").appendChild(target);
    },
  },
  computed: {
    ...mapState(store, {
      item: "item",
    }),

    todayDate() {
      const now = new Date();
      // Get local date in YYYY-MM-DD format (timezone adjusted)
      return now.toLocaleDateString("en-CA"); // 'en-CA' gives YYYY-MM-DD format
    },

    todayStart() {
      const today = new Date();
      // Set to start of day in local timezone
      today.setHours(0, 0, 0, 0);
      // Convert to ISO format but keep local time
      const localISO = new Date(
        today.getTime() - today.getTimezoneOffset() * 60000
      ).toISOString();
      return localISO.slice(0, 16);
    },

    todayEnd() {
      const today = new Date();
      // Set to end of day in local timezone
      today.setHours(23, 59, 59, 999);
      // Convert to ISO format but keep local time
      const localISO = new Date(
        today.getTime() - today.getTimezoneOffset() * 60000
      ).toISOString();
      return localISO.slice(0, 16);
    },
  },
};
</script>
