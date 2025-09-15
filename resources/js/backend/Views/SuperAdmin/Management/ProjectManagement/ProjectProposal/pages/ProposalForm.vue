<template>
  <div class="proposal-form-wrapper">
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5 class="text-capitalize">Proposal Form</h5>
        </div>
        <div class="card-body card_body_fixed_height">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Proposal Title</label>
                <input
                  v-model="proposal.proposal_title"
                  class="form-control"
                  type="text"
                />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Prepared For - Name</label>
                <input
                  v-model="proposal.prepare_for_name"
                  class="form-control"
                  type="text"
                />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Designation</label>
                <input
                  v-model="proposal.prepare_for_designation"
                  class="form-control"
                  type="text"
                />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Company Name</label>
                <input
                  v-model="proposal.prepare_for_company_name"
                  class="form-control"
                  type="text"
                />
              </div>
            </div>
          </div>

          <!-- Additional "Prepared For" Contact Details -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Address</label>
                <input
                  v-model="proposal.prepare_for_address"
                  class="form-control"
                  type="text"
                  placeholder="Street address"
                />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>City</label>
                <input
                  v-model="proposal.prepare_for_city"
                  class="form-control"
                  type="text"
                  placeholder="City, State ZIP"
                />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Phone</label>
                <input
                  v-model="proposal.prepare_for_phone"
                  class="form-control"
                  type="text"
                  placeholder="Phone number"
                />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Mobile</label>
                <input
                  v-model="proposal.prepare_for_mobile"
                  class="form-control"
                  type="text"
                  placeholder="Mobile number"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Prepared By - Name</label>
                <input
                  v-model="proposal.prepare_by_name"
                  class="form-control"
                  type="text"
                />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Designation</label>
                <input
                  v-model="proposal.prepare_by_designation"
                  class="form-control"
                  type="text"
                />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Company Name</label>
                <input
                  v-model="proposal.prepare_by_company_name"
                  class="form-control"
                  type="text"
                />
              </div>
            </div>
          </div>

          <!-- Additional "Prepared By" Contact Details -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Address</label>
                <input
                  v-model="proposal.prepare_by_address"
                  class="form-control"
                  type="text"
                  placeholder="Company address"
                />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>City</label>
                <input
                  v-model="proposal.prepare_by_city"
                  class="form-control"
                  type="text"
                  placeholder="City, State ZIP"
                />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Phone</label>
                <input
                  v-model="proposal.prepare_by_phone"
                  class="form-control"
                  type="text"
                  placeholder="Company phone"
                />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Email</label>
                <input
                  v-model="proposal.prepare_by_email"
                  class="form-control"
                  type="email"
                  placeholder="company@example.com"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Website</label>
                <input
                  v-model="proposal.prepare_by_website"
                  class="form-control"
                  type="text"
                  placeholder="www.company.com"
                />
              </div>
            </div>
          </div>

          <!-- 2. Cover Letter -->
          <h6>Cover Letter</h6>
          <div class="form-group">
            <text-editor
              :name="'cover_letter'"
              :value="proposal.cover_letter"
              @input="(value) => setField('cover_letter', value)"
            />
          </div>
          <!-- 2. Executive Summary -->
          <h6>Executive Summary</h6>
          <div class="form-group">
            <text-editor
              :name="'executive_summary'"
              :value="proposal.executive_summary"
              @input="(value) => setField('executive_summary', value)"
            />
          </div>
          <h6>Technology Stack</h6>
          <div class="form-group">
            <text-editor
              :name="'technology_stack'"
              :value="proposal.technology_stack"
              @input="(value) => setField('technology_stack', value)"
            />
          </div>
          <!-- 3. Project Overview -->
          <h6>Project Overview</h6>
          <div class="form-group">
            <text-editor
              :name="'project_overview'"
              :value="proposal.project_overview"
              @input="(value) => setField('project_overview', value)"
            />
          </div>

          <!-- 4. Timeline (Dynamic) -->
          <h6>Timeline</h6>
          <div
            v-for="(item, idx) in proposal.timeline"
            :key="idx"
            class="border p-2 mb-2"
          >
            <div class="row">
              <div class="col-md-3">
                <input
                  v-model="item.title"
                  class="form-control"
                  placeholder="Title"
                />
              </div>
              <div class="col-md-4">
                <input
                  v-model="item.description"
                  class="form-control"
                  placeholder="Description"
                />
              </div>
              <div class="col-md-2">
                <input
                  v-model="item.date"
                  class="form-control"
                  placeholder="Date"
                  type="date"
                />
              </div>
              <div class="col-md-2">
                <input
                  v-model="item.weeks"
                  class="form-control"
                  placeholder="Weeks"
                  type="number"
                  min="1"
                />
              </div>
              <div class="col-md-1 d-flex align-items-center">
                <button
                  type="button"
                  class="btn btn-danger btn-sm"
                  @click="removeTimeline(idx)"
                >
                  Remove
                </button>
              </div>
            </div>
          </div>
          <button
            type="button"
            class="btn btn-primary btn-sm mb-3"
            @click="addTimeline"
          >
            + Add Timeline
          </button>

          <!-- 5. Payment Invoice (Dynamic) -->
          <h6>Payment Invoice</h6>
          <div
            v-for="(invoice, iidx) in proposal.payment_invoice"
            :key="iidx"
            class="border p-2 mb-2"
          >
            <div class="form-group">
              <input
                v-model="invoice.heading"
                class="form-control mb-2"
                placeholder="Table Heading"
              />
            </div>
            <div
              v-for="(row, ridx) in invoice.rows"
              :key="ridx"
              class="row mb-1"
            >
              <div class="col-md-4">
                <input
                  v-model="row.title"
                  class="form-control"
                  placeholder="Title"
                />
              </div>
              <div class="col-md-4">
                <input
                  v-model="row.date"
                  class="form-control"
                  placeholder="Date"
                  type="date"
                />
              </div>
              <div class="col-md-3">
                <input
                  v-model="row.total"
                  class="form-control"
                  placeholder="Total"
                  type="number"
                  min="0"
                />
              </div>
              <div class="col-md-1 d-flex align-items-center">
                <button
                  type="button"
                  class="btn btn-danger btn-sm"
                  @click="removePaymentInvoiceRow(iidx, ridx)"
                >
                  Remove
                </button>
              </div>
            </div>
            <button
              type="button"
              class="btn btn-secondary btn-sm"
              @click="addPaymentInvoiceRow(iidx)"
            >
              + Add Row
            </button>
            <button
              type="button"
              class="btn btn-danger btn-sm ml-2"
              @click="removePaymentInvoice(iidx)"
            >
              Remove Table
            </button>
          </div>
          <button
            type="button"
            class="btn btn-primary btn-sm mb-3"
            @click="addPaymentInvoice"
          >
            + Add Payment Invoice
          </button>

          <!-- 6. Terms & Conditions (Dynamic) -->
          <h6>Terms & Conditions</h6>
          <div
            v-for="(term, tidx) in proposal.terms_conditions"
            :key="tidx"
            class="border p-2 mb-2"
          >
            <div class="row">
              <div class="col-md-5">
                <input
                  v-model="term.title"
                  class="form-control"
                  placeholder="Title"
                />
              </div>
              <div class="col-md-6">
                <input
                  v-model="term.description"
                  class="form-control"
                  placeholder="Description"
                />
              </div>
              <div class="col-md-1 d-flex align-items-center">
                <button
                  type="button"
                  class="btn btn-danger btn-sm"
                  @click="removeTermsCondition(tidx)"
                >
                  Remove
                </button>
              </div>
            </div>
          </div>
          <button
            type="button"
            class="btn btn-primary btn-sm mb-3"
            @click="addTermsCondition"
          >
            + Add Terms & Condition
          </button>
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
import { defineComponent, computed, watch } from "vue";
import { projectProposalStore } from "../store";

