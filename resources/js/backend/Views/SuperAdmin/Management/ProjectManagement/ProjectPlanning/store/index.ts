import { defineStore } from "pinia";

interface ProposalFormData {
  // Basic Information
  title: string;
  proposal_id: string;
  project_category: string;
  priority: string;

  // Client Information
  client: {
    name: string;
    position: string;
    company: string;
    email: string;
  };

  // Content Sections
  cover_letter: string;
  executive_summary: string;
  key_features: string;
  project_overview: string;

  // Timeline
  estimated_duration: string;
  start_date: string;
  timeline: Array<{
    title: string;
    duration: string;
    start_date: string;
    description: string;
  }>;

  // Objectives
  objectives: Array<{
    title: string;
    description: string;
    icon: string;
    metric: string;
  }>;

  // Budget
  budget_items: Array<{
    description: string;
    quantity: number;
    unit_price: number;
  }>;
  tax_rate: number;

  // Milestones
  milestones: Array<{
    title: string;
    due_date: string;
    payment_amount: number;
    status: string;
    description: string;
  }>;

  // Terms & Agreement
  terms_conditions: string;
  payment_terms: string;
  validity_period: number;
  acceptance_deadline: string;

  // Signature
  signature: {
    name: string;
    title: string;
    company: string;
    date: string;
  };

  // Calculated fields
  total_budget?: number;
}

