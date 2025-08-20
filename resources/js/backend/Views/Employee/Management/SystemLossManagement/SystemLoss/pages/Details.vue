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
                    <th>Project ID</th>
                    <th class="text-center">:</th>
                    <td>{{ item.project_id?.name }}</td>
                    </tr>
                    <tr>
                    <th>Loss Category ID</th>
                    <th class="text-center">:</th>
                    <td>{{ item.loss_category_id?.title }}</td>
                    </tr>
                    <tr>
                    <th>Loss Date</th>
                    <th class="text-center">:</th>
                    <td>{{ item.loss_date }}</td>
                    </tr>
                    <tr>
                    <th>Loss Total Time</th>
                    <th class="text-center">:</th>
                    <td>
                       {{ calculateDuration(item.start_time, item.end_time) }}
                    </td>
                    </tr>
                    <tr>
                    <th>Start Time</th>
                    <th class="text-center">:</th>
                    <td>{{ item.start_time }}</td>
                    </tr>
                    <tr>
                    <th>End Time</th>
                    <th class="text-center">:</th>
                    <td>{{ item.end_time }}</td>
                    </tr>
                    <tr>
                    <th>Loss Type</th>
                    <th class="text-center">:</th>
                    <td>{{ item.loss_type }}</td>
                    </tr>
                    <tr>
                    <th>Remarks</th>
                    <th class="text-center">:</th>
                    <td>{{ item.remarks }}</td>
                    </tr>
                    <tr>
                      <th>Created At</th>
                      <th class="text-center">:</th>
                      <td>{{ formatDateTime(item.created_at) }}</td>
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
    calculateDuration(start_time, end_time) {
    if (!start_time || !end_time) return '';

    const start = new Date(`1970-01-01T${start_time}`);
    const end = new Date(`1970-01-01T${end_time}`);
    let diff = (end - start) / 1000; // seconds

    if (diff < 0) diff += 24 * 60 * 60; // handle overnight

    const hours = Math.floor(diff / 3600);
    const minutes = Math.floor((diff % 3600) / 60);

    return `${hours}h ${minutes}m`;
  },
    formatDateTime(dateTime) {
      if (!dateTime) return "";
      const options = {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
      };
      return new Date(dateTime).toLocaleString("en-US", options);
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
