<template>
  <div>
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5 class="text-capitalize">
            {{ setup.details_page_title }}
          </h5>
          <div>
            <router-link class="btn btn-outline-warning btn-sm" :to="{ name: `All${setup.route_prefix}` }">
              {{ setup.all_page_title }}
            </router-link>
          </div>
        </div>
        <div class="card-body card_body_fixed_height">
          <div class="row">
            <div class="col-lg-6">
              <table class="table quick_modal_table table-bordered">
                <tbody>
                  <tr>
                    <th>Project Group Name</th>
                    <th class="text-center">:</th>
                    <th>{{ item.project_group_id?.name }}</th>
                  </tr>
                  <tr>
                    <th>Project Name</th>
                    <th class="text-center">:</th>
                    <th>{{ item.name }}</th>
                  </tr>
                  <tr>
                    <th>Project Users</th>
                    <th class="text-center">:</th>
                    <th>
                      <span v-if="Array.isArray(item.project_users) && item.project_users.length">
                        <span v-for="(user, idx) in item.project_users" :key="user.id || idx">
                          {{ user.name || "N/A" }}<span v-if="idx < item.project_users.length - 1">, </span>
                        </span>
                      </span>
                      <span v-else>
                        {{ item.project_users?.name || "N/A" }}
                      </span>
                    </th>
                  </tr>

                  <tr>
                    <th>tart Date</th>
                    <th class="text-center">:</th>
                    <th>{{ item.start_date || "N/A" }}</th>
                  </tr>
                  <tr>
                    <th>End Date</th>
                    <th class="text-center">:</th>
                    <th>{{ item.end_date || "N/A" }}</th>
                  </tr>
                  <tr>
                    <th>Project Status</th>
                    <th class="text-center">:</th>
                    <th>{{ item.project_status || "N/A" }}</th>
                  </tr>
                  <tr>
                    <th>Delivery Status</th>
                    <th class="text-center">:</th>
                    <th>{{ item.delivery_status || "N/A" }}</th>
                  </tr>
                  <tr>
                    <th>Client Rating</th>
                    <th class="text-center">:</th>
                    <th>{{ item.client_rating || "N/A" }}</th>
                  </tr>
                  <tr>
                    <th>Project Link</th>
                    <th class="text-center">:</th>
                    <th>
                      <span v-if="item.project_link">
                        <a class="btn btn-info" target="_blank" :href="item.project_link">Click here</a>
                      </span>
                      <span v-else>N/A</span>
                    </th>
                  </tr>
                  <tr>
                    <th>Project progress</th>
                    <th class="text-center">:</th>
                    <th>{{ item.project_progress || "N/A" }}</th>
                  </tr>
                  <tr>
                    <th>Project agrement File</th>
                    <th class="text-center">:</th>
                    <th>
                      <span v-if="item.project_agrement_file">
                        <a class="btn btn-info btn-sm mx-2" :href="item.project_agrement_file" target="_blank"> Click here </a>
                      </span>
                      <span v-else>N/A</span>
                    </th>
                  </tr>
                  <tr>
                    <th>Project Document</th>
                    <th class="text-center">:</th>
                    <th>
                      <span v-if="item.project_document">
                        <a class="btn btn-info btn-sm mx-2" :href="item.project_document" target="_blank"> Click here </a>
                      </span>
                      <span v-else>N/A</span>
                    </th>
                  </tr>
                  <tr>
                    <th>Created At</th>
                    <th class="text-center">:</th>
                    <th>{{ formatDateTime(item.created_at) || "N/A" }}</th>
                  </tr>
                  <tr>
                    <th>Description</th>
                    <th class="text-center">:</th>
                    <th v-html="item.description || 'N/A'"></th>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-lg-6">
              <h5 class="text-center">Project Documents Files</h5>
              <hr />
              <table class="table quick_modal_table table-bordered">
                <tbody>
                  <tr v-for="(fileItem, index) in item.project_document_files" :key="index">
                    <th>{{ fileItem.name }}</th>
                    <th class="text-center">:</th>
                    <th>
                      <span v-if="/\.(jpe?g|png|gif|bmp|webp|svg)$/i.test(fileItem.file)">
                        <a data-lightbox="roadtrip" data-title="Image preview" :href="fileItem.file" target="_blank">
                          <img :src="fileItem.file" alt="file image" style="max-width: 100px; height: auto" />
                        </a>
                      </span>
                      <span v-else>
                        <a class="btn btn-info" target="_blank" :href="fileItem.file"> Click here </a>
                      </span>
                    </th>
                  </tr>
                </tbody>
              </table>
              <h5 class="text-center my-3">Project Documents Links</h5>
              <hr />
              <table class="table quick_modal_table table-bordered">
                <tbody>
                  <tr v-for="(fileItem, index) in item.project_document_links" :key="index">
                    <th>{{ fileItem.name }}</th>
                    <th class="text-center">:</th>
                    <th><a class="btn btn-info" target="_blank" :href="fileItem.link"> Click here </a></th>
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

          <a href="" v-if="item.prev_slug" @click.prevent="get_data(item.prev_slug)" class="btn btn-secondary btn-sm ml-2">
            <i class="fa fa-angle-left"></i>
            Previous {{ setup.route_prefix }} ({{ item.prev_count }})
          </a>

          <a href="" v-if="item.next_slug" @click.prevent="get_data(item.next_slug)" class="btn btn-secondary btn-sm ml-2">
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
    param_id: null,
  }),
  created: async function () {
    let id = (this.param_id = this.$route.params.id);
    await this.get_data(id);
  },
  watch: {
    "$route.params.id": {
      immediate: false,
      handler(newId, oldId) {
        if (newId && newId !== oldId) {
          this.param_id = newId;
          this.get_data(newId);
        }
      },
    },
  },
  methods: {
    ...mapActions(store, {
      details: "details",
    }),
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
