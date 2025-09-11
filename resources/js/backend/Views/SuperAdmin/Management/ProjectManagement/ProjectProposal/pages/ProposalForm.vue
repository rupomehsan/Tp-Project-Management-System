<template>
  <div>
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5 class="text-capitalize"></h5>
          <div></div>
        </div>
        <div class="card-body card_body_fixed_height">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Category Id</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.category_id"
                    class="form-control"
                    name="category_id"
                    id="category_id"
                  >
                    <option value="">Selet-- Category Name</option>
                    <option
                      v-for="item in categoryData?.data"
                      :key="item.id"
                      :value="item.id"
                    >
                      {{ item.name }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Priority</label>
                <div class="mt-1 mb-3">
                  <select
                    v-model="form_fields.priority"
                    class="form-control form-control-square mb-2"
                    name="priority"
                    id="priority"
                  >
                    <option value="normal">Normal</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="">task description</label>
                <div class="mt-1 mb-3">
                  <text-editor :name="'description'" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-light btn-square px-5">
            <i class="icon-lock"></i> Submit
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
// import { projectProposalStore } from "../store";

export default {
  data: () => ({
    param_id: null,
    form_fields: {
      category_id: "",
      priority: "",
      description: "",
    },
    categoryData: [],
  }),
};
</script>

<style>
@import "../styles/theme.css";

/* Additional ProposalForm specific styles */
.card_body_fixed_height {
  max-height: 80vh;
  overflow-y: auto;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  margin-bottom: 0.5rem;
  display: block;
  font-weight: 500;
}

/* Enhanced form styling for better UX */
.form-control {
  border-radius: 0.375rem;
  padding: 0.75rem;
  font-size: 0.875rem;
  line-height: 1.5;
}

.form-control:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

/* Better spacing for mobile */
@media (max-width: 768px) {
  .form-group {
    margin-bottom: 1rem;
  }

  .card_body_fixed_height {
    max-height: 70vh;
  }
}
</style>
