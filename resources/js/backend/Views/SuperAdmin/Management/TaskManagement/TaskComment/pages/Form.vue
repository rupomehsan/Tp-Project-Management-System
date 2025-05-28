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
              v-if="item.slug"
              class="btn btn-outline-info mr-2 btn-sm"
              :to="{
                name: `Details${setup.route_prefix}`,
                params: { id: item.slug },
              }"
            >
              {{ setup.details_page_title }}
            </router-link>
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
            <div class="col-md-12">
              <div class="col-md-6 pull-left">
                <div class="mb-3">
                  <label for="task_id" class="form-label">Task Name</label>
                  <select name="task_id" v-model="form_fields.task_id" class="form-control" id="task_id">
                    <option>Select Task Name</option>
                    <option
                      v-for="task in all_task"
                      :key="task.id"
                      :value="task.id"
                    >
                      {{ task?.title ?? "N/A" }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="col-md-6 pull-right">
                <div class="mb-3">
                  <label for="user_id" class="form-label">User Name</label>
                  <select name="user_id" v-model="form_fields.user_id" class="form-control" id="user_id">
                    <option>Select User Name</option>
                    <option
                      v-for="user in all_user"
                      :key="user.id"
                      :value="user.id"
                    >
                      {{ user?.name ?? "N/A" }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="mb-3">
                <label for="comment" class="form-label">Comment</label>
                <text-editor :name="'comment'" />
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-light btn-square px-5">
            <i class="icon-lock"></i>
            Submit
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
import form_fields from "../setup/form_fields";
import textEditor from "../../../../../../GlobalComponents/FormComponents/TextEditor.vue";
import axios from "axios";

export default {
  components: { textEditor },
  data: () => ({
    setup,
    form_fields,
    param_id: null,
    from_fields: {
      task_id: null,
      user_id: null,
    },
    all_task: [],
    all_user: [],
  }),
  created: async function () {
    this.get_all_task();
    this.get_all_user();

    let id = (this.param_id = this.$route.params.id);
    if (id) {
      this.set_fields(id);
    }
  },
  methods: {
    ...mapActions(store, {
      create: "create",
      update: "update",
      details: "details",
      get_all: "get_all",
      set_only_latest_data: "set_only_latest_data",
    }),

    set_fields: async function (id) {
      this.param_id = id;
      await this.details(id);
      if (this.item) {
        this.form_fields.task_id = this.item.task_id;
        this.form_fields.user_id = this.item.user_id;
        $("#comment").summernote("code", this.item.comment);
      }
    },

    submitHandler: async function ($event) {
      this.set_only_latest_data(true);
      if (this.param_id) {
        this.setSummerEditor();
        let response = await this.update($event);
        if ([200, 201].includes(response.status)) {
          window.s_alert("Data successfully updated");
          this.$router.push({
            name: `All${this.setup.route_prefix}`,
          });
        }
      } else {
        this.setSummerEditor();
        let response = await this.create($event);
        if ([200, 201].includes(response.status)) {
          window.s_alert("Data Successfully Created");
          this.$router.push({
            name: `All${this.setup.route_prefix}`,
          });
        }
      }
    },

    setSummerEditor() {
      var markupStr = $("#comment").summernote("code");
      var target = document.createElement("input");
      target.setAttribute("name", "comment");
      target.value = markupStr;
      document.getElementById("comment").appendChild(target);
    },

    get_all_task: async function () {
      try {
        let response = await axios.get("tasks");
        this.all_task = response.data.data.data;
      } catch (error) {
        console.error("Failed to fetch tasks:", error);
        this.all_task = [];
      }
    },
    get_all_user: async function () {
      try {
        let response = await axios.get("users");
        this.all_user = response.data.data.data;
      } catch (error) {
        console.error("Failed to fetch users:", error);
        this.all_user = [];
      }
    },
  },

  computed: {
    ...mapState(store, {
      item: "item",
    }),
  },
};
</script>
