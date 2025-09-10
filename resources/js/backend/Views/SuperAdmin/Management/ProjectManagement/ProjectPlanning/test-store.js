// Test file to verify store functionality
import { store } from "./store/index.js";

// Test the store initialization
const proposalStore = store();

// Test basic functionality
console.log("Store initialized successfully");
console.log("Initial proposal title:", proposalStore.currentProposal.title);
console.log("Initial budget total:", proposalStore.budgetTotal);

// Test updating a field
proposalStore.updateField("title", "Test Proposal");
console.log("Updated proposal title:", proposalStore.currentProposal.title);

// Test adding an objective
proposalStore.addObjective();
console.log(
  "Number of objectives:",
  proposalStore.currentProposal.objectives.length
);

export default {
  message: "Store integration test completed successfully!",
};
