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
                    <label for="project_id" class="form-label">Project Name</label>
                    <select name="project_id" v-model="form_fields.project_id" class="form-control" id="project_id">
                      <option v-for="user in all_users" :key="user.id" :value="user.id">
                        {{ user.name }}
                      </option>
                    </select>
                  </div>

                <div class="mb-3">
                  <label for="title" class="form-label">Title</label>
                  <input type="text" name="title" v-model="form_fields.title" class="form-control" id="title" >
                </div>
                <div class="mb-3">
                  <label for="description" class="form-label">Description</label>
                  <textarea name="description" id="description" v-model="form_fields.description" class="form-control" rows="7"></textarea>
                </div>

                <div class="mb-3">
                  <label for="system_loss" class="form-label">System Loss</label>
                   <select name="system_loss" v-model="form_fields.system_loss" class="form-control" id="system_loss">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="assigned_to" class="form-label">Assigned To</label>
                  <select name="assigned_to" v-model="form_fields.assigned_to" class="form-control" id="assigned_to">
                      <option v-for="user in all_users" :key="user.id" :value="user.id">
                        {{ user.name }}
                      </option>
                    </select>
                </div>

              </div>

              <div class="col-md-6 pull-right">

                <div class="mb-3">
                  <label for="start_date" class="form-label">Start Date</label>
                  <input type="date" name="start_date" v-model="form_fields.start_date" class="form-control" id="start_date" >
                </div>

                <div class="mb-3">
                  <label for="due_date" class="form-label">Due Date</label>
                  <input type="date" name="due_date" v-model="form_fields.due_date" class="form-control" id="due_date" >
                </div>

                <div class="mb-3">
                  <label for="due_date" class="form-label">Task Status</label>
                   <select name="task_status" v-model="form_fields.task_status" class="form-control" id="status">
                    <option>Select a Status</option>
                    <option>Pending</option>
                    <option>In Progress</option>
                    <option>Completed</option>
                  </select>
                </div>

              </div>
            </div>

          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-light btn-square px-5">
            <i class="icon-lock"></i>
            {{
              param_id
                ? `Update`
                : `Submit`
            }}
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
        project_id: '',
        title: '',
        description: '',
        system_loss: '',
        assigned_to: '',
        start_date: '',
        due_date: '',
        priority: '',
        task_status: '',
    }
    
  }),

  created: async function () {
    await this.get_all_user();
    
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
    //   console.log('nahid', this.param_id);
      
      await this.details(id);
      if (this.item) {
            this.form_fields.project_id = this.item.project_id
            this.form_fields.title = this.item.title
            this.form_fields.description = this.item.description
            this.form_fields.system_loss = this.item.system_loss
            this.form_fields.assigned_to = this.item.assigned_to
            this.form_fields.start_date = this.item.start_date
            this.form_fields.due_date = this.item.due_date
            this.form_fields.priority = this.item.priority
            this.form_fields.task_status = this.item.task_status

        }
    },

    submitHandler: async function ($event) {
      this.set_only_latest_data(true);
      if (this.param_id) {
        let response = await this.update($event);
        await this.get_all();
        if ([200, 201].includes(response.status)) {
          window.s_alert("Data successfully updated");
          this.$router.push({
            name: `All${this.setup.route_prefix}`,
          });
        }
      } else {
        let response = await this.create($event);
        await this.get_all();
        if ([200, 201].includes(response.status)) {
          window.s_alert("Data Successfully Created");
          this.$router.push({
            name: `All${this.setup.route_prefix}`,
          });
        }
      }
    },

   get_all_user: async function() {
    try {
      let response = await axios.get('users');
      this.all_users = response.data.data.data;
    } catch (error) {
      console.error('Failed to fetch users:', error);
      this.all_users = []; 
    }
  }


  },

  computed: {
    ...mapState(store, {
      item: "item",
    }),
  },
};
</script>