export const store = defineStore("project-proposal", {
  state: () => ({
    proposals: [] as ProposalFormData[],
    currentProposal: {
      // Basic Information
      title: "",
      proposal_id: "",
      project_category: "",
      priority: "",

      // Client Information
      client: {
        name: "",
        position: "",
        company: "",
        email: "",
      },

      // Content Sections
      cover_letter: "",
      executive_summary: "",
      key_features: "",
      project_overview: "",

      // Timeline
      estimated_duration: "",
      start_date: "",
      timeline: [
        {
          title: "Project Planning",
          duration: "1 week",
          start_date: "",
          description: "Initial project setup and planning phase",
        },
      ],

      // Objectives
      objectives: [
        {
          title: "Primary Objective",
          description: "",
          icon: "fas fa-target",
          metric: "",
        },
      ],

      // Budget
      budget_items: [
        {
          description: "Development",
          quantity: 1,
          unit_price: 0,
        },
      ],
      tax_rate: 0,

      // Milestones
      milestones: [
        {
          title: "Project Kickoff",
          due_date: "",
          payment_amount: 0,
          status: "pending",
          description: "",
        },
      ],

      // Terms & Agreement
      terms_conditions: "",
      payment_terms: "",
      validity_period: 30,
      acceptance_deadline: "",

      // Signature
      signature: {
        name: "",
        title: "",
        company: "",
        date: "",
      },
    } as ProposalFormData,
    activeTab: 0,
  }),

  getters: {
    budgetSubtotal: (state) => {
      return state.currentProposal.budget_items.reduce((total, item) => {
        return total + (item.quantity || 0) * (item.unit_price || 0);
      }, 0);
    },

    budgetTotal: (state) => {
      const subtotal = state.currentProposal.budget_items.reduce(
        (total, item) => {
          return total + (item.quantity || 0) * (item.unit_price || 0);
        },
        0
      );
      const tax = subtotal * ((state.currentProposal.tax_rate || 0) / 100);
      return subtotal + tax;
    },

    formattedProposalId: (state) => {
      if (state.currentProposal.proposal_id) {
        return state.currentProposal.proposal_id;
      }
      const timestamp = new Date().getTime().toString().slice(-6);
      return `PROP-${timestamp}`;
    },

    proposalPreviewData: (state) => {
      return {
        ...state.currentProposal,
        total_budget:
          state.currentProposal.budget_items.reduce((total, item) => {
            return total + (item.quantity || 0) * (item.unit_price || 0);
          }, 0) *
          (1 + (state.currentProposal.tax_rate || 0) / 100),
      };
    },
  },

  actions: {
    // Update form field
    updateField(field: string, value: any) {
      const keys = field.split(".");
      let target = this.currentProposal as any;

      for (let i = 0; i < keys.length - 1; i++) {
        if (!target[keys[i]]) {
          target[keys[i]] = {};
        }
        target = target[keys[i]];
      }

      target[keys[keys.length - 1]] = value;
    },

    // Set entire form data
    setFormData(formData: Partial<ProposalFormData>) {
      this.currentProposal = { ...this.currentProposal, ...formData };
    },

    // Initialize form with default values
    initializeForm() {
      const today = new Date().toISOString().split("T")[0];
      this.currentProposal.start_date = today;
      this.currentProposal.signature.date = today;

      // Set acceptance deadline to 30 days from now
      const deadline = new Date();
      deadline.setDate(deadline.getDate() + 30);
      this.currentProposal.acceptance_deadline = deadline
        .toISOString()
        .split("T")[0];

      // Generate proposal ID if not provided
      if (!this.currentProposal.proposal_id) {
        const timestamp = new Date().getTime().toString().slice(-6);
        this.currentProposal.proposal_id = `PROP-${timestamp}`;
      }
    },

    // Set active tab
    setActiveTab(tabIndex: number) {
      this.activeTab = tabIndex;
    },

    // Add objective
    addObjective() {
      this.currentProposal.objectives.push({
        title: "",
        description: "",
        icon: "fas fa-target",
        metric: "",
      });
    },

    // Remove objective
    removeObjective(index: number) {
      if (this.currentProposal.objectives.length > 1) {
        this.currentProposal.objectives.splice(index, 1);
      }
    },

    // Add timeline phase
    addTimelinePhase() {
      this.currentProposal.timeline.push({
        title: "",
        duration: "",
        start_date: "",
        description: "",
      });
    },

    // Remove timeline phase
    removeTimelinePhase(index: number) {
      if (this.currentProposal.timeline.length > 1) {
        this.currentProposal.timeline.splice(index, 1);
      }
    },

    // Add budget item
    addBudgetItem() {
      this.currentProposal.budget_items.push({
        description: "",
        quantity: 1,
        unit_price: 0,
      });
    },

    // Remove budget item
    removeBudgetItem(index: number) {
      if (this.currentProposal.budget_items.length > 1) {
        this.currentProposal.budget_items.splice(index, 1);
      }
    },

    // Add milestone
    addMilestone() {
      this.currentProposal.milestones.push({
        title: "",
        due_date: "",
        payment_amount: 0,
        status: "pending",
        description: "",
      });
    },

    // Remove milestone
    removeMilestone(index: number) {
      if (this.currentProposal.milestones.length > 1) {
        this.currentProposal.milestones.splice(index, 1);
      }
    },

    // Create/Save proposal
    create(proposalData: ProposalFormData) {
      // Calculate total budget
      proposalData.total_budget = this.budgetTotal;

      // Add to proposals array
      this.proposals.push({ ...proposalData });

      console.log("Proposal created:", proposalData);
      return proposalData;
    },

    // Reset form
    resetForm() {
      this.currentProposal = {
        title: "",
        proposal_id: "",
        project_category: "",
        priority: "",
        client: {
          name: "",
          position: "",
          company: "",
          email: "",
        },
        cover_letter: "",
        executive_summary: "",
        key_features: "",
        project_overview: "",
        estimated_duration: "",
        start_date: "",
        timeline: [
          {
            title: "Project Planning",
            duration: "1 week",
            start_date: "",
            description: "Initial project setup and planning phase",
          },
        ],
        objectives: [
          {
            title: "Primary Objective",
            description: "",
            icon: "fas fa-target",
            metric: "",
          },
        ],
        budget_items: [
          {
            description: "Development",
            quantity: 1,
            unit_price: 0,
          },
        ],
        tax_rate: 0,
        milestones: [
          {
            title: "Project Kickoff",
            due_date: "",
            payment_amount: 0,
            status: "pending",
            description: "",
          },
        ],
        terms_conditions: "",
        payment_terms: "",
        validity_period: 30,
        acceptance_deadline: "",
        signature: {
          name: "",
          title: "",
          company: "",
          date: "",
        },
      };
      this.activeTab = 0;
      this.initializeForm();
    },
  },
});
