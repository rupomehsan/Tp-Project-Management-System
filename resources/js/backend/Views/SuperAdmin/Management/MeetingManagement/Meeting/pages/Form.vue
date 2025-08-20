<template>
  <div>
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5 class="text-capitalize">
            {{ param_id ? `${setup.edit_page_title}` : `${setup.create_page_title}` }}
          </h5>
          <div>
            <router-link class="btn btn-outline-warning btn-sm" :to="{ name: `All${setup.route_prefix}` }">
              {{ setup.all_page_title }}
            </router-link>
          </div>
        </div>
        <div class="card-body card_body_fixed_height">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Meeting Group</label>
                <div class="mt-1 mb-3">
                  <select v-model="form_fields.group_id" class="form-control" name="group_id" id="group_id">
                    <option value="">Select-- Meeting Group</option>
                    <option v-for="item in meetigGroupData?.data" :key="item.id" :value="item.id">
                      {{ item.title }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Title</label>
                <div class="mt-1 mb-3">
                  <input class="form-control form-control-square mb-2" type="text" name="title" id="title" v-model="form_fields.title" />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="date">Date</label>
                <div class="mt-1 mb-3">
                  <input type="datetime-local" class="form-control form-control-square mb-2" name="date" id="date" v-model="form_fields.date" />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Meeting Type</label>
                <div class="mt-1 mb-3">
                  <select v-model="form_fields.meeting_type" class="form-control form-control-square mb-2" name="meeting_type" id="meeting_type">
                    <option value="">Select-- Meeting Type</option>
                    <option value="Offline">Offline</option>
                    <option value="Online">Online</option>
                    <option value="Hybrid">Hybrid</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6" v-show="form_fields.meeting_type == 'Online'">
              <div class="form-group">
                <label for="">meeting_link</label>
                <div class="mt-1 mb-3">
                  <input
                    class="form-control form-control-square mb-2"
                    type="url"
                    name="meeting_link"
                    id="meeting_link"
                    v-model="form_fields.meeting_link"
                    placeholder="Enter meeting link"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="">description</label>
                <div class="mt-1 mb-3">
                  <text-editor :name="'description'" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-light btn-square px-5"><i class="icon-lock"></i> Submit</button>
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
      group_id: "",
      title: "",
      date: "",
      meeting_type: "",
      meeting_link: "",
      description: "",
    },
    meetigGroupData: [],
  }),
  created: async function () {
    let id = (this.param_id = this.$route.params.id);
    let group_id = this.$route.query.group_id;
    if (group_id) {
      this.form_fields.group_id = group_id;
    }

    if (id) {
      this.set_fields(id);
    }

    await this.get_project_data();
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
        let res = await axios.get("/meeting-group");
        this.meetigGroupData = res.data.data; // ✅ Assign data properly
      } catch (error) {
        console.error("Error fetching meeting group", error);
      }
    },

    set_fields: async function (id) {
      this.param_id = id;
      await this.details(id);
      if (this.item) {
        this.form_fields.group_id = this.item.meeting_group_id?.id;
        this.form_fields.title = this.item.title;
        this.form_fields.date = this.item.date;
        this.form_fields.meeting_type = this.item.meeting_type;
        this.form_fields.meeting_link = this.item.meeting_link;
        $("#description").summernote("code", this.item.description);
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
      var markupStr = $("#description").summernote("code");
      var target = document.createElement("input");
      target.setAttribute("name", "description");
      target.value = markupStr;
      document.getElementById("description").appendChild(target);
    },
  },
  computed: {
    ...mapState(store, {
      item: "item",
    }),
  },
};
</script>
