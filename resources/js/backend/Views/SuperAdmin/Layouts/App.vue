<template>
  <div id="wrapper" v-if="is_auth">
    <TopHeader
      :key="headerKey"
      :headerKey="headerKey"
      @update:headerKey="headerKey = $event"
    ></TopHeader>
    <div class="clearfix"></div>
    <div class="content-wrapper">
      <router-view></router-view>
    </div>
    <!--End content-wrapper-->

    <!--start color switcher-->
    <Footer></Footer>
    <!--end color switcher-->
  </div>
</template>

<script>
import TopHeader from "../Layouts/Partials/Header/Index.vue";
import Footer from "../Layouts/Partials/Footer/Index.vue";
//auth_store
import { auth_store } from "../../../GlobalStore/auth_store";
import { site_settings_store } from "../../../GlobalStore/site_settings_store";
import { mapActions, mapState } from "pinia";
import { redirectionUtils } from "../../../Utils/redirectionUtils";
export default {
  components: { TopHeader, Footer },
  data: () => ({
    rightToggle: false,
    headerKey: 0,
  }),
  created: async function () {
    await this.check_is_auth();
    await this.get_all_website_settings();

    this.handleUserRedirection();
  },
  methods: {
    ...mapActions(auth_store, {
      check_is_auth: "check_is_auth",
    }),
    ...mapActions(site_settings_store, {
      get_all_website_settings: "get_all_website_settings",
    }),

    handleUserRedirection() {
      redirectionUtils.handleUserRedirection(this, "super-admin");
    },

    redirectSuperAdmin(prevUrl, currentPath) {
      redirectionUtils.redirectSuperAdmin(this, prevUrl, currentPath);
    },

    redirectEmployee(prevUrl, currentPath) {
      redirectionUtils.redirectEmployee(this, prevUrl, currentPath);
    },

    extractRouteFromUrl(url) {
      return redirectionUtils.extractRouteFromUrl(url);
    },
    changeTheme(id) {
      const totalThemes = Array.from({ length: 15 }, (_, i) => i + 1);
      const newThemeNo = "bg-theme" + id;
      const body = document.getElementById("body");

      totalThemes.forEach((item) => {
        const currentThemeClass = "bg-theme" + item;
        if (body.classList.contains(currentThemeClass)) {
          body.classList.remove(currentThemeClass);
        }
      });

      body.classList.add(newThemeNo);
    },
  },

  computed: {
    ...mapState(auth_store, {
      auth_info: "auth_info",
      is_auth: "is_auth",
    }),
  },

  watch: {
    // Watch for authentication state changes
    is_auth(newVal, oldVal) {
      redirectionUtils.handleAuthStateChange(newVal, oldVal);
    },

    // Watch for role changes (in case of role updates)
    "auth_info.role_id"(newRoleId, oldRoleId) {
      redirectionUtils.handleRoleChange(newRoleId, oldRoleId, () => {
        this.handleUserRedirection();
      });
    },
  },
};
</script>

<style></style>
