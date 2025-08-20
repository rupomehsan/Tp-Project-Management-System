<template>
  <div class="proposal-form">
    <div class="form-header">
      <h2>Create Proposal</h2>
      <div class="form-tabs">
        <button
          type="button"
          v-for="(tab, index) in formTabs"
          :key="index"
          :class="['tab-btn', { active: activeTab === index }]"
          @click="activeTab = index"
        >
          <i :class="tab.icon"></i>
          {{ tab.label }}
        </button>
      </div>
    </div>

    <form @submit.prevent="submitProposal" class="proposal-form-content">
      <!-- Tab 1: Basic Information -->
      <div v-if="activeTab === 0" class="form-section">
        <h3><i class="fas fa-info-circle"></i> Basic Information</h3>

        <div class="form-row">
          <div class="form-group">
            <label for="title">Proposal Title *</label>
            <input
              type="text"
              id="title"
              v-model="currentProposal.title"
              required
              placeholder="Enter proposal title"
            />
          </div>
          <div class="form-group">
            <label for="proposal_id">Proposal ID</label>
            <input
              type="text"
              id="proposal_id"
              v-model="currentProposal.proposal_id"
              placeholder="Auto-generated if empty"
            />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="client_name">Client Name *</label>
            <input
              type="text"
              id="client_name"
              v-model="currentProposal.client.name"
              required
              placeholder="Enter client name"
            />
          </div>
          <div class="form-group">
            <label for="client_position">Client Position</label>
            <input
              type="text"
              id="client_position"
              v-model="currentProposal.client.position"
              placeholder="Enter client position"
            />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="client_company">Client Company</label>
            <input
              type="text"
              id="client_company"
              v-model="currentProposal.client.company"
              placeholder="Enter client company"
            />
          </div>
          <div class="form-group">
            <label for="client_email">Client Email</label>
            <input
              type="email"
              id="client_email"
              v-model="currentProposal.client.email"
              placeholder="Enter client email"
            />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="project_category">Project Category</label>
            <select id="project_category" v-model="currentProposal.project_category">
              <option value="">Select Category</option>
              <option value="web_development">Web Development</option>
              <option value="mobile_app">Mobile App</option>
              <option value="design">Design</option>
              <option value="consulting">Consulting</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label for="priority">Priority</label>
            <select id="priority" v-model="currentProposal.priority">
              <option value="">Select Priority</option>
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Tab 2: Cover Letter & Executive Summary -->
      <div v-if="activeTab === 1" class="form-section">
        <h3>
          <i class="fas fa-file-alt"></i> Cover Letter & Executive Summary
        </h3>

        <div class="form-group">
          <label for="cover_letter">Cover Letter</label>
          <textarea
            id="cover_letter"
            v-model="currentProposal.cover_letter"
            rows="6"
            placeholder="Write your cover letter here..."
          ></textarea>
        </div>

        <div class="form-group">
          <label for="executive_summary">Executive Summary</label>
          <textarea
            id="executive_summary"
            v-model="currentProposal.executive_summary"
            rows="8"
            placeholder="Provide a comprehensive executive summary..."
          ></textarea>
        </div>

        <div class="form-group">
          <label for="key_features">Key Features (one per line)</label>
          <textarea
            id="key_features"
            v-model="currentProposal.key_features"
            rows="6"
            placeholder="Feature 1&#10;Feature 2&#10;Feature 3"
          ></textarea>
        </div>
      </div>

      <!-- Tab 3: Project Objectives -->
      <div v-if="activeTab === 2" class="form-section">
        <h3><i class="fas fa-bullseye"></i> Project Objectives</h3>

        <div class="objectives-container">
          <div
            v-for="(objective, index) in currentProposal.objectives"
            :key="index"
            class="objective-item"
          >
            <div class="objective-header">
              <input
                v-model="objective.title"
                placeholder="Objective title"
                class="objective-title-input"
              />
              <button
                type="button"
                @click="removeObjective(index)"
                class="btn-remove"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
            <textarea
              v-model="objective.description"
              placeholder="Describe this objective..."
              rows="3"
            ></textarea>
            <div class="form-row">
              <div class="form-group">
                <label>Icon Class</label>
                <input v-model="objective.icon" placeholder="fas fa-target" />
              </div>
              <div class="form-group">
                <label>Success Metric</label>
                <input
                  v-model="objective.metric"
                  placeholder="e.g., 50% increase"
                />
              </div>
            </div>
          </div>

          <button type="button" @click="addObjective" class="btn-add">
            <i class="fas fa-plus"></i> Add Objective
          </button>
        </div>
      </div>

      <!-- Tab 4: Project Overview & Timeline -->
      <div v-if="activeTab === 3" class="form-section">
        <h3>
          <i class="fas fa-project-diagram"></i> Project Overview & Timeline
        </h3>

        <div class="form-group">
          <label for="project_overview">Project Overview</label>
          <textarea
            id="project_overview"
            v-model="currentProposal.project_overview"
            rows="6"
            placeholder="Detailed project overview..."
          ></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="estimated_duration">Estimated Duration (days)</label>
            <input
              type="number"
              id="estimated_duration"
              v-model="currentProposal.estimated_duration"
            />
          </div>
          <div class="form-group">
            <label for="start_date">Proposed Start Date</label>
            <input type="date" id="start_date" v-model="currentProposal.start_date" />
          </div>
        </div>

        <div class="timeline-container">
          <h4>Project Timeline</h4>
          <div
            v-for="(phase, index) in currentProposal.timeline"
            :key="index"
            class="timeline-item-form"
          >
            <div class="timeline-header">
              <input
                v-model="phase.title"
                placeholder="Phase title"
                class="phase-title-input"
              />
              <button
                type="button"
                @click="removeTimelinePhase(index)"
                class="btn-remove"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Duration</label>
                <input v-model="phase.duration" placeholder="e.g., 2 weeks" />
              </div>
              <div class="form-group">
                <label>Start Date</label>
                <input type="date" v-model="phase.start_date" />
              </div>
            </div>
            <textarea
              v-model="phase.description"
              placeholder="Phase description..."
              rows="2"
            ></textarea>
          </div>

          <button type="button" @click="addTimelinePhase" class="btn-add">
            <i class="fas fa-plus"></i> Add Phase
          </button>
        </div>
      </div>

      <!-- Tab 5: Budget & Pricing -->
      <div v-if="activeTab === 4" class="form-section">
        <h3><i class="fas fa-dollar-sign"></i> Budget & Pricing</h3>

        <div class="budget-container">
          <div
            v-for="(item, index) in currentProposal.budget_items"
            :key="index"
            class="budget-item"
          >
            <div class="budget-header">
              <input
                v-model="item.description"
                placeholder="Budget item description"
                class="budget-desc-input"
              />
              <button
                type="button"
                @click="removeBudgetItem(index)"
                class="btn-remove"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Quantity</label>
                <input
                  type="number"
                  v-model="item.quantity"
                  @input="calculateBudget"
                />
              </div>
              <div class="form-group">
                <label>Unit Price ($)</label>
                <input
                  type="number"
                  v-model="item.unit_price"
                  step="0.01"
                  @input="calculateBudget"
                />
              </div>
              <div class="form-group">
                <label>Total ($)</label>
                <input
                  type="number"
                  :value="item.quantity * item.unit_price"
                  readonly
                />
              </div>
            </div>
          </div>

          <button type="button" @click="addBudgetItem" class="btn-add">
            <i class="fas fa-plus"></i> Add Budget Item
          </button>

          <div class="budget-summary">
            <div class="form-row">
              <div class="form-group">
                <label>Subtotal ($)</label>
                <input type="number" :value="budgetSubtotal" readonly />
              </div>
              <div class="form-group">
                <label>Tax Rate (%)</label>
                <input
                  type="number"
                  v-model="currentProposal.tax_rate"
                  step="0.01"
                  @input="calculateBudget"
                />
              </div>
              <div class="form-group">
                <label>Total Budget ($)</label>
                <input
                  type="number"
                  :value="budgetTotal"
                  readonly
                  class="total-budget"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 6: Milestones & Invoices -->
      <div v-if="activeTab === 5" class="form-section">
        <h3><i class="fas fa-flag-checkered"></i> Milestones & Invoices</h3>

        <div class="milestones-container">
          <h4>Project Milestones</h4>
          <div
            v-for="(milestone, index) in currentProposal.milestones"
            :key="index"
            class="milestone-item"
          >
            <div class="milestone-header">
              <input
                v-model="milestone.title"
                placeholder="Milestone title"
                class="milestone-title-input"
              />
              <button
                type="button"
                @click="removeMilestone(index)"
                class="btn-remove"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Due Date</label>
                <input type="date" v-model="milestone.due_date" />
              </div>
              <div class="form-group">
                <label>Payment Amount ($)</label>
                <input
                  type="number"
                  v-model="milestone.payment_amount"
                  step="0.01"
                />
              </div>
              <div class="form-group">
                <label>Status</label>
                <select v-model="milestone.status">
                  <option value="pending">Pending</option>
                  <option value="in_progress">In Progress</option>
                  <option value="completed">Completed</option>
                </select>
              </div>
            </div>
            <textarea
              v-model="milestone.description"
              placeholder="Milestone description..."
              rows="2"
            ></textarea>
          </div>

          <button type="button" @click="addMilestone" class="btn-add">
            <i class="fas fa-plus"></i> Add Milestone
          </button>
        </div>
      </div>

      <!-- Tab 7: Terms & Agreement -->
      <div v-if="activeTab === 6" class="form-section">
        <h3><i class="fas fa-file-contract"></i> Terms & Agreement</h3>

        <div class="form-group">
          <label for="terms_conditions">Terms & Conditions</label>
          <textarea
            id="terms_conditions"
            v-model="currentProposal.terms_conditions"
            rows="8"
            placeholder="Enter terms and conditions..."
          ></textarea>
        </div>

        <div class="form-group">
          <label for="payment_terms">Payment Terms</label>
          <textarea
            id="payment_terms"
            v-model="currentProposal.payment_terms"
            rows="4"
            placeholder="Enter payment terms..."
          ></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="validity_period">Proposal Validity (days)</label>
            <input
              type="number"
              id="validity_period"
              v-model="currentProposal.validity_period"
            />
          </div>
          <div class="form-group">
            <label for="acceptance_deadline">Acceptance Deadline</label>
            <input
              type="date"
              id="acceptance_deadline"
              v-model="currentProposal.acceptance_deadline"
            />
          </div>
        </div>

        <div class="signature-section">
          <h4>Signature Information</h4>
          <div class="form-row">
            <div class="form-group">
              <label for="signatory_name">Signatory Name</label>
              <input
                type="text"
                id="signatory_name"
                v-model="currentProposal.signature.name"
              />
            </div>
            <div class="form-group">
              <label for="signatory_title">Signatory Title</label>
              <input
                type="text"
                id="signatory_title"
                v-model="currentProposal.signature.title"
              />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="company_name">Company Name</label>
              <input
                type="text"
                id="company_name"
                v-model="currentProposal.signature.company"
              />
            </div>
            <div class="form-group">
              <label for="signature_date">Signature Date</label>
              <input
                type="date"
                id="signature_date"
                v-model="currentProposal.signature.date"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation & Submit -->
      <div class="form-navigation">
        <button
          type="button"
          @click="prevTab"
          :disabled="activeTab === 0"
          class="btn-nav btn-prev"
        >
          <i class="fas fa-chevron-left"></i> Previous
        </button>

        <button
          type="button"
          @click="nextTab"
          v-if="activeTab < formTabs.length - 1"
          class="btn-nav btn-next"
        >
          Next <i class="fas fa-chevron-right"></i>
        </button>

        <button
          type="submit"
          v-if="activeTab === formTabs.length - 1"
          class="btn-submit"
        >
          <i class="fas fa-paper-plane"></i> Submit Proposal
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapState, mapWritableState } from "pinia";
import { store } from "../store";

