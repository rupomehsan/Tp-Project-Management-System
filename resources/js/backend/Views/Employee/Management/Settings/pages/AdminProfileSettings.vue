<template>
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Profile Settings</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
        <div class="card profile-card-2">
          <div class="card-img-top bg-light" style="height: 120px"></div>
          <div class="card-body pt-5">
            <img
              :src="`${auth_info.image ?? 'avatar.png'}`"
              @error="$event.target.src = 'avatar.png'"
              alt="profile-image"
              class="profile my-2"
              style="width: 150px; height: 150px; object-fit: cover"
            />

            <h5 class="card-title text-capitalize">
              Name : {{ auth_info.name }}
            </h5>
            <p class="card-text">Eamil : {{ auth_info.email }}</p>
            <p class="card-text">Phone : {{ auth_info.phone_number }}</p>
            <p class="card-text">Address : {{ auth_info.address ?? "N/A" }}</p>
          </div>
          <div class="card-body border-light">
            <div class="media align-items-center">
              <div class="icon-block">
                <template
                  v-if="
                    auth_info.social_media && auth_info.social_media.length > 0
                  "
                >
                  <a
                    v-for="(social, index) in auth_info.social_media"
                    :key="index"
                    :href="social.link"
                    target="_blank"
                    :title="social.name"
                    class="social-link"
                  >
                    <i :class="getSocialMediaIcon(social.name)"></i>
                  </a>
                </template>
                <template v-else>
                  <span class="text-muted"
                    >No social media links available</span
                  >
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
              <li class="nav-item" @click="tab = 'edit'">
                <a
                  :class="tab == 'edit' ? ' active' : ''"
                  href="javascript:void();"
                  data-target="#edit"
                  data-toggle="pill"
                  class="nav-link"
                  ><i class="icon-note"></i>
                  <span class="hidden-xs">Edit</span></a
                >
              </li>
              <li class="nav-item" @click="tab = 'change_password'">
                <a
                  :class="tab == 'change_password' ? 'active' : ''"
                  href="javascript:void();"
                  data-target="#profile"
                  data-toggle="pill"
                  class="nav-link"
                  ><i class="icon-user"></i>
                  <span class="hidden-xs">Change password</span></a
                >
              </li>
            </ul>
            <div class="tab-content p-3">
              <div
                v-if="tab == 'edit'"
                :class="tab == 'edit' ? ' active' : ''"
                class="tab-pane active"
                id="profile"
              >
                <form
                  @submit.prevent="UpdateProfileHandler"
                  enctype="multipart/form-data"
                >
                  <div class="form-group row">
                    <label class="col-lg-3 col-form-label form-control-label"
                      >Name</label
                    >
                    <div class="col-lg-9">
                      <input
                        v-model="auth_info.name"
                        name="name"
                        class="form-control"
                        type="text"
                        value=""
                      />
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-lg-3 col-form-label form-control-label"
                      >Email</label
                    >
                    <div class="col-lg-9">
                      <input
                        v-model="auth_info.email"
                        name="email"
                        class="form-control"
                        type="email"
                        value=""
                      />
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-lg-3 col-form-label form-control-label"
                      >Phone</label
                    >
                    <div class="col-lg-9">
                      <input
                        v-model="auth_info.phone_number"
                        name="phone_number"
                        class="form-control"
                        type="number"
                        value=""
                      />
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-lg-3 col-form-label form-control-label"
                      >Address</label
                    >
                    <div class="col-lg-9">
                      <input
                        v-model="auth_info.address"
                        name="address"
                        class="form-control"
                        type="text"
                        value=""
                        placeholder="Street"
                      />
                    </div>
                  </div>

                  <div class="form-group row align-items-center">
                    <label class="col-lg-3 col-form-label form-control-label"
                      >Change image</label
                    >
                    <div class="col-lg-9">
                      <img
                        v-if="auth_info.image"
                        class="mt-2"
                        :src="auth_info.image"
                        height="100"
                        width="100"
                        alt=""
                      />
                      <input class="form-control" name="image" type="file" />
                    </div>
                  </div>

                  <multiple-input-field
                    :name="'social_media'"
                    :value="auth_info.social_media"
                  />
                  <div class="form-group row">
                    <label
                      class="col-lg-3 col-form-label form-control-label"
                    ></label>
                    <div class="col-lg-9">
                      <input
                        type="submit"
                        class="btn btn-primary"
                        value="Save Changes"
                      />
                    </div>
                  </div>
                </form>
              </div>
              <div
                v-if="tab == 'change_password'"
                :class="tab == 'change_password' ? ' active' : ''"
                class="tab-pane"
                id="change_password"
              >
                <form @submit.prevent="ChangePasswordHandler">
                  <div class="form-group row">
                    <label class="col-lg-3 col-form-label form-control-label"
                      >Current password</label
                    >
                    <div class="col-lg-9">
                      <input
                        class="form-control"
                        name="current_password"
                        type="password"
                        value=""
                      />
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-lg-3 col-form-label form-control-label"
                      >New password</label
                    >
                    <div class="col-lg-9">
                      <input
                        class="form-control"
                        name="new_password"
                        type="password"
                        value=""
                      />
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-lg-3 col-form-label form-control-label"
                      >Confirm New password</label
                    >
                    <div class="col-lg-9">
                      <input
                        class="form-control"
                        name="confirm_new_password"
                        type="password"
                        value=""
                      />
                    </div>
                  </div>
                  <div class="form-group row">
                    <label
                      class="col-lg-3 col-form-label form-control-label"
                    ></label>
                    <div class="col-lg-9">
                      <input
                        type="submit"
                        class="btn btn-primary"
                        value="Save Changes"
                      />
                    </div>
                  </div>
                </form>
              </div>
              <div
                v-if="tab == 'message'"
                :class="tab == 'message' ? ' active' : ''"
                class="tab-pane"
                id="message"
              >
                <table class="table table-striped table-active table-bordered">
                  <tr v-for="i in 5" :key="i">
                    <td>1</td>
                    <td>1</td>
                    <td>1</td>
                    <td>1</td>
                    <td>1</td>
                    <td>1</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End container-fluid-->
