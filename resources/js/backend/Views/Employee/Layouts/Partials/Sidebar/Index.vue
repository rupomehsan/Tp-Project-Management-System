<template>
  <!--Start sidebar-wrapper-->
  <div id="sidebar-wrapper">
    <div class="brand-logo">
      <router-link :to="{ name: `adminDashboard` }" class="d-flex align-items-center">
        <img :src="`${get_setting_value('image') ?? 'avatar.png'} `" class="logo-icon" alt="logo icon" />
        <h5 class="logo-text">Employee Panel</h5>
      </router-link>
      <div class="close-btn">
        <i class="zmdi zmdi-close" @click="toggle_menu"></i>
      </div>
    </div>

    <div class="text-center mt-3">
      <img class="rounded-circle p-1" height="70" width="70" :src="`${auth_info.image ?? 'avatar.png'}`" alt="" />
      <p class="mt-2">Mr. {{ auth_info.name }}</p>
    </div>
    <hr />
    <ul class="metismenu" id="menu">
      <!-- <li class="menu-label">Management</li> -->
      <li>
        <router-link :to="{ name: `adminDashboard` }" class="border" href="javascript:void();">
          <div class="parent-icon">
            <i class="zmdi zmdi-view-dashboard"></i>
          </div>
          <div class="menu-title">Dashboard</div>
        </router-link>
      </li>

      <side-bar-single-menu :icon="`fa fa-plus`" :menu_title="`Attendance`" :route_name="`AllAttendance`" />
      <side-bar-single-menu :icon="`fa fa-plus`" :menu_title="`Projects`" :route_name="`AllProject`" />
      <side-bar-single-menu :icon="`fa fa-plus`" :menu_title="`Tasks`" :route_name="`AllTasks`" />
      <side-bar-single-menu :icon="`fa fa-plus`" :menu_title="`SystemLoss`" :route_name="`AllSystemLoss`" />

      <!-- Todo Management -->
      <side-bar-drop-down-menus
        :icon="`fa fa-plus`"
        :menu_title="`Todo Management`"
        :menus="[
          {
            route_name: `AllCategory`,
            title: `Category`,
            icon: `zmdi zmdi-dot-circle-alt`,
          },
          {
            route_name: `AllAddTodo`,
            title: `Add Todo`,
            icon: `zmdi zmdi-dot-circle-alt`,
          },
          {
            route_name: `AllCredential`,
            title: `Credential`,
            icon: `zmdi zmdi-dot-circle-alt`,
          },
        ]"
      />

      <!-- Meeting Management -->
      <side-bar-drop-down-menus
        :icon="`fa fa-plus`"
        :menu_title="`Meeting Management`"
        :menus="[
          {
            route_name: `AllMeeting`,
            title: `Meeting`,
            icon: `zmdi zmdi-dot-circle-alt`,
          },
          {
            route_name: `AllMeetingAgenda`,
            title: `Meeting Agenda`,
            icon: `zmdi zmdi-dot-circle-alt`,
          },
        ]"
      />

      <side-bar-single-menu :icon="`fa fa-plus`" :menu_title="`Notification`" :route_name="`AllNotification`" />
      <!-- <side-bar-single-menu :icon="`fa fa-plus`" :menu_title="`Message`" :route_name="`AllMessage`" /> -->
      <!-- Management end -->
    </ul>
  </div>
</template>

<script>
//auth_store
import { auth_store } from "../../../../../GlobalStore/auth_store";
import { site_settings_store } from "../../../../../GlobalStore/site_settings_store";
import { mapState, mapActions } from "pinia";
//components
import SideBarDropDownMenus from "./SideBarDropDownMenus.vue";
import SideBarSingleMenu from "./SideBarSingleMenu.vue";
export default {
  components: { SideBarDropDownMenus, SideBarSingleMenu },
  methods: {
    ...mapActions(site_settings_store, {
      get_setting_value: "get_setting_value",
    }),

    logout_submit: function () {
      let confirm = window.confirm("logout");
      if (confirm) {
        this.log_out();
      }
    },
    toggle_menu: function () {
      document.getElementById("wrapper").classList.toggle("toggled");
    },
  },
  computed: {
    ...mapState(auth_store, {
      auth_info: "auth_info",
    }),
  },
};
</script>

<style></style>
<!-- <side-bar-drop-down-menus :icon="`fa fa-plus`" :icon_image="`https://files.etek.com.bd/images/icon_sales.png`"
    :menu_title="`title Management`" :menus="[
                {
                    route_name: `AllUser`,
                    title: `title`,
                    icon: `zmdi zmdi-dot-circle-alt`,
                },
            ]" /> -->
