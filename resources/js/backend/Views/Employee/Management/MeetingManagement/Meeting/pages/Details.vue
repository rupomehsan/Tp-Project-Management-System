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
                    <th>Meeting Group Title</th>
                    <th class="text-center">:</th>
                    <th>{{ item.meeting_group_id?.title }}</th>
                  </tr>
                  <tr>
                    <th>title</th>
                    <th class="text-center">:</th>
                    <th>{{ item.title }}</th>
                  </tr>
                  <tr>
                    <th>Date</th>
                    <th class="text-center">:</th>
                    <td>{{ formatDateTime(item.date) }}</td>
                  </tr>
                  <tr>
                    <th>Meeting Type</th>
                    <th class="text-center">:</th>
                    <td>{{ item.meeting_type }}</td>
                  </tr>
                  <tr>
                    <th>Meeting Link</th>
                    <th class="text-center">:</th>
                    <th>
                      <a
                        v-if="item.meeting_type !== 'Offline'"
                        class="btn btn-info"
                        target="_blank"
                        :href="item.meeting_link"
                      >
                        Click here
                      </a>
                      <button v-else class="btn btn-secondary" disabled>
                        Not Available (Offline)
                      </button>
                    </th>
                  </tr>
                  <tr>
                    <th>Description</th>
                    <th class="text-center">:</th>
                    <th>{{ item.description }}</th>
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