export default {
  computed: {
    ...mapWritableState(store, ['currentProposal', 'activeTab']),
    ...mapState(store, ['budgetSubtotal', 'budgetTotal', 'formattedProposalId']),
    
    formTabs() {
      return [
        { label: "Basic Info", icon: "fas fa-info-circle" },
        { label: "Cover Letter", icon: "fas fa-file-alt" },
        { label: "Objectives", icon: "fas fa-bullseye" },
        { label: "Timeline", icon: "fas fa-project-diagram" },
        { label: "Budget", icon: "fas fa-dollar-sign" },
        { label: "Milestones", icon: "fas fa-flag-checkered" },
        { label: "Terms", icon: "fas fa-file-contract" },
      ];
    },
  },

  methods: {
    ...mapActions(store, [
      'updateField',
      'setActiveTab', 
      'addObjective',
      'removeObjective',
      'addTimelinePhase',
      'removeTimelinePhase',
      'addBudgetItem',
      'removeBudgetItem',
      'addMilestone',
      'removeMilestone',
      'create',
      'initializeForm'
    ]),

    // Navigation methods
    nextTab() {
      if (this.activeTab < this.formTabs.length - 1) {
        this.setActiveTab(this.activeTab + 1);
      }
    },

    prevTab() {
      if (this.activeTab > 0) {
        this.setActiveTab(this.activeTab - 1);
      }
    },

    // Form submission
    submitProposal() {
      // Generate proposal ID if not provided
      if (!this.currentProposal.proposal_id) {
        this.currentProposal.proposal_id = "PROP-" + Date.now();
      }

      // Set default signature date if not provided
      if (!this.currentProposal.signature.date) {
        this.currentProposal.signature.date = new Date().toISOString().split("T")[0];
      }

      console.log("Submitting proposal:", this.currentProposal);
      this.create(this.currentProposal);
    },
  },

  mounted() {
    this.initializeForm();
  },
};
</script>

