import { defineStore } from "pinia";

export const projectProposalStore = defineStore("project-proposal", {
  state: () => ({
    template: "one",
    currentProposal: {
      // Proposal Details
      proposal_title: "",
      prepare_for_name: "",
      prepare_for_designation: "",
      prepare_for_company_name: "",
      prepare_for_address: "",
      prepare_for_city: "",
      prepare_for_phone: "",
      prepare_for_mobile: "",
      prepare_by_name: "",
      prepare_by_designation: "",
      prepare_by_company_name: "",
      prepare_by_address: "",
      prepare_by_city: "",
      prepare_by_phone: "",
      prepare_by_email: "",
      prepare_by_website: "",
      // Cover Letter & Overview
      cover_letter: "",
      executive_summary: "",
      technology_stack: "",
      project_overview: "",
      // Dynamic Sections
      timeline: [],
      payment_invoice: [],
      terms_conditions: [],
    },
  }),
  getters: {
    getProposal: (state) => state.currentProposal,
    // Calculate total from all payment invoice items
    totalAmount: (state) => {
      let total = 0;
      state.currentProposal.payment_invoice.forEach((invoice) => {
        if (invoice.rows && Array.isArray(invoice.rows)) {
          invoice.rows.forEach((row) => {
            const amount = parseFloat(row.total) || 0;
            total += amount;
          });
        }
      });
      return total;
    },
    // Convert number to words using existing function
    totalAmountInWords: (state) => {
      // Calculate total directly to avoid getter dependency issues
      let total = 0;
      state.currentProposal.payment_invoice.forEach((invoice) => {
        if (invoice.rows && Array.isArray(invoice.rows)) {
          invoice.rows.forEach((row) => {
            const amount = parseFloat(row.total) || 0;
            total += amount;
          });
        }
      });

      if (total <= 0) return "Zero Taka Only";

      // Use the English convert function instead of convertAmount (which returns Bengali)
      if (typeof window !== "undefined" && window.convert) {
        const amountString = total.toString();
        const parts = amountString.split(".");
        const taka = parts[0];
        const paisa = parts[1] || "00";

        const takaWords = window.convert(taka);
        const paisaWords = paisa !== "00" ? window.convert(paisa) : "";

        let result = takaWords + " Taka";
        if (paisaWords) {
          result += " and" + paisaWords + " Paisa";
        }
        result += " Only";

        return result.trim();
      }
      // Fallback if function not available
      return `${total} Taka Only`;
    },
  },
  actions: {
    setTemplate(template) {
      this.template = template;
      console.log("Template set to:", template);
    },
    setField(key, value) {
      this.currentProposal[key] = value;
      this.saveToLocalStorage();
    },
    addTimelineItem(item) {
      this.currentProposal.timeline.push(item);
      this.saveToLocalStorage();
    },
    removeTimelineItem(index) {
      this.currentProposal.timeline.splice(index, 1);
      this.saveToLocalStorage();
    },
    updateTimelineItem(index, item) {
      this.currentProposal.timeline[index] = item;
      this.saveToLocalStorage();
    },
    addPaymentInvoice(item) {
      this.currentProposal.payment_invoice.push(item);
      this.saveToLocalStorage();
    },
    removePaymentInvoice(index) {
      this.currentProposal.payment_invoice.splice(index, 1);
      this.saveToLocalStorage();
    },
    updatePaymentInvoice(index, item) {
      this.currentProposal.payment_invoice[index] = item;
      this.saveToLocalStorage();
    },
    addPaymentInvoiceRow(invoiceIndex, row) {
      this.currentProposal.payment_invoice[invoiceIndex].rows.push(row);
      this.saveToLocalStorage();
    },
    removePaymentInvoiceRow(invoiceIndex, rowIndex) {
      this.currentProposal.payment_invoice[invoiceIndex].rows.splice(
        rowIndex,
        1
      );
      this.saveToLocalStorage();
    },
    updatePaymentInvoiceRow(invoiceIndex, rowIndex, row) {
      this.currentProposal.payment_invoice[invoiceIndex].rows[rowIndex] = row;
      this.saveToLocalStorage();
    },
    addTermsCondition(item) {
      this.currentProposal.terms_conditions.push(item);
      this.saveToLocalStorage();
    },
    removeTermsCondition(index) {
      this.currentProposal.terms_conditions.splice(index, 1);
      this.saveToLocalStorage();
    },
    updateTermsCondition(index, item) {
      this.currentProposal.terms_conditions[index] = item;
      this.saveToLocalStorage();
    },
    saveToLocalStorage() {
      localStorage.setItem(
        "projectProposal",
        JSON.stringify(this.currentProposal)
      );
    },
    loadFromLocalStorage() {
      const data = localStorage.getItem("projectProposal");
      if (data) {
        try {
          const parsed = JSON.parse(data);
          this.currentProposal = Object.assign(this.currentProposal, parsed);
        } catch (e) {
          // ignore parse errors
        }
      }
    },
  },
});
// Auto-load from localStorage on store creation
