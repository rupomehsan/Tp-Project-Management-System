import { defineStore } from "pinia";

export const store = defineStore("project-proposal", {
  state: () => ({
    currentProposal: {
      // Basic Information
      title: "",
      proposal_id: "",
      project_category: "",
      priority: "",
    },
  }),
});

export default store;
