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
                    <th>Project Name</th>
                    <th class="text-center">:</th>
                    <th>{{ item.project_id?.name }}</th>
                  </tr>
                  <tr>
                    <th>Task Group Name</th>
                    <th class="text-center">:</th>
                    <th>{{ item.task_group?.name }}</th>
                  </tr>
                  <tr>
                    <th>title</th>
                    <th class="text-center">:</th>
                    <th>{{ item.title }}</th>
                  </tr>

                  <tr>
                    <th>Assigned To</th>
                    <th class="text-center">:</th>
                    <td>{{ item.user?.name || "Not Assigned" }}</td>
                  </tr>
                  <tr>
                    <th>Start Date</th>
                    <th class="text-center">:</th>
                    <td>{{ formatDateTime(item.start_date) }}</td>
                  </tr>
                  <tr>
                    <th>End Date</th>
                    <th class="text-center">:</th>
                    <td>{{ formatDateTime(item.end_date) }}</td>
                  </tr>
                  <tr>
                    <th>Actual Time</th>
                    <th class="text-center">:</th>
                    <td class="text-capitalize">
                      {{ FindActualTime(item.start_date, item.end_date) }}
                    </td>
                  </tr>

                  <tr>
                    <th>Task Status</th>
                    <th class="text-center">:</th>
                    <td>{{ item.task_status }}</td>
                  </tr>
                  <tr>
                    <th>Task Rating</th>
                    <th class="text-center">:</th>
                    <td>{{ item.rating }}</td>
                  </tr>
                  <tr>
                    <th>Priority</th>
                    <th class="text-center">:</th>
                    <td class="text-capitalize">{{ item.priority }}</td>
                  </tr>
                  <tr>
                    <th>Created at</th>
                    <th class="text-center">:</th>
                    <td>{{ formatDateTime(item.created_at) }}</td>
                  </tr>
                  <tr>
                    <th>Description</th>
                    <th class="text-center">:</th>
                    <th v-html="item.description"></th>
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
    // Calculate actual working time between start and end dates (9 AM to 7 PM, Monday to Friday)
    FindActualTime(start_date, end_date) {
      if (!start_date || !end_date) return "N/A";
      
      try {
        const start = new Date(start_date);
        const end = new Date(end_date);

        // Validate dates
        if (isNaN(start.getTime()) || isNaN(end.getTime())) {
          return "Invalid date";
        }

        // If end date is before start date
        if (end.getTime() < start.getTime()) {
          return "Invalid range";
        }

        // Working hours: 9 AM to 7 PM (10 hours per day)
        const WORK_START_HOUR = 9;
        const WORK_END_HOUR = 19; // 7 PM in 24-hour format
        const WORK_HOURS_PER_DAY = WORK_END_HOUR - WORK_START_HOUR; // 10 hours

        let totalWorkingMinutes = 0;
        
        // Create a new date for iteration, starting from the start date
        let currentDate = new Date(start);
        
        // If start time is before 9 AM, adjust to 9 AM
        if (currentDate.getHours() < WORK_START_HOUR) {
          currentDate.setHours(WORK_START_HOUR, 0, 0, 0);
        }
        
        // If start time is after 7 PM, move to next day 9 AM
        if (currentDate.getHours() >= WORK_END_HOUR) {
          currentDate.setDate(currentDate.getDate() + 1);
          currentDate.setHours(WORK_START_HOUR, 0, 0, 0);
        }

        while (currentDate < end) {
          // Check if current date is a weekday (Monday = 1, Friday = 5)
          const dayOfWeek = currentDate.getDay();
          if (dayOfWeek >= 1 && dayOfWeek <= 5) { // Monday to Friday
            
            // Calculate work end time for current day
            let workEndTime = new Date(currentDate);
            workEndTime.setHours(WORK_END_HOUR, 0, 0, 0);
            
            // If the task ends before the work day ends, use task end time
            let effectiveEndTime = end < workEndTime ? end : workEndTime;
            
            // Calculate working minutes for this day
            let dayWorkingMs = effectiveEndTime.getTime() - currentDate.getTime();
            if (dayWorkingMs > 0) {
              totalWorkingMinutes += Math.floor(dayWorkingMs / (1000 * 60));
            }
            
            // If we've reached the end date, break
            if (end <= workEndTime) {
              break;
            }
          }
          
          // Move to next day at 9 AM
          currentDate.setDate(currentDate.getDate() + 1);
          currentDate.setHours(WORK_START_HOUR, 0, 0, 0);
        }

        // Convert total working minutes to readable format
        if (totalWorkingMinutes <= 0) {
          return "0h 0m";
        }

        const totalHours = Math.floor(totalWorkingMinutes / 60);
        const remainingMinutes = totalWorkingMinutes % 60;

        return `${totalHours}(h) ${remainingMinutes}(m)`;
      } catch (error) {
        console.error("Error calculating actual working time:", error);
        return "Error";
      }
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
