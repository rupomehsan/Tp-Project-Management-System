<template>
  <tr v-for="(item, dataindex) in data" :key="item.id" :class="[`table_rows table_row_${item.id}`, item.is_seen === 0 ? 'unseen-row' : '']">
    <td>
      <table-row-action :item="item"></table-row-action>
    </td>
    <td>
      <select-single :data="item" />
    </td>
    <template v-for="(row_item, index) in setup.table_row_data" :key="index">
      <td v-if="row_item == 'id'">
        {{ dataindex + 1 }}
      </td>
      <td v-else class="text-wrap max-w-120">
        <button type="button" v-if="row_item === 'link'" class="btn btn-primary" @click="handleLinkClick(item, `/${item[row_item]}`)">
          Click here
        </button>
        <span v-else>
          {{ trim_content(item[row_item], row_item) }}
        </span>
      </td>
    </template>
  </tr>
</template>

<script>
import axios from "axios";
import setup from "../../setup";
import SelectAll from "./select_data/SelectAll.vue";
import TableRowAction from "./TableRowAction.vue";
import SelectSingle from "./select_data/SelectSingle.vue";
export default {
  props: ["data"],
  data: () => ({
    setup,
  }),
  components: {
    SelectAll,
    TableRowAction,
    SelectSingle,
  },

  methods: {
    trim_content(content, row_item = null) {
      if (typeof content == "string") {
        if (row_item == "created_at" || row_item == "updated_at") {
          return new Date(content).toLocaleTimeString();
        }
        return content.length > 50 ? content.substring(0, 50) + "..." : content;
      }
      if (content && typeof content === "object") {
        for (const key of Object.keys(content)) {
          if (key === "title" && content.title) {
            return content.title;
          }
          if (key === "name" && content.name) {
            return content.name;
          }
        }
      }

      return content || "";
    },

    async handleLinkClick(data, link) {
      try {
        // Call API to mark as seen
        await axios.post(`/notifications/seen`, { data });
        // Redirect after marking as seen
        this.$router.push(link);
      } catch (e) {
        // Optionally handle error
        this.$router.push(link);
      }
    },
  },
};
</script>

<style scoped>
.max-w-120 {
  max-width: 120px;
}

.unseen-row {
  font-weight: bold;
  background: rgba(128, 128, 128, 0.377);
}
</style>
