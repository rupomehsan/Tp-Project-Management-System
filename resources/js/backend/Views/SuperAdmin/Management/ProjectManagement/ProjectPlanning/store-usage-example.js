// Example of how the Pinia store works for Project Proposal state management

import { store } from "./store/index";

// Initialize the store
const proposalStore = store();

// Example 1: Initialize the form with default data
proposalStore.initializeForm();

// Example 2: Update basic information
proposalStore.updateField("title", "My New Project Proposal");
proposalStore.updateField("client.name", "John Doe");
proposalStore.updateField("client.company", "ABC Corporation");

// Example 3: Add objectives
proposalStore.addObjective();
proposalStore.currentProposal.objectives[1].title = "Improve Efficiency";
proposalStore.currentProposal.objectives[1].description =
  "Streamline processes by 50%";

// Example 4: Add budget items
proposalStore.addBudgetItem();
proposalStore.currentProposal.budget_items[1].description =
  "Custom Development";
proposalStore.currentProposal.budget_items[1].quantity = 1;
proposalStore.currentProposal.budget_items[1].unit_price = 5000;

// Example 5: Access computed values
console.log("Budget Total:", proposalStore.budgetTotal);
console.log("Formatted Proposal ID:", proposalStore.formattedProposalId);

// Example 6: Switch tabs
proposalStore.setActiveTab(2); // Switch to objectives tab

// Example 7: Submit proposal
proposalStore.create(proposalStore.currentProposal);

/**
 * How the reactive state management works:
 *
 * 1. ProposalForm.vue uses mapWritableState to bind form inputs directly to store state
 * 2. Any changes in the form automatically update the store's currentProposal
 * 3. ProposalPreview.vue uses mapState to read the current proposal data
 * 4. Changes in the form are immediately reflected in the preview
 *
 * Key Features:
 * - Real-time synchronization between form and preview
 * - Centralized state management
 * - Computed properties for calculations (budget total, etc.)
 * - Actions for complex operations (add/remove items, validation)
 * - Type safety with TypeScript interfaces
 */
