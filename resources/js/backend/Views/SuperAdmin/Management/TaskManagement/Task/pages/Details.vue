<template>
  <div>
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5 class="text-capitalize">
            {{ setup.details_page_title }}
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
                    <th>Project Name</th>
                    <th class="text-center">:</th>
                    <th>{{ item?.project_id }}</th>
                  </tr>
                  <tr>
                    <th>Title</th>
                    <th class="text-center">:</th>
                    <th>{{ item?.title }}</th>
                  </tr>
                  <tr>
                    <th>Description</th>
                    <th class="text-center">:</th>
                    <td v-html="item && item.description ? item.description : 'N/A'"></td>
                  </tr>
                  <tr>
                    <th>System Loss</th>
                    <th class="text-center">:</th>
                    <td>
                      {{ item.system_loss  ?? "N/A" }}
                    </td>
                  </tr>
                  <tr>
                    <th>Assigned To</th>
                    <th class="text-center">:</th>
                    <th>{{ item?.user?.name ?? "N/A " }}</th>
                  </tr>
                  <tr>
                    <th>Start Date</th>
                    <th class="text-center">:</th>
                    <th>{{ item?.start_date }}</th>
                  </tr>
                  <tr>
                    <th>Due Date</th>
                    <th class="text-center">:</th>
                    <th>{{ item?.due_date }}</th>
                  </tr>
                  <tr>
                    <th>Priority</th>
                    <th class="text-center">:</th>
                    <th>{{ item?.priority }}</th>
                  </tr>
                  <tr>
                    <th>Task Status</th>
                    <th class="text-center">:</th>
                    <th>{{ item?.task_status }}</th>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <router-link
            class="btn btn-outline-warning btn-sm"
            :to="{
              name: `Edit${setup.route_prefix}`,
              params: { id: item.slug },
            }"
          >
            {{ setup.edit_page_title }}
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
