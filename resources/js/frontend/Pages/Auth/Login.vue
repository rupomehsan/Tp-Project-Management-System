<template>
  <Head>
    <title>Login</title>
  </Head>
  <Layout>
    <!-- Loading overlay for authentication check -->
    <div v-if="checkingAuth" class="auth-check-loading">
      <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Checking authentication...</p>
      </div>
    </div>

    <div v-else class="professional-login-container">
      <div class="login-card">
        <div class="login-header">
          <div class="brand-section">
            <div class="brand-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h2 class="brand-title">Project Management</h2>
            <p class="brand-subtitle">
              Welcome back! Please sign in to your account
            </p>
          </div>
        </div>

        <form @submit.prevent="LoginSubmitHandler" class="login-form">
          <div class="form-group">
            <label for="email" class="form-label">
              <i class="fas fa-envelope"></i>
              Email Address
            </label>
            <input
              id="email"
              class="form-control"
              type="email"
              placeholder="Enter your email"
              name="email"
              v-model="email"
              required
            />
          </div>

          <div class="form-group">
            <label for="password" class="form-label">
              <i class="fas fa-lock"></i>
              Password
            </label>
            <div class="password-input-wrapper">
              <input
                id="password"
                class="form-control"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter your password"
                name="password"
                v-model="password"
                required
              />
              <button
                type="button"
                class="password-toggle"
                @click="showPassword = !showPassword"
                :title="showPassword ? 'Hide password' : 'Show password'"
              >
                <i
                  :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
                ></i>
              </button>
            </div>
          </div>

          <div class="form-options">
            <div class="remember-me">
              <input
                type="checkbox"
                id="rememberMe"
                v-model="rememberMe"
                class="checkbox-input"
              />
              <label for="rememberMe" class="checkbox-label">
                Remember me
              </label>
            </div>
            <Link href="/forgot-password" class="forgot-password-link">
              Forgot password?
            </Link>
          </div>

          <button
            class="login-button"
            type="submit"
            :disabled="loading || !email || !password"
          >
            <span v-if="!loading" class="button-content">
              <i class="fas fa-sign-in-alt"></i>
              Sign In
            </span>
            <span v-if="loading" class="button-content loading">
              <div class="spinner"></div>
              Signing in...
            </span>
          </button>
        </form>

        <div class="login-footer">
          <p class="footer-text">
            Don't have an account?
            <a href="#" class="signup-link">Contact Administrator</a>
          </p>
        </div>
      </div>
    </div>
  </Layout>
</template>
<script>
import { mapActions, mapState } from "pinia";
import Layout from "./Layout/Layout.vue";
import { Link } from "@inertiajs/vue3";
//auth_store
import { auth_store } from "../../GlobalStore/auth_store";
export default {
  components: { Layout, Link },

  data() {
    return {
      loading: false,
      checkingAuth: true, // Add loading state for auth check
      showPassword: false,
      passwordError: false,
      email: "",
      password: "",
      rememberMe: false,
    };
  },

  created: async function () {
    const role = localStorage.getItem("admin_role");
    const prevUrl = window.sessionStorage.getItem("prevurl");

    console.log(role, prevUrl, "super-admin" + prevUrl);

    if (role) {
      // Convert role to number for comparison or compare as string
      const userRole = parseInt(role);

      if (userRole === 1 || role === "1") {
        if (prevUrl) {
          window.location.href = "/super-admin" + prevUrl;
        } else {
          window.location.href = "/super-admin#/dashboard";
        }
      } else if (userRole === 2 || role === "2") {
        if (prevUrl) {
          window.location.href = "/employee" + prevUrl;
        } else {
          window.location.href = "/employee#/dashboard";
        }
      }
    }

    // If no token or token is invalid, user stays on login page
    this.checkingAuth = false; // End loading state
  },

  mounted() {
    this.loadRememberedCredentials();
  },

  methods: {
    ...mapActions(auth_store, {
      check_is_auth: "check_is_auth",
    }),

    LoginSubmitHandler: async function () {
      try {
        this.loading = true;

        // Handle remember me functionality
        if (this.rememberMe) {
          this.saveCredentials();
        } else {
          this.clearSavedCredentials();
        }

        let formData = new FormData();
        formData.append("email", this.email);
        formData.append("password", this.password);
        formData.append("remember", this.rememberMe);

        let response = await axios.post("/login", formData);

        if (response.data?.status === "success") {
          let data = response.data?.data;

          if (data.access_token && data.user) {
            // Store authentication data
            localStorage.setItem("admin_token", data.access_token);
            localStorage.setItem("admin_role", data.user.role_id);

            // Clear any previous URL after successful login
            const prevUrl = window.sessionStorage.getItem("prevurl");
            window.sessionStorage.removeItem("prevurl");

            // Show success message
            window.s_alert("Login Successfully");

            // Redirect based on role
            let redirectUrl = "";
            if (data.user.role_id === 1) {
              redirectUrl =
                prevUrl && prevUrl.startsWith("/super-admin")
                  ? prevUrl
                  : "/super-admin#/dashboard";
            } else if (data.user.role_id === 2) {
              redirectUrl =
                prevUrl && prevUrl.startsWith("/employee")
                  ? prevUrl
                  : "/employee#/dashboard";
            }

            if (redirectUrl) {
              // Small delay to ensure token is saved before redirect
              setTimeout(() => {
                window.location.href = redirectUrl;
              }, 100);
            }
          } else {
            window.s_alert(
              "Login failed. Invalid response from server.",
              "error"
            );
          }
        } else {
          window.s_alert(
            response.data?.message || "Login failed. Please try again.",
            "error"
          );
        }
      } catch (error) {
        console.error("Login error", error);

        // Handle specific error responses
        if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          window.s_alert(error.response.data.message, "error");
        } else if (error.response && error.response.status === 401) {
          window.s_alert(
            "Invalid credentials. Please check your email and password.",
            "error"
          );
        } else {
          window.s_alert(
            "Login failed. Please check your connection and try again.",
            "error"
          );
        }
      } finally {
        this.loading = false;
      }
    },

    saveCredentials() {
      const credentials = {
        email: this.email,
        password: this.password,
        rememberMe: this.rememberMe,
      };
      localStorage.setItem(
        "rememberedCredentials",
        JSON.stringify(credentials)
      );
    },

    clearSavedCredentials() {
      localStorage.removeItem("rememberedCredentials");
    },

    clearAuthData() {
      localStorage.removeItem("admin_token");
      localStorage.removeItem("admin_role");
      window.sessionStorage.removeItem("prevurl");
    },

    loadRememberedCredentials() {
      const savedCredentials = localStorage.getItem("rememberedCredentials");
      if (savedCredentials) {
        try {
          const credentials = JSON.parse(savedCredentials);
          this.email = credentials.email || "";
          this.password = credentials.password || "";
          this.rememberMe = credentials.rememberMe || false;
        } catch (error) {
          console.error("Error loading remembered credentials:", error);
          this.clearSavedCredentials();
        }
      }
    },

    setPassword(email) {
      this.email = email;
    },
  },
  computed: {
    ...mapState(auth_store, {
      auth_info: "auth_info",
      is_auth: "is_auth",
    }),
  },
};
</script>

<style scoped>
.auth-check-loading {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.loading-spinner {
  text-align: center;
  color: #667eea;
}

.loading-spinner i {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.loading-spinner p {
  margin: 0;
  font-size: 1rem;
  font-weight: 500;
}
</style>
