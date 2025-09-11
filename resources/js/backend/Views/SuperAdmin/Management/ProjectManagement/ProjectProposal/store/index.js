import { defineStore } from "pinia";

export const projectProposalStore = defineStore("project-proposal", {
  state: () => ({
    template: "one",

    currentProposal: {
      // Basic Information
      title: "",
      proposal_id: "",
      project_category: "",
      priority: "",
    },
  }),
  actions: {
    setTemplate(template) {
      this.template = template;
      console.log("Template set to:", template);
    },
  },
});