<style scoped>
/* CSS Variables */
:root {
  --primary-blue: #2563eb;
  --secondary-blue: #1e40af;
  --accent-blue: #3b82f6;
  --light-blue: #dbeafe;
  --dark-blue: #1e3a8a;

  --primary-gray: #1f2937;
  --secondary-gray: #374151;
  --light-gray: #f9fafb;
  --border-gray: #e5e7eb;

  --success-green: #10b981;
  --warning-orange: #f59e0b;
  --error-red: #ef4444;

  --white: #ffffff;
  --black: #000000;

  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1),
    0 4px 6px -4px rgb(0 0 0 / 0.1);

  --border-radius: 8px;
  --border-radius-lg: 12px;

  --font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.proposal-form {
  background: var(--white);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  font-family: var(--font-family);
  max-width: 100%;
}

/* Form Header */
.form-header {
  background: linear-gradient(
    135deg,
    var(--primary-blue),
    var(--secondary-blue)
  );
  color: var(--white);
  padding: 2rem;
}

.form-header h2 {
  margin: 0 0 1.5rem 0;
  font-size: 1.875rem;
  font-weight: 700;
}

/* Tabs */
.form-tabs {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.tab-btn {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: var(--white);
  padding: 0.75rem 1rem;
  border-radius: var(--border-radius);
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
}

.tab-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-1px);
}

