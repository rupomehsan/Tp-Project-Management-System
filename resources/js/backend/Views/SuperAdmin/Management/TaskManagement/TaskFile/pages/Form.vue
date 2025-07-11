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
                  <select
                    name="task_id"
                    v-model="form_fields.task_id"
                    class="form-control"
                    id="task_id"
                  >
                    <option value="">Select Task Name</option>
                    <option
                      v-for="task in all_task"
                      :key="task.id"
                      :value="task.id"
                    >
                      {{ task.title }}
                    </option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="file_name" class="form-label">File Name</label>
                  <input
                    type="text"
                    v-model="form_fields.file_name"
                    name="file_name"
                    class="form-control"
                    id="file_name"
                  />
                </div>
              </div>

              <div class="col-md-6 pull-right">
                <div class="mb-3">
                  <label for="file_path" class="form-label">File Path</label>
                  <input
                    type="text"
                    v-model="form_fields.file_path"
                    name="file_path"
                    class="form-control"
                    id="file_path"
                  />
                </div>

                <div class="mb-3">
                  <label for="uploaded_at" class="form-label">Uploaded</label>
                  <input
                    type="date"
                    v-model="form_fields.uploaded_at"
                    name="uploaded_at"
                    class="form-control"
                    id="uploaded_at"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-light btn-square px-5">
            <i class="icon-lock"></i>
            {{ param_id ? `Update` : `Submit` }}
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
import axios from "axios";

export default {
  data: () => ({
    setup,
    form_fields,
    param_id: null,
    form_fields: {
      task_id: null,
      file_name: "",
      file_path: "",
      uploaded_at: "",
    },
    all_task: [],
  }),

  created: async function () {
    await this.get_all_task();

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
        this.form_fields.file_name = this.item.file_name;
        this.form_fields.file_path = this.item.file_path;
        this.form_fields.uploaded_at = this.item.uploaded_at
          ? new Date(this.item.uploaded_at).toISOString().split('T')[0]
          : "";
      }
    },

    submitHandler: async function ($event) {
      this.set_only_latest_data(true);
      if (this.param_id) {
        let response = await this.update($event);

        if ([200, 201].includes(response.status)) {
          window.s_alert("Data successfully updated");
          this.$router.push({
            name: `All${this.setup.route_prefix}`,
          });
        }
      } else {
        let response = await this.create($event);

        if ([200, 201].includes(response.status)) {
          window.s_alert("Data Successfully Created");
          this.$router.push({
            name: `All${this.setup.route_prefix}`,
          });
        }
      }
    },

    
    get_all_task: async function () {
      try {
        let response = await axios.get("tasks");
        this.all_task = response.data.data.data;
      } catch (error) {
        console.error("Failed to fetch users:", error);
        this.all_task = [];
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
