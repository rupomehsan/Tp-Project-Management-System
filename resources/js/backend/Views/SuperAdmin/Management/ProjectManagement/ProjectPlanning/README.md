# Project Proposal State Management

This project implements a reactive state management system for project proposals using Pinia store. Changes made in the ProposalForm component are automatically reflected in the ProposalPreview component in real-time.

## Features Implemented

### 1. Centralized State Management

- **Store Location**: `store/index.ts`
- **State**: All proposal data is stored in `currentProposal` object
- **Reactivity**: Changes in the form instantly update the preview

### 2. Form to Preview Synchronization

- **Form Component**: Uses `mapWritableState` to bind inputs directly to store
- **Preview Component**: Uses `mapState` to read current proposal data
- **Real-time Updates**: Any form field change immediately reflects in preview

### 3. Key Store Features

#### State Properties

- `currentProposal` - Main proposal data object
- `activeTab` - Current form tab index
- `proposals` - Array of saved proposals

#### Computed Properties

- `budgetSubtotal` - Calculated subtotal from budget items
- `budgetTotal` - Total including tax
- `formattedProposalId` - Auto-generated proposal ID
- `proposalPreviewData` - Complete proposal data for preview

#### Actions

- `updateField(field, value)` - Update any nested field
- `setActiveTab(index)` - Change active form tab
- `addObjective()` - Add new objective
- `addBudgetItem()` - Add new budget item
- `addTimelinePhase()` - Add new timeline phase
- `addMilestone()` - Add new milestone
- `initializeForm()` - Initialize with default values
- `create(proposalData)` - Save proposal

## Usage Examples

### Basic Form Field Updates

```javascript
// In ProposalForm.vue - these happen automatically with v-model
<input v-model="currentProposal.title" />
<input v-model="currentProposal.client.name" />
<textarea v-model="currentProposal.cover_letter" />
```

### Preview Display

```javascript
// In ProposalPreview.vue - these update automatically
{
  {
    currentProposal.title;
  }
}
{
  {
    currentProposal.client.name;
  }
}
{
  {
    budgetTotal;
  }
}
```

### Programmatic Updates

```javascript
// Update nested properties
this.updateField("client.email", "new@email.com");

// Add dynamic items
this.addObjective();
this.addBudgetItem();

// Switch tabs
this.setActiveTab(2);
```

## Component Structure

```
ProjectProposal.vue (Parent)
├── ProposalForm.vue (Left Side - Form Input)
│   ├── Uses mapWritableState for two-way binding
│   ├── Uses store actions for dynamic operations
│   └── All changes immediately saved to store
└── ProposalPreview.vue (Right Side - Live Preview)
    ├── Uses mapState for read-only access
    ├── Computed properties for calculated values
    └── Automatically updates when store changes
```

## Benefits

1. **Real-time Synchronization**: Form changes instantly appear in preview
2. **Type Safety**: TypeScript interfaces prevent data structure errors
3. **Centralized Logic**: All proposal logic in one store
4. **Easy Extension**: Simple to add new fields or features
5. **Performance**: Efficient reactivity with minimal re-renders

## Adding New Fields

To add a new field to the proposal:

1. Add to TypeScript interface in `store/index.ts`
2. Add to default state in store
3. Add form input in `ProposalForm.vue` with `v-model="currentProposal.newField"`
4. Add display in `ProposalPreview.vue` with `{{ currentProposal.newField }}`

The new field will automatically be included in the reactive system!