.tab-btn.active {
  background: var(--white);
  color: var(--primary-blue);
  border-color: var(--white);
}

/* Form Content */
.proposal-form-content {
  padding: 2rem;
}

.form-section {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form-section h3 {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 0 0 2rem 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--primary-gray);
  padding-bottom: 1rem;
  border-bottom: 2px solid var(--light-blue);
}

.form-section h3 i {
  color: var(--primary-blue);
}

/* Form Layout */
.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: var(--primary-gray);
  font-size: 0.875rem;
}

/* Form Inputs */
.form-group input,
.form-group select,
.form-group textarea {
  padding: 0.75rem;
  border: 2px solid var(--border-gray);
  border-radius: var(--border-radius);
  font-size: 0.875rem;
  transition: all 0.3s ease;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary-blue);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
}

/* Dynamic Sections */
.objectives-container,
.timeline-container,
.budget-container,
.milestones-container {
  border: 2px dashed var(--border-gray);
  border-radius: var(--border-radius-lg);
  padding: 1.5rem;
  margin: 1rem 0;
}

.objective-item,
.timeline-item-form,
.budget-item,
.milestone-item {
  background: var(--light-gray);
  border: 1px solid var(--border-gray);
  border-radius: var(--border-radius);
  padding: 1.5rem;
  margin-bottom: 1rem;
  position: relative;
}

