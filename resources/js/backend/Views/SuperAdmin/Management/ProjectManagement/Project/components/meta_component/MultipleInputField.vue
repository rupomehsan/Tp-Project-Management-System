<template>
  <div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center pb-2 section-title">
      <h5 class="m-0">Add Project documents</h5>
      <button class="btn btn-sm btn-outline-success" @click.prevent="add_row('project_document')">Add row</button>
    </div>

    <div class="row align-items-center" v-for="(project_document, index) in project_document_data" :key="index">
      <div class="col-md-3">
        <div class="form-group">
          <label for="">name</label>
          <div class="mt-1 mb-3">
            <input
              class="form-control form-control-square mb-2"
              type="text"
              :name="`project_documents[${index}][name]`"
              v-model="project_document.name"
              id="name"
              :class="{
                custom_error: errors['project_document'] && errors['project_document'][index] && errors['project_document'][index].name,
              }"
            />
          </div>
          <div v-if="errors['project_document'] && errors['project_document'][index] && errors['project_document'][index].name" class="text-danger small">
            {{ errors["project_document"][index].name }}
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label for="">Doucument type</label>
          <div class="mt-1 mb-3">
            <select
              class="form-control form-control-square mb-2"
              :name="`project_documents[${index}][type]`"
              v-model="project_document.type"
              :class="{
                custom_error: errors['project_document'] && errors['project_document'][index] && errors['project_document'][index].type,
              }"
            >
              
              <option value="link">Link</option>
              <option value="file">File</option>
            </select>
          </div>
          <div v-if="errors['project_document'] && errors['project_document'][index] && errors['project_document'][index].type" class="text-danger small">
            {{ errors["project_document"][index].type }}
          </div>
        </div>
      </div>
      <div class="col-md-3" v-if="project_document.type === 'link'">
        <div class="form-group">
          <label for="">Document link</label>
          <div class="mt-1 mb-3">
            <input
              class="form-control form-control-square mb-2"
              type="text"
              :name="`project_documents[${index}][link]`"
              v-model="project_document.link"
              id="link"
              :class="{
                custom_error: errors['project_document'] && errors['project_document'][index] && errors['project_document'][index].link,
              }"
            />
          </div>
          <div v-if="errors['project_document'] && errors['project_document'][index] && errors['project_document'][index].link" class="text-danger small">
            {{ errors["project_document"][index].link }}
          </div>
        </div>
      </div>
     
      <div class="col-md-3" v-if="project_document.type === 'file'">
        <div class="form-group">
          <div>
             <label for="">Document file</label>
            <a :href="project_document_data[index].file" data-lightbox="image-1" data-title="My caption">
              <img class="image_preview" v-if="project_document_data[index].file" :src="project_document_data[index].file" />
            </a>
          </div>

          <div class="mt-1 mb-3">
            <input
              class="form-control form-control-square mb-2"
              type="file"
              @change="onImageChange($event, index)"
              :name="`project_documents[${index}][file]`"
              id="file"
              :class="{
                custom_error: errors['project_document'] && errors['project_document'][index] && errors['project_document'][index].file,
              }"
            />
          </div>
          <div v-if="errors['project_document'] && errors['project_document'][index] && errors['project_document'][index].file" class="text-danger small">
            {{ errors["project_document"][index].file }}
          </div>
        </div>
      </div>
      <div class="col-md-1 d-flex align-items-center justify-content-center">
        <button
          class="btn btn-sm btn-outline-danger"
          :style="{
            width: '50%',
            marginTop: !errors['project_document']?.[index]?.title ? '30px' : '0',
          }"
          @click.prevent="delete_row('project_document', index)"
        >
          <i class="fa fa-trash"></i>
        </button>
      </div>
    </div>
  </div>
</template>
<script>
import { mapActions, mapState, mapWritableState } from "pinia";
import { store } from "../../store";

export default {
  props: {
    name: {
      type: String,
    },
    value: {
      type: Array,
      default: [],
    },
  },
  data: () => ({
    errors: [],
    //----------- for floor_plan list input ----------

    project_document_data: [
      {
        name: "",
        type: "link",
        link: "",
        file: "",
      },
    ],

    //----------- for floor_plan list input ----------
  }),
  created: function () {
    this.$watch("item", (newValue) => {
      console.log("newValue", newValue?.project_documents);

      if (newValue) {
        this.project_document_data = newValue?.project_documents?.map((project_document) => ({
          name: project_document.name,
          email: project_document.email,
          age: project_document.age,
          image: project_document.image,
        }));
      }
    });
  },

  methods: {
    ...mapActions(store, ["get_all", "set_paginate", "set_page"]),
    add_row() {
      this.project_document_data.push({
        name: "",
        age: "",
        email: "",
        image: "",
      });
    },
    onImageChange(event, index) {
      const file = event.target.files[0];
      if (file) {
        // store the file for submission
        this.project_document_data[index].image = file;

        // create base64 preview
        const reader = new FileReader();
        reader.onload = (e) => {
          this.project_document_data[index].image = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    delete_row(index) {
      if (this.project_document_data.length < 2) {
        console.error("cant delete first row");
        return;
      }
      this.project_document_data.splice(index, 1);
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