</template>

<script>
import { auth_store } from "../../../../../GlobalStore/auth_store";
import { settings_store } from "../store/store";
import { mapState, mapActions } from "pinia";
import MultipleInputField from "../components/meta_component/MultipleInputField.vue";
export default {
  components: { MultipleInputField },
  data: () => ({
    tab: "edit",
  }),
  methods: {
    ...mapActions(auth_store, {
      check_is_auth: "check_is_auth",
    }),
    getSocialMediaIcon(socialMediaName) {
      const iconMap = {
        Facebook: "fa fa-facebook bg-facebook text-white",
        Instagram: "fa fa-instagram bg-instagram text-white",
        Twitter: "fa fa-twitter bg-twitter text-white",
        LinkedIn: "fa fa-linkedin bg-linkedin text-white",
        YouTube: "fa fa-youtube bg-youtube text-white",
        TikTok: "fa fa-tiktok bg-tiktok text-white",
        Snapchat: "fa fa-snapchat bg-snapchat text-white",
        Pinterest: "fa fa-pinterest bg-pinterest text-white",
        WhatsApp: "fa fa-whatsapp bg-whatsapp text-white",
        Telegram: "fa fa-telegram bg-telegram text-white",
        Discord: "fa fa-discord bg-discord text-white",
        Reddit: "fa fa-reddit bg-reddit text-white",
        Tumblr: "fa fa-tumblr bg-tumblr text-white",
        Twitch: "fa fa-twitch bg-twitch text-white",
        GitHub: "fa fa-github bg-github text-white",
        Behance: "fa fa-behance bg-behance text-white",
        Dribbble: "fa fa-dribbble bg-dribbble text-white",
        Skype: "fa fa-skype bg-skype text-white",
        Viber: "fa fa-viber bg-viber text-white",
        WeChat: "fa fa-wechat bg-wechat text-white",
        Other: "fa fa-link bg-secondary text-white",
      };

      return iconMap[socialMediaName] || "fa fa-link bg-secondary text-white";
    },
    UpdateProfileHandler: async function () {
      let formData = new FormData(event.target);
      let response = await axios.post("user-profile-update", formData);
      if (response.data.status == "success") {
        window.s_alert(response.data.message);
        this.check_is_auth();
      }
    },
    ChangePasswordHandler: async function () {
      let formData = new FormData(event.target);
      let response = await axios.post("user-change-password", formData);
      if (response.data.status == "success") {
        window.s_alert(response.data.message);
        this.check_is_auth();
      }
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