.objective-header,
.timeline-header,
.budget-header,
.milestone-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.objective-title-input,
.phase-title-input,
.budget-desc-input,
.milestone-title-input {
  flex: 1;
  font-weight: 600;
  font-size: 1rem;
  padding: 0.5rem;
  border: 1px solid var(--border-gray);
  border-radius: var(--border-radius);
}

/* Buttons */
.btn-add {
  background: linear-gradient(135deg, var(--success-green), #059669);
  color: var(--white);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: var(--border-radius);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  transition: all 0.3s ease;
  margin-top: 1rem;
}

.btn-add:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.btn-remove {
  background: var(--error-red);
  color: var(--white);
  border: none;
  padding: 0.5rem;
  border-radius: var(--border-radius);
  cursor: pointer;
  transition: all 0.3s ease;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-remove:hover {
  background: #dc2626;
  transform: scale(1.05);
}

/* Budget Summary */
.budget-summary {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 2px solid var(--border-gray);
  background: var(--white);
  border-radius: var(--border-radius);
  padding: 1.5rem;
}

.total-budget {
  background: linear-gradient(135deg, var(--success-green), #059669);
  color: var(--white);
  font-weight: 700;
  font-size: 1.125rem;
}

/* Signature Section */
.signature-section {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 2px solid var(--border-gray);
}

.signature-section h4 {
  margin: 0 0 1rem 0;
  color: var(--primary-gray);
  font-size: 1.125rem;
}

/* Navigation */
.form-navigation {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 2px solid var(--border-gray);
}

.btn-nav {
  background: var(--secondary-gray);
  color: var(--white);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: var(--border-radius);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-nav:hover:not(:disabled) {
  background: var(--primary-gray);
  transform: translateY(-2px);
}

.btn-nav:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-submit {
  background: linear-gradient(
    135deg,
    var(--primary-blue),
    var(--secondary-blue)
  );
  color: var(--white);
  border: none;
  padding: 1rem 2rem;
  border-radius: var(--border-radius);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 700;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

/* Responsive Design */
@media (max-width: 768px) {
  .form-tabs {
    grid-template-columns: repeat(2, 1fr);
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .proposal-form-content {
    padding: 1rem;
  }

  .form-header {
    padding: 1.5rem 1rem;
  }

  .form-navigation {
    flex-direction: column;
    gap: 1rem;
  }
}

@media (max-width: 480px) {
  .tab-btn {
    padding: 0.5rem 0.75rem;
    font-size: 0.75rem;
  }

  .form-section h3 {
    font-size: 1.25rem;
  }

  .objective-header,
  .timeline-header,
  .budget-header,
  .milestone-header {
    flex-direction: column;
    align-items: stretch;
  }
}

/* Utility Classes */
.text-muted {
  color: var(--secondary-gray);
}

.text-success {
  color: var(--success-green);
}

.text-warning {
  color: var(--warning-orange);
}

.text-error {
  color: var(--error-red);
}

/* Animation for dynamic items */
.objective-item,
.timeline-item-form,
.budget-item,
.milestone-item {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>
