<template>
  <div>
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5 class="text-capitalize">
            Project Tasks
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
            <div class="col-lg-8">
              <table class="table quick_modal_table table-bordered">
                <tbody>
                  <tr>
                    <th>Project Group Name</th>
                    <th class="text-center">:</th>
                    <th>{{ item.project_group_id?.name }}</th>
                  </tr>
                  <tr>
                    <th>Name</th>
                    <th class="text-center">:</th>
                    <th>{{ item.name }}</th>
                  </tr>
                </tbody>
              </table>
              <table class="table table-bordered mt-3">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Task Title</th>
                    <th>Status</th>
                    <th>End Date</th>
                    <th>Start Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-if="Array.isArray(item.tasks) && item.tasks.length"
                    v-for="(task, index) in item.tasks"
                    :key="index"
                  >
                    <td>{{ index + 1 }}</td>
                    <td>{{ task.title }}</td>
                    <td>{{ task.status }}</td>
                    <td>{{ task.end_date }}</td>
                    <td>{{ task.start_date }}</td>
                  </tr>
                  <tr v-else>
                    <td colspan="4" class="text-center">No tasks available.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <router-link
            class="btn btn-outline-warning btn-sm"
            :to="`/tasks/create?project_id=${item.id}`"

          >
            Create Task
          </router-link>

          <a
            href=""
            v-if="item.prev_slug"
            @click.prevent="get_data(item.prev_slug)"
            class="btn btn-secondary btn-sm ml-2"
          >
            <i class="fa fa-angle-left"></i>
            Previous {{ setup.route_prefix }} ({{ item.prev_count }})
          </a>

          <a
            href=""
            v-if="item.next_slug"
            @click.prevent="get_data(item.next_slug)"
            class="btn btn-secondary btn-sm ml-2"
          >
            Next {{ setup.route_prefix }} ({{ item.next_count }})
            <i class="fa fa-angle-right"></i>
          </a>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapState, mapWritableState } from "pinia";
import { store } from "../store";
import setup from "../setup";

export default {
  data: () => ({
    setup,
  }),
  created: async function () {
    let id = (this.param_id = this.$route.params.id);
    await this.get_data(id);
  },
  methods: {
    ...mapActions(store, {
      details: "details",
    }),
    get_data: async function (slug) {
      this.item = {};
      await this.details(slug);
    },
  },
  computed: {
    ...mapWritableState(store, {
      item: "item",
    }),
  },
};
</script>

<style>
tr th {
  text-align: left !important;
}
</style>
