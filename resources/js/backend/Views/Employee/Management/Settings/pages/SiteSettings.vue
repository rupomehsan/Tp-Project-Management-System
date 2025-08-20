<template></template>

<script>
import { auth_store } from "../../../../../GlobalStore/auth_store";
import { site_settings_store } from "../../../../../GlobalStore/site_settings_store";
import { mapState, mapActions } from "pinia";
export default {
  data: () => ({
    tab: "bassic_settings",
  }),

  methods: {
    ...mapActions(auth_store, {
      check_is_auth: "check_is_auth",
    }),
    ...mapActions(site_settings_store, {
      get_all_website_settings: "get_all_website_settings",
      get_setting_value: "get_setting_value",
    }),
    SiteSettingsHandler: async function () {
      let formData = new FormData(event.target);
      let response = await axios.post("website-settings/store", formData);
      if (response.data.status == "success") {
        window.s_alert(response.data.message);
        this.get_all_website_settings();
      }
    },
  },
  computed: {
    ...mapState(auth_store, {
      auth_info: "auth_info",
    }),
    ...mapState(site_settings_store, {
      website_settings_data: "website_settings_data",
    }),
  },
  watch: {
    website_settings_data: {
      handler: function () {
        // this.get_all_website_settings();
      },
    },
  },
};
</script>

<style></style>
