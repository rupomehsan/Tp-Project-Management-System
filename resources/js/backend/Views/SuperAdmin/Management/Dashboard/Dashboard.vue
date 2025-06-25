<template>
  <div class="container-fluid">
    <!--Start Dashboard Content-->
    <div class="row">
      <div class="col-12 col-lg-12">
      <div class="card">
        <div class="card-header">Calendar (Current Month)</div>
        <div class="card-body">
        <div class="calendar">
          <div class="calendar-header text-center mb-3">
          <h5>{{ monthName }} {{ currentYear }}</h5>
          </div>
          <div class="calendar-grid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px;">
          <!-- Weekday Headers -->
          <div class="day-name font-weight-bold bg-light py-2 border" v-for="day in weekDays" :key="day">
            {{ day }}
          </div>
          <!-- Blank Days -->
          <div
            class="day blank border"
            v-for="(blank, index) in blankDays"
            :key="'b' + index"
            style="background: #f8f9fa;"
          ></div>

          <!-- Actual Days -->
          <div
            class="day border py-2"
            v-for="day in daysInMonth"
            :key="'d' + day"
            style="min-height: 40px;"
          >
            <span>{{ day }}</span>
          </div>
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>
    <!--End Row-->
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="card">
          <div class="card-header">Latest Purchase Orders</div>
          <table
            class="table table-hover text-center table-bordered table_responsive"
          >
            <thead>
              <tr>
                <th>#</th>
                <th>Order Id</th>
                <th>reference</th>
                <th>suppliyer</th>
                <th>total</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in data?.getLatesOrders" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ item.Order_id ?? "N/A" }}</td>
                <td>{{ item.reference ?? "N/A" }}</td>
                <td>{{ item.suppliyer?.name ?? "N/A" }}</td>
                <td>{{ item.total ?? "N/A" }}</td>
                <td>{{ item.date ?? "N/A" }}</td>
                <td>
                  <router-link
                    :to="{
                      name: 'EditPurchaseOrder',
                      params: { id: item.slug },
                    }"
                  >
                    <button class="btn btn-info">View</button>
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="card">
          <div class="card-header">Latest Sales Orders</div>
          <table
            class="table table-hover text-center table-bordered table_responsive"
          >
            <thead>
              <tr>
                <th>#</th>
                <th>Order Id</th>
                <th>reference</th>
                <th>costomer</th>
                <th>total</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(item, index) in data?.getLatesSalesOrders"
                :key="index"
              >
                <td>{{ index + 1 }}</td>
                <td>{{ item.order_id ?? "N/A" }}</td>
                <td>{{ item.reference ?? "N/A" }}</td>
                <td>{{ item.customer?.name.substring(0, 10) ?? "N/A" }}</td>
                <td>{{ item.total ?? "N/A" }}</td>
                <td>{{ item.date ?? "N/A" }}</td>
                <td>
                  <router-link
                    :to="{ name: 'EditSalesOrder', params: { id: item.slug } }"
                  >
                    <button class="btn btn-info">View</button>
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!--End Dashboard Content-->
    <!--start overlay-->
    <div class="overlay"></div>
    <!--end overlay-->
  </div>
  <!-- End container-fluid-->
</template>

<script>
export default {
  data: () => ({
    data: {},
    currentDate: new Date(),
    weekDays: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
  }),
  created: async function () {
    await this.get_all_dashboard_data();
  },
  methods: {
    get_all_dashboard_data: async function () {
      let response = await axios.get("get-all-dashboard-data");
      if (response.status == 200) {
        this.data = response.data.data;
      }
    },
  },

  computed: {
    currentYear() {
      return this.currentDate.getFullYear();
    },
    monthName() {
      return this.currentDate.toLocaleString("default", { month: "long" });
    },
    daysInMonth() {
      const month = this.currentDate.getMonth();
      const year = this.currentYear;
      return new Array(
        new Date(year, month + 1, 0).getDate()
      ).fill(0).map((_, i) => i + 1);
    },
    blankDays() {
      const firstDay = new Date(this.currentYear, this.currentDate.getMonth(), 1);
      return new Array(firstDay.getDay()).fill(null);
    },
  },
};
</script>

<style></style>
