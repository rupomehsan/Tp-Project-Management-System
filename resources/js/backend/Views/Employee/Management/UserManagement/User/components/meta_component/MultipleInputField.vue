<template>
  <!-- <div class="col-md-12">
    <div
      class="d-flex justify-content-between align-items-center pb-2 section-title"
    >
      <h5 class="m-0">Add Project document links</h5>
      <button
        class="btn btn-sm btn-outline-success"
        @click.prevent="add_link_row"
      >
        Add link
      </button>
    </div>
    <div
      class="row align-items-center"
      v-for="(link, index) in social_media"
      :key="'link-' + index"
    >
      <div class="col-md-5">
        <div class="form-group">
          <label for="">Name</label>
          <div class="mt-1 mb-3">
            <input
              class="form-control form-control-square mb-2"
              type="text"
              v-model="link.name"
              :name="`social_media[${index}][name]`"
              :class="{ custom_error: errors.links?.[index]?.name }"
            />
          </div>
          <div v-if="errors.links?.[index]?.name" class="text-danger small">
            {{ errors.links[index].name }}
          </div>
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          <label for="">Document link</label>
          <div class="mt-1 mb-3">
            <input
              class="form-control form-control-square mb-2"
              type="text"
              v-model="link.link"
              :name="`social_media[${index}][link]`"
              :class="{ custom_error: errors.links?.[index]?.link }"
            />
          </div>
          <div v-if="errors.links?.[index]?.link" class="text-danger small">
            {{ errors.links[index].link }}
          </div>
        </div>
      </div>
      <div class="col-md-2 d-flex align-items-center justify-content-center">
        <button
          class="btn btn-sm btn-outline-danger"
          @click.prevent="delete_link_by_id(link.id)"
        >
          <i class="fa fa-trash"></i>
        </button>
      </div>
    </div>
  </div> -->

  <div class="col-md-12">
    <div
      class="d-flex justify-content-between align-items-center pb-2 section-title"
    >
      <h5 class="m-0">Add Social links</h5>
      <button
        class="btn btn-sm btn-outline-success"
        @click.prevent="add_link_row"
      >
        Add link
      </button>
    </div>
    <div
      class="row align-items-center"
      v-for="(link, index) in social_media"
      :key="'link-' + index"
    >
      <div class="col-md-5">
        <div class="form-group">
          <label for="">Social Media Name</label>
          <div class="mt-1 mb-3">
            <input
              class="form-control form-control-square mb-2"
              type="text"
              v-model="link.name"
              :name="`social_media[${index}][name]`"
              :class="{ custom_error: errors.links?.[index]?.name }"
            />
          </div>
          <div v-if="errors.links?.[index]?.name" class="text-danger small">
            {{ errors.links[index].name }}
          </div>
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          <label for="">Social link</label>
          <div class="mt-1 mb-3">
            <input
              class="form-control form-control-square mb-2"
              type="text"
              v-model="link.link"
              :name="`social_media[${index}][link]`"
              :class="{ custom_error: errors.links?.[index]?.link }"
            />
          </div>
          <div v-if="errors.links?.[index]?.link" class="text-danger small">
            {{ errors.links[index].link }}
          </div>
        </div>
      </div>
      <div class="col-md-2 d-flex align-items-center justify-content-center">
        <button
          class="btn btn-sm btn-outline-danger"
          @click.prevent="delete_link_row(index)"
        >
          <i class="fa fa-trash"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { store } from "../../store";

export default {
  props: {
    name: String,
    value: { type: Object, default: () => ({ links: [], files: [] }) },
  },
  data() {
    return {
      errors: { links: [] },
      social_media: [{ name: "", link: "" }],
    };
  },
  created: function () {
    this.$watch(
      "item",
      (newValue) => {
        if (newValue) {
          if (newValue.social_media) {
            this.social_media = newValue.social_media.map((doc) => ({
              name: doc.name,
              link: doc.link,
            }));
          }
        }
      },
      { immediate: true, deep: true }
    );
  },
  watch: {
    social_media: {
      handler() {
        this.emitChange();
      },
      deep: true,
    },
    project_document_files: {
      handler() {
        this.emitChange();
      },
      deep: true,
    },
  },

  methods: {
    ...mapActions(store, ["get_all", "set_paginate", "set_page"]),

    // async delete_file_by_id(index, id) {
    //   try {
    //     let con = await window.s_confirm("Delete this file?");
    //     if (con) {
    //       let response = await axios.post(
    //         `/project/filelink-delete/${id}?index=${index}`
    //       );
    //       if (response.data.status == "success") {
    //         window.s_alert("File deleted successfully");
    //         this.project_document_files.splice(index, 1);
    //       }
    //     }
    //   } catch (e) {
    //     window.s_warning(e.response?.data?.message || "Delete failed");
    //   }
    // },

    add_link_row() {
      this.social_media.push({ name: "", link: "" });
    },
    delete_link_row(index) {
      if (this.social_media.length < 2) return;
      this.social_media.splice(index, 1);
    },
    emitChange() {
      this.$emit("input", {
        links: this.social_media,
      });
    },
  },
  computed: {
    ...mapState(store, ["item"]),
  },
};
</script>

<style scoped>
.image_preview {
  width: 40px;
  height: 40px;
  margin-left: 30px;
  object-fit: cover;
  border: 1px solid #dddddd35;
  padding: 3px;
}
.section-title {
  border: 1px solid #dddddd78;
  padding: 10px;
  margin: 10px 0;
  border-radius: 5px;
  font-weight: 500;
  color: #343a40;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
