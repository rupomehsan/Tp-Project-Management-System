/**
 * Utility functions for handling user authentication and redirection
 */

export const redirectionUtils = {
  /**
   * Extract route from stored URL (e.g., "/super-admin#/users" -> "/users")
   * @param {string} url - The URL to extract route from
   * @returns {string} - The extracted route
   */
  extractRouteFromUrl(url) {
    if (url && url.includes("#")) {
      return url.split("#")[1] || "/dashboard";
    }
    return "/dashboard";
  },

  /**
   * Navigate to a route using Vue Router with error handling
   * @param {Object} router - Vue Router instance
   * @param {string} targetRoute - Route to navigate to
   * @param {string} fallbackRoute - Fallback route if navigation fails
   */
  navigateWithFallback(router, targetRoute, fallbackRoute = "/dashboard") {
    router.push(targetRoute).catch((err) => {
      console.error("Router navigation error:", err);
      router.push(fallbackRoute);
    });
  },

  /**
   * Handle redirection based on user role and authentication status
   * @param {Object} context - Vue component context
   * @param {string} userType - 'super-admin' or 'employee'
   */
  handleUserRedirection(context, userType = "super-admin") {
    const { is_auth, auth_info, $router, $route } = context;

    if (!is_auth) {
      window.location.href = "/login";
      return;
    }

    const prevUrl = window.sessionStorage.getItem("prevurl");
    const currentPath = $route.path;

    try {
      if (auth_info?.role_id === 1) {
        // Super Admin
        if (userType === "employee") {
          // Super Admin trying to access employee area
          window.location.href = "/super-admin#/dashboard";
          return;
        }
        this.redirectSuperAdmin(context, prevUrl, currentPath);
      } else if (auth_info?.role_id === 2) {
        // Employee
        if (userType === "super-admin") {
          // Employee trying to access super-admin area
          window.location.href = "/employee#/dashboard";
          return;
        }
        this.redirectEmployee(context, prevUrl, currentPath);
      } else {
        // Unknown role
        window.location.href = "/login";
      }
    } catch (error) {
      console.error("Redirection error:", error);
      window.location.href = "/login";
    }
  },

  /**
   * Handle Super Admin redirection
   * @param {Object} context - Vue component context
   * @param {string} prevUrl - Previous URL from session storage
   * @param {string} currentPath - Current route path
   */
  redirectSuperAdmin(context, prevUrl, currentPath) {
    const { $router } = context;
    const targetRoute = prevUrl
      ? this.extractRouteFromUrl(prevUrl)
      : "/dashboard";

    if (currentPath !== targetRoute) {
      this.navigateWithFallback($router, targetRoute);
    }
  },

  /**
   * Handle Employee redirection
   * @param {Object} context - Vue component context
   * @param {string} prevUrl - Previous URL from session storage
   * @param {string} currentPath - Current route path
   */
  redirectEmployee(context, prevUrl, currentPath) {
    const { $router } = context;
    const targetRoute = prevUrl
      ? this.extractRouteFromUrl(prevUrl)
      : "/dashboard";

    if (currentPath !== targetRoute) {
      this.navigateWithFallback($router, targetRoute);
    }
  },

  /**
   * Handle authentication state changes
   * @param {boolean} newVal - New authentication state
   * @param {boolean} oldVal - Old authentication state
   */
  handleAuthStateChange(newVal, oldVal) {
    if (oldVal === true && newVal === false) {
      // User logged out, redirect to login
      window.location.href = "/login";
    }
  },

  /**
   * Handle role changes
   * @param {number} newRoleId - New role ID
   * @param {number} oldRoleId - Old role ID
   * @param {Function} redirectionHandler - Function to handle redirection
   */
  handleRoleChange(newRoleId, oldRoleId, redirectionHandler) {
    if (newRoleId !== oldRoleId && newRoleId) {
      redirectionHandler();
    }
  },
};

export default redirectionUtils;
