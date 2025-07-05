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
      v-for="(link, index) in project_document_links"
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
              :name="`project_document_links[${index}][name]`"
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
              :name="`project_document_links[${index}][link]`"
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
      v-for="(link, index) in project_document_links"
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
              :name="`project_document_links[${index}][name]`"
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
              :name="`project_document_links[${index}][link]`"
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
  <div class="col-md-12 mt-4">
    <div
      class="d-flex justify-content-between align-items-center pb-2 section-title"
    >
      <h5 class="m-0">Add Project document files</h5>
      <button
        class="btn btn-sm btn-outline-success"
        @click.prevent="add_file_row"
      >
        Add file
      </button>
    </div>

    <div
      class="row align-items-center"
      v-for="(file, index) in project_document_files"
      :key="'file-' + index"
    >
      <div class="col-md-5">
        <div class="form-group">
          <label for="">Name</label>
          <div class="mt-1 mb-3">
            <input
              class="form-control form-control-square mb-2"
              type="text"
              v-model="file.name"
              :name="`project_document_files[${index}][name]`"
              :class="{ custom_error: errors.files?.[index]?.name }"
            />
          </div>
          <div v-if="errors.files?.[index]?.name" class="text-danger small">
            {{ errors.files[index].name }}
          </div>
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          <label for="">Document file</label>
          <a
            v-if="file.file"
            target="_blank"
            :href="file.file"
            data-lightbox="image-1"
            data-title="My caption"
          >
            <img
              v-if="file.file && /\.(jpe?g|png|gif|bmp|webp|svg)$/i.test(file.file)"
              class="image_preview"
              :src="file.file"
              alt="Document preview"
            />
            <button
              v-else
              type="button"
              @click="window.open(file.file, '_blank')"
              class="btn btn-sm btn-outline-primary"
            >
              Show file
            </button>
          </a>
          <div class="mt-1 mb-3">
            <input
              class="form-control form-control-square mb-2"
              type="file"
              @change="onFileChange($event, index)"
              :name="`project_document_files[${index}][file]`"
              :class="{ custom_error: errors.files?.[index]?.file }"
            />
          </div>
          <div v-if="errors.files?.[index]?.file" class="text-danger small">
            {{ errors.files[index].file }}
          </div>
        </div>
      </div>
      <div class="col-md-2 d-flex align-items-center justify-content-center">
        <button
          class="btn btn-sm btn-outline-danger"
          @click.prevent="delete_file_by_id(index, item.id)"
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
      errors: { links: [], files: [] },
      project_document_links: [{ name: "", link: "" }],
      project_document_files: [{ name: "", file: "" }],
    };
  },
  created: function () {
    this.$watch(
      "item",
      (newValue) => {
        if (newValue) {
          if (newValue.project_document_links) {
            this.project_document_links = newValue.project_document_links.map(
              (doc) => ({
                name: doc.name,
                link: doc.link,
              })
            );
          }
          // if (newValue.project_document_links) {
          //   this.project_document_links = newValue.project_document_links.map(doc => ({
          //     id  : doc.id,
          //     name: doc.name,
          //     link: doc.link,
          //   }));
          // }
          if (newValue.project_document_files) {
            this.project_document_files = newValue.project_document_files.map(
              (doc) => ({
                id: doc.id, // ← keep it
                name: doc.name,
                file: doc.file,
              })
            );
          }
        }
      },
      { immediate: true, deep: true }
    );
  },
  watch: {
    project_document_links: {
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

    // async delete_link_by_id(id) {
    //   try {
    //     await axios.post(`/project/filelink-delete/${this.item.slug}`, {
    //       id,
    //       type: "link",
    //     });
    //     this.project_document_links = this.project_document_links.filter(
    //       (l) => l.id !== id
    //     );
    //   } catch (e) {
    //     window.s_warning(e.response?.data?.message || "Delete failed");
    //   }
    // },

    async delete_file_by_id(index, id) {
      try {
        let con = await window.s_confirm("Delete this file?");
        if (con) {
          let response = await axios.post(
            `/project/filelink-delete/${id}?index=${index}`
          );
          if (response.data.status == "success") {
            window.s_alert("File deleted successfully");
            this.project_document_files.splice(index, 1);
          }
        }
      } catch (e) {
        window.s_warning(e.response?.data?.message || "Delete failed");
      }
    },

    add_link_row() {
      this.project_document_links.push({ name: "", link: "" });
    },
    delete_link_row(index) {
      if (this.project_document_links.length < 2) return;
      this.project_document_links.splice(index, 1);
    },
    add_file_row() {
      this.project_document_files.push({
        name: "",
        file: "",
      });
    },
    delete_file_row(index) {
      if (this.project_document_files.length < 2) return;
      this.project_document_files.splice(index, 1);
    },
    onFileChange(event, index) {
      const file = event.target.files[0];
      if (file) {
        // store the file for submission
        this.project_document_files[index].file = file;
        // create base64 preview
        const reader = new FileReader();
        reader.onload = (e) => {
          this.project_document_files[index].file = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    emitChange() {
      this.$emit("input", {
        links: this.project_document_links,
        files: this.project_document_files,
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