export default defineComponent({
  setup() {
    const store = projectProposalStore();

    // Direct reference to store data for v-model binding
    const proposal = store.currentProposal;

    // Watch for deep changes in proposal data and save to localStorage
    let saveTimeout;
    watch(
      () => store.currentProposal,
      (newVal) => {
        // Debounce the save to avoid excessive localStorage writes
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
          store.saveToLocalStorage();
        }, 300);
      },
      { deep: true }
    );

    // Field setter helper
    function setField(key, value) {
      store.setField(key, value);
    }

    // Timeline
    function addTimeline() {
      store.addTimelineItem({
        title: "",
        description: "",
        date: "",
        weeks: "",
      });
    }
    function removeTimeline(idx) {
      store.removeTimelineItem(idx);
    }

    // Payment Invoice
    function addPaymentInvoice() {
      store.addPaymentInvoice({ heading: "", rows: [] });
    }
    function removePaymentInvoice(iidx) {
      store.removePaymentInvoice(iidx);
    }
    function addPaymentInvoiceRow(iidx) {
      store.addPaymentInvoiceRow(iidx, { title: "", date: "", total: "" });
    }
    function removePaymentInvoiceRow(iidx, ridx) {
      store.removePaymentInvoiceRow(iidx, ridx);
    }

    // Terms & Conditions
    function addTermsCondition() {
      store.addTermsCondition({ title: "", description: "" });
    }
    function removeTermsCondition(tidx) {
      store.removeTermsCondition(tidx);
    }
    function updateTermsCondition(tidx, term) {
      store.updateTermsCondition(tidx, term);
    }

    function submitHandler() {
      // You can handle form submission here, e.g., send proposal to API
      alert("Proposal submitted! (implement API call as needed)");
    }

    return {
      proposal,
      setField,
      addTimeline,
      removeTimeline,
      addPaymentInvoice,
      removePaymentInvoice,
      addPaymentInvoiceRow,
      removePaymentInvoiceRow,
      addTermsCondition,
      removeTermsCondition,
      updateTermsCondition,
      submitHandler,
    };
  },
});
</script>

<style scoped>
@import "../styles/theme.css";
.proposal-form-wrapper {
  height: 90vh;
  overflow-y: auto;
}
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
