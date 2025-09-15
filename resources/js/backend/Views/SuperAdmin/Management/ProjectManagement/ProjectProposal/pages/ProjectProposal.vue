<template>
  <div class="proposal-page-wrapper" :data-theme="currentTheme">
    <section class="header-section enhanced-header">
      <div class="container-fluid header-inner">
        <div class="header-left">
          <h5 class="header-title">Project Proposal — Template Builder</h5>

          <div class="meta-row">
            <small class="muted"
              >Project: <strong>New Engagement</strong></small
            >
            <small class="muted">•</small>
            <small class="muted">Template: <strong>Standard</strong></small>
          </div>
        </div>
        <div class="w-25">
          <select
            class="form-control form-control-square w-100 template-select"
            name=""
            id=""
            v-model="selectedTemplate"
            @change="handleTemplateChange($event.target.value)"
          >
            <option value="one">Template One</option>
            <option value="two">Template Two</option>
          </select>
        </div>

        <div class="header-actions">
          <div class="action-left">
            <span class="status-badge">Draft</span>
            <!-- Dark Mode Toggle Button -->
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary theme-toggle-btn"
              @click="toggleTheme"
              :title="
                currentTheme === 'dark'
                  ? 'Switch to Light Mode'
                  : 'Switch to Dark Mode'
              "
            >
              <i
                :class="currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon'"
              ></i>
            </button>
          </div>
          <div class="action-right">
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary"
              @click="$emit('cancel')"
            >
              <i class="fas fa-times" aria-hidden="true"></i>
            </button>
            <button
              type="button"
              class="btn btn-sm btn-secondary"
              @click="$emit('save-draft')"
            >
              <i
                class="fas fa-save"
                aria-hidden="true"
                style="margin-right: 6px"
              ></i>
              Save
            </button>
            <button
              type="button"
              class="btn btn-sm btn-info"
              @click="printPreview"
              title="Print preview"
            >
              <i class="fas fa-print" style="margin-right: 6px"></i>
              Print
            </button>
            <button
              type="button"
              class="btn btn-sm btn-primary"
              @click="downloadPDF"
              title="Open print preview (use Print -> Save as PDF)"
            >
              <i class="fas fa-file-pdf" style="margin-right: 6px"></i>
              PDF
            </button>
            <button
              type="button"
              class="btn btn-sm btn-outline-primary"
              @click="downloadDoc"
              title="Download as Word (.doc)"
            >
              <i class="fas fa-file-word" style="margin-right: 6px"></i>
              DOC
            </button>
          </div>
        </div>
      </div>
    </section>
    <section class="row align-items-start">
      <div :class="previewExpanded ? 'col-md-12' : 'col-md-4'">
        <div class="d-flex justify-content-end mb-2">
          <button
            type="button"
            class="btn btn-outline-secondary btn-sm"
            @click="togglePreview"
          >
            <i
              :class="previewExpanded ? 'fas fa-compress' : 'fas fa-expand'"
            ></i>
            {{ previewExpanded ? "Collapse Preview" : "Expand Preview" }}
          </button>
        </div>
        <ProposalForm />
      </div>
      <div class="col-md-8 preview-section">
        <ProposalPreview />
      </div>
    </section>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import ProposalForm from "./ProposalForm.vue";
import ProposalPreview from "./ProposalPreview.vue";
import { projectProposalStore } from "../store";

export default {
  components: {
    ProposalForm,
    ProposalPreview,
  },
  data() {
    return {
      currentTheme: "light",
      selectedTemplate: "one",
      previewExpanded: false,
    };
  },

  computed: {
    ...mapState(projectProposalStore, {
      currentTemplate: "template",
    }),
  },
  watch: {
    currentTemplate(newTemplate) {
      this.selectedTemplate = newTemplate;
    },
  },
  created() {
    this.detectTheme();
    // Sync the local selectedTemplate with store
    this.selectedTemplate = this.currentTemplate;
  },
  mounted() {
    // Listen for system theme changes
    if (window.matchMedia) {
      window
        .matchMedia("(prefers-color-scheme: dark)")
        .addEventListener("change", this.handleSystemThemeChange);
    }
  },
  beforeUnmount() {
    // Clean up event listener
    if (window.matchMedia) {
      window
        .matchMedia("(prefers-color-scheme: dark)")
        .removeEventListener("change", this.handleSystemThemeChange);
    }
  },
  methods: {
    ...mapActions(projectProposalStore, {
      setTemplate: "setTemplate",
    }),

    togglePreview() {
      this.previewExpanded = !this.previewExpanded;
    },

    detectTheme() {
      // Check for saved theme preference first
      const savedTheme = localStorage.getItem("proposal-theme");
      if (savedTheme) {
        this.currentTheme = savedTheme;
        return;
      }

      // Auto-detect system theme preference
      if (
        window.matchMedia &&
        window.matchMedia("(prefers-color-scheme: dark)").matches
      ) {
        this.currentTheme = "dark";
      } else {
        this.currentTheme = "light";
      }
    },

    toggleTheme() {
      this.currentTheme = this.currentTheme === "dark" ? "light" : "dark";
      localStorage.setItem("proposal-theme", this.currentTheme);
    },

    handleSystemThemeChange(e) {
      // Only auto-switch if no manual theme is set
      if (!localStorage.getItem("proposal-theme")) {
        this.currentTheme = e.matches ? "dark" : "light";
      }
    },

    handleTemplateChange(template) {
      this.selectedTemplate = template;
      this.setTemplate(template);
    },
    downloadPDF() {
      // Open a print-friendly window with the preview content only
      const previewEl = document.querySelector(".preview-section");
      if (!previewEl) return alert("Preview section not found.");

      const html = `
        <html>
          <head>
            <title>Project Proposal - PDF</title>
            <meta charset="utf-8" />
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
              /* Essential Proposal Styles */
              body {
                font-family: Arial, Helvetica, sans-serif; 
                margin: 0;
                padding: 20px;
                background: white !important;
                color: black !important;
                line-height: 1.4;
              }
              
              /* Section Title Styles */
              .section-title {
                margin: 0 0 37px 0;
                padding: 10px;
                background: #772c2c !important;
                font-size: 1.5rem;
                font-weight: 700;
                color: #ffffff !important;
                text-align: center;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              /* Page Wrapper Styles */
              .page-wrapper, .main-wrapper, .document-wrapper, .main-document, .terms-conditions-page {
                width: 100%;
                position: relative;
                background: white;
                margin-bottom: 20px;
                page-break-after: auto;
                min-height: auto;
                max-height: none;
                overflow: visible;
              }
              
              /* Specific page break control - Each section starts on new page except cover letter */
              #projectOverviewSection, #timelineSection, #paymentInvoiceSection, #termsConditionsSection, #agreementSection {
                page-break-before: always;
                page-break-after: auto;
                page-break-inside: avoid;
              }
              
              /* Cover page and cover letter flow together */
              #coverPageSection {
                page-break-before: auto;
                page-break-after: auto;
              }
              
              /* Cover letter flows immediately after cover page without page break */
              #coverLetterSection {
                page-break-before: auto;
                page-break-after: auto;
                page-break-inside: avoid;
              }
              
              /* FRONT PAGE ONE STYLES */
              .proposal-background {
                background-image: url("/backend/assets/images/proposal/bg.png") !important;
                background-size: 100% 100%;
                background-position: top center !important;
                min-height: 100vh !important;
                max-height: 100vh !important;
                position: relative !important;
                width: 100% !important;
                overflow: hidden !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
              .proposal-header {
                position: absolute !important;
                top: 5mm !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                display: grid !important;
                justify-content: center !important;
                align-items: center !important;
              }
              
              .header-logo {
                display: flex !important;
                align-items: center !important;
                gap: 15px !important;
              }
              
              .header-logo img {
                width: 100px !important;
                height: 100px !important;
                object-fit: contain !important;
              }
              
              .company-name {
                font-size: 1.5rem !important;
                font-weight: bold !important;
                color: #444 !important;
                letter-spacing: 0.5px !important;
              }
              
              .proposal-content {
                position: absolute !important;
                top: 50mm !important;
                left: 10mm !important;
                color: #fff !important;
              }
              
              .title-block {
                margin-bottom: 20mm !important;
              }
              
              .creative-text {
                background-color: #7d493d !important;
                color: #fff !important;
                padding: 5px 10px !important;
                font-size: 0.9rem !important;
                font-weight: bold !important;
                letter-spacing: 0.1em !important;
                display: inline-block !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
              .proposal-title {
                font-size: 2.5rem !important;
                font-weight: 900 !important;
                margin: 5px 0 0 !important;
                letter-spacing: 0.05em !important;
                color: #fff !important;
              }
              
              .lorem-block {
                margin-bottom: 20mm !important;
                color: #eee !important;
              }
              
              .lorem-block h2 {
                font-size: 1rem !important;
                font-weight: bold !important;
                color: #fff !important;
                margin-top: 0 !important;
                margin-bottom: 5px !important;
              }
              
              .lorem-block p {
                width: 200px !important;
                font-size: 0.7rem !important;
                line-height: 1.4 !important;
                margin-bottom: 0 !important;
                color: #eee !important;
              }
              
              .contact-block {
                margin-top: 200px !important;
                color: #333 !important;
                font-size: 0.7rem !important;
              }
              
              .contact-block .text-muted {
                color: #555 !important;
              }
              
              .contact-block .text-dark {
                color: #333 !important;
              }
              
              .contact-block .fw-semibold {
                font-weight: 600 !important;
              }
              
              /* FRONT PAGE TWO STYLES */
              .proposal-page {
                background: white !important;
                box-shadow: 0 0 10px rgba(0,0,0,0.1) !important;
                min-height: 100vh !important;
                max-height: 100vh !important;
                position: relative !important;
                overflow: hidden !important;
              }
              
              .hero-section {
                position: relative !important;
              }
              
              .hero-background {
                background: linear-gradient(135deg, rgba(79, 70, 229, 0.9), rgba(16, 185, 129, 0.8)), url('/backend/assets/images/proposal/angela.png') !important;
                height: 35vh !important;
                background-size: cover !important;
                background-position: center !important;
                background-blend-mode: multiply !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
              .hero-content {
                position: absolute !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                text-align: center !important;
                width: 100% !important;
              }
              
              .company-badge {
                margin: 0.75rem 0 !important;
              }
              
              .badge {
                display: inline-block !important;
                padding: 0.375rem 0.75rem !important;
                font-size: 0.75rem !important;
                font-weight: 700 !important;
                line-height: 1 !important;
                color: #212529 !important;
                text-align: center !important;
                white-space: nowrap !important;
                vertical-align: baseline !important;
                border-radius: 0.375rem !important;
              }
              
              .bg-warning {
                background-color: #1a1a1a !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
              .text-dark {
                color: #212529 !important;
              }
              
              .text-white {
                color: #fff !important;
              }
              
              .display-4 {
                font-size: calc(1.475rem + 2.7vw) !important;
                font-weight: 300 !important;
                line-height: 1.2 !important;
              }
              
              .fw-bold {
                font-weight: 700 !important;
              }
              
              .lead {
                font-size: 1.25rem !important;
                font-weight: 300 !important;
              }
              
              .project-section {
                width: 100% !important;
                color: white !important;
                padding: 1.5rem 0 !important;
                background: linear-gradient(135deg, #1f2937 0%, #374151 100%) !important;
                min-height: 20vh !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
              .contact-section {
                background-color: #1a1a1a !important;
                color: #212529 !important;
                padding: 1.5rem 0 !important;
                min-height: 40vh !important;
                max-height: 40vh !important;
                overflow: hidden !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
              .prepared-for, .prepared-by {
                background-color: white !important;
                border-radius: 0.375rem !important;
                padding: 1.5rem !important;
                height: 100% !important;
                border: 1px solid #dee2e6 !important;
                box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
                text-align: left !important;
              }
              
              .prepared-for *, .prepared-by * {
                text-align: left !important;
              }
              
              .prepared-for h4, .prepared-by h4,
              .prepared-for h5, .prepared-by h5 {
                text-align: left !important;
                margin-bottom: 1rem !important;
                font-weight: 600 !important;
              }
              
              .prepared-for p, .prepared-by p, 
              .prepared-for div, .prepared-by div {
                text-align: left !important;
                margin-bottom: 0.5rem !important;
              }
              
              .client-details, .company-details {
                text-align: left !important;
              }
              
              .client-details *, .company-details * {
                text-align: left !important;
              }
              
              .border-bottom {
                border-bottom: 1px solid #dee2e6 !important;
              }
              
              .border-primary {
                border-color: #0d6efd !important;
              }
              
              .border-success {
                border-color: #198754 !important;
              }
              
              /* Bootstrap Grid Classes */
              .container {
                width: 100% !important;
                padding-right: var(--bs-gutter-x, 0.75rem) !important;
                padding-left: var(--bs-gutter-x, 0.75rem) !important;
                margin-right: auto !important;
                margin-left: auto !important;
              }
              
              .row {
                display: flex !important;
                flex-wrap: wrap !important;
                margin-top: calc(-1 * var(--bs-gutter-y, 0)) !important;
                margin-right: calc(-0.5 * var(--bs-gutter-x, 1.5rem)) !important;
                margin-left: calc(-0.5 * var(--bs-gutter-x, 1.5rem)) !important;
              }
              
              .col-md-6 {
                flex: 0 0 auto !important;
                width: 50% !important;
                padding-right: calc(var(--bs-gutter-x, 1.5rem) * 0.5) !important;
                padding-left: calc(var(--bs-gutter-x, 1.5rem) * 0.5) !important;
              }
              
              .col-lg-10 {
                flex: 0 0 auto !important;
                width: 83.33333333% !important;
                padding-right: calc(var(--bs-gutter-x, 1.5rem) * 0.5) !important;
                padding-left: calc(var(--bs-gutter-x, 1.5rem) * 0.5) !important;
              }
              
              /* Utility Classes */
              .d-flex { display: flex !important; }
              .d-block { display: block !important; }
              .justify-content-center { justify-content: center !important; }
              .align-items-center { align-items: center !important; }
              .text-center { text-align: center !important; }
              .mx-auto { margin-left: auto !important; margin-right: auto !important; }
              .my-3 { margin-top: 1rem !important; margin-bottom: 1rem !important; }
              .my-5 { margin-top: 3rem !important; margin-bottom: 3rem !important; }
              .mb-2 { margin-bottom: 0.5rem !important; }
              .mb-3 { margin-bottom: 1rem !important; }
              .mb-4 { margin-bottom: 1.5rem !important; }
              .mb-0 { margin-bottom: 0 !important; }
              .py-5 { padding-top: 3rem !important; padding-bottom: 3rem !important; }
              .px-3 { padding-left: 1rem !important; padding-right: 1rem !important; }
              .py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
              .p-5 { padding: 3rem !important; }
              .pb-2 { padding-bottom: 0.5rem !important; }
              .fs-6 { font-size: 1rem !important; }
              .fs-5 { font-size: 1.25rem !important; }
              .h-100 { height: 100% !important; }
              .w-100 { width: 100% !important; }
              .gap-2 { gap: 0.5rem !important; }
              .gap-4 { gap: 1.5rem !important; }
              .g-4 > * { padding: 0.75rem !important; }
              .lh-base { line-height: 1.5 !important; }
              .rounded { border-radius: 0.375rem !important; }
              .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; }
              .border { border: 1px solid #dee2e6 !important; }
              .border-3 { border-width: 3px !important; }
              .border-white { border-color: white !important; }
              .text-uppercase { text-transform: uppercase !important; }
              }
              
              .hero-section {
                position: relative;
              }
              
              .hero-background {
                background: linear-gradient(135deg, rgba(79, 70, 229, 0.9), rgba(16, 185, 129, 0.8)), url('/backend/assets/images/proposal/angela.png') !important;
                height: 45vh !important;
                background-size: cover !important;
                background-position: center !important;
                background-blend-mode: multiply !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .creative-text {
                background-color: #7d493d !important;
                color: #fff !important;
                padding: 5px 10px;
                font-size: 0.9rem;
                font-weight: bold;
                letter-spacing: 0.1em;
                display: inline-block;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              /* Payment Invoice & Budget Styles */
              .budget-category {
                margin-bottom: 20px;
                border-bottom: 1px solid #eee;
                padding-bottom: 15px;
              }
              
              .category-content {
                display: flex;
                border: 1px solid #eee;
                border-top: none;
              }
              
              .item-list-container {
                flex: 1;
                padding: 15px 20px;
                background: #fff;
              }
              
              .item-pricing {
                width: 300px;
                background: #f9f9f9;
                border-left: 1px solid #eee;
              }
              
              .pricing-row {
                display: flex;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid #eee;
              }
              
              .pricing-row:last-child {
                border-bottom: none;
              }
              
              .price-col {
                font-size: 0.85rem;
                color: #555;
                text-align: center;
                width: 100px;
                font-weight: 500;
              }
              
              .total-section {
                margin-top: 20px;
                padding: 15px 20px;
                border-top: 2px solid #772c2c;
                background: #f5f5f5;
                display: flex;
                justify-content: space-between;
                align-items: center;
              }
              
              .total-label {
                font-size: 1.3rem;
                font-weight: 700;
                color: #772c2c !important;
                flex: 1;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .total-pricing {
                display: flex;
                align-items: center;
                width: 300px;
              }
              
              .total-col {
                font-size: 1.1rem;
                font-weight: 700;
                color: #772c2c !important;
                text-align: center;
                width: 100px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              /* Category Header Styles */
              .category-header {
                background-color: #5c2121 !important;
                padding: 0px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                color: #fff !important;
                margin-bottom: 0;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .category-title {
                font-size: 1.2rem;
                font-weight: 700;
                color: #fff !important;
                margin: 0;
                flex: 1;
              }
              
              .category-table-header {
                display: flex;
                align-items: center;
                gap: 0;
                width: 300px;
              }
              
              .header-col {
                font-size: 0.9rem;
                font-weight: 600;
                text-align: center;
                width: 100px;
                color: #fff !important;
              }
              
              /* Bootstrap Colors */
              .bg-warning {
                background-color: #1a1a1a !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .text-white {
                color: white !important;
              }
              
              .text-dark {
                color: #333 !important;
              }
              
              /* All other styles from printPreview... */
              .background-visual {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url("/images/bg2.PNG") !important;
                background-size: cover;
                background-position: center;
                z-index: -1;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .timeline-section {
                position: relative;
                padding-left: 20px;
              }
              
              .timeline-section::before {
                content: "";
                position: absolute;
                top: 0;
                left: -2px;
                width: 4px;
                height: 100%;
                background-color: #772c2c !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .timeline-marker {
                position: absolute;
                left: -7px;
                width: 14px;
                height: 14px;
                background-color: #772c2c !important;
                border-radius: 50%;
                z-index: 1;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .term-number-circle {
                width: 30px;
                height: 30px;
                background-color: #9d4834 !important;
                color: #fff !important;
                border-radius: 5px;
                display: flex;
                justify-content: center;
                align-items: center;
                font-weight: bold;
                font-size: 1rem;
                flex-shrink: 0;
                margin-right: 15px;
                margin-top: 5px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .term-heading {
                font-size: 1.1rem;
                color: #772c2c !important;
                margin: 0 0 5px 0;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              /* Images */
              img {
                max-width: 100%;
                height: auto;
                display: block;
              }
              
              /* Lists */
              .item-list li:before {
                content: "•";
                color: #772c2c !important;
                position: absolute;
                left: 0;
                font-weight: bold;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              /* Print specific */
              @media print {
                body { margin: 0; }
                @page { 
                  margin: 1cm; 
                  size: A4;
                }
                * {
                  -webkit-print-color-adjust: exact !important;
                  color-adjust: exact !important;
                  print-color-adjust: exact !important;
                }
              }
            </style>
          </head>
          <body>
            ${previewEl.innerHTML}
          </body>
        </html>`;
      const w = window.open("", "_blank");
      // w.document.open();
      w.document.write(html);
      w.document.close();
      w.focus();
      // Let user print to PDF from browser
    },
    downloadDoc() {
      const previewEl = document.querySelector(".preview-section");
      if (!previewEl) return alert("Preview section not found.");

      const header = "\uFEFF"; // BOM for Word compatibility
      const html = `<!DOCTYPE html>
        <html>
          <head>
            <meta charset="utf-8">
            <style>
              /* Essential Proposal Styles for Word */
              body {
                font-family: Arial, Helvetica, sans-serif; 
                margin: 20px;
                background: white !important;
                color: black !important;
                line-height: 1.4;
              }
              
              /* Section Title Styles */
              .section-title {
                margin: 0 0 37px 0;
                padding: 10px;
                background: #772c2c !important;
                font-size: 1.5rem;
                font-weight: 700;
                color: #ffffff !important;
                text-align: center;
              }
              
              /* Timeline Styles */
              .timeline-section {
                position: relative;
                padding-left: 20px;
              }
              
              .timeline-section::before {
                content: "";
                position: absolute;
                top: 0;
                left: -2px;
                width: 4px;
                height: 100%;
                background-color: #772c2c !important;
              }
              
              .timeline-marker {
                position: absolute;
                left: -7px;
                width: 14px;
                height: 14px;
                background-color: #772c2c !important;
                border-radius: 50%;
                z-index: 1;
              }
              
              /* Category Header Styles */
              .category-header {
                background-color: #5c2121 !important;
                color: #fff !important;
                padding: 10px 20px;
              }
              
              .category-title {
                color: #fff !important;
                font-weight: 700;
              }
              
              /* Term Number Circle */
              .term-number-circle {
                width: 30px;
                height: 30px;
                background-color: #9d4834 !important;
                color: #fff !important;
                border-radius: 5px;
                display: inline-flex;
                justify-content: center;
                align-items: center;
                font-weight: bold;
                margin-right: 15px;
              }
              
              .term-heading {
                color: #772c2c !important;
                font-weight: 700;
              }
              
             
              /* Images */
              img {
                max-width: 100%;
                height: auto;
                display: block;
              }
              
              /* Lists */
              .item-list li:before {
                content: "• ";
                color: #772c2c !important;
                font-weight: bold;
              }
            </style>
          </head>
          <body>
            ${previewEl.innerHTML}
          </body>
        </html>`;
      const blob = new Blob([header + html], { type: "application/msword" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = (document.title || "project-proposal") + ".doc";
      document.body.appendChild(a);
      a.click();
      a.remove();
      setTimeout(() => URL.revokeObjectURL(url), 1000);
    },
    printPreview() {
      // Open print window and auto-trigger print for preview section only
      const previewEl = document.querySelector(".preview-section");
      if (!previewEl) return alert("Preview section not found.");

      const html = `
        <html>
          <head>
            <title>Project Proposal - Print</title>
            <meta charset="utf-8" />
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
              /* Essential Proposal Styles */
              body {
                font-family: Arial, Helvetica, sans-serif; 
                margin: 0;
                padding: 20px;
                background: white !important;
                color: black !important;
                line-height: 1.4;
              }

              .section-border {
                border: 1px solid #38242e3f !important;
              }
              
              /* Section Title Styles */
              .section-title {
                margin: 0 0 37px 0;
                padding: 10px;
                background: #772c2c !important;
                font-size: 1.5rem;
                font-weight: 700;
                color: #ffffff !important;
                text-align: center;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              /* Page Wrapper Styles */
              .page-wrapper, .main-wrapper, .document-wrapper, .main-document, .terms-conditions-page {
                width: 100%;
                position: relative;
                background: white;
                margin-bottom: 20px;
                page-break-after: auto;
                min-height: auto;
                max-height: none;
                overflow: visible;
              }
              
              /* Specific page break control - Each section starts on new page except cover letter */
              #projectOverviewSection, #timelineSection, #paymentInvoiceSection, #termsConditionsSection, #agreementSection {
                page-break-before: always;
                page-break-after: auto;
                page-break-inside: avoid;
              }
              
              /* Cover page and cover letter flow together */
              #coverPageSection {
                page-break-before: auto;
                page-break-after: auto;
              }
              
              /* Cover letter flows immediately after cover page without page break */
              #coverLetterSection {
                page-break-before: auto;
                page-break-after: auto;
                page-break-inside: avoid;
              }
              
              /* FRONT PAGE ONE STYLES */
              .proposal-background {
                background-image: url("/backend/assets/images/proposal/bg.png") !important;
                background-size: 100% 100%;
                background-position: top center !important;
                min-height: 100vh !important;
                max-height: 100vh !important;
                position: relative !important;
                width: 100% !important;
                overflow: hidden !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
             .proposal-header {
                position: absolute !important;
                top: 5mm !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                display: grid !important;
                justify-content: center !important;
                align-items: center !important;
              }
              
              .header-logo {
                display: flex !important;
                align-items: center !important;
                gap: 15px !important;
              }
              
              .header-logo img {
                width: 100px !important;
                height: 100px !important;
                object-fit: contain !important;
              }
              
              .company-name {
                font-size: 1.5rem !important;
                font-weight: bold !important;
                color: #444 !important;
                letter-spacing: 0.5px !important;
              }
              
              .proposal-content {
                position: absolute !important;
                top: 50mm !important;
                left: 10mm !important;
                color: #fff !important;
              }
              
              .title-block {
                margin-bottom: 20mm !important;
              }
              
              .creative-text {
                background-color: #7d493d !important;
                color: #fff !important;
                padding: 5px 10px !important;
                font-size: 0.9rem !important;
                font-weight: bold !important;
                letter-spacing: 0.1em !important;
                display: inline-block !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
              .proposal-title {
                font-size: 2rem !important;
                font-weight: 900 !important;
                margin: 5px 0 0 !important;
                letter-spacing: 0.05em !important;
                color: #fff !important;
              }
              
              .lorem-block {
                margin-bottom: 15mm !important;
                color: #eee !important;
              }
              
              .lorem-block h2 {
                font-size: 1rem !important;
                font-weight: bold !important;
                color: #fff !important;
                margin-top: 0 !important;
                margin-bottom: 5px !important;
              }
              
              .lorem-block p {
                width: 200px !important;
                font-size: 0.7rem !important;
                line-height: 1.4 !important;
                margin-bottom: 0 !important;
                color: #eee !important;
              }
              
              .contact-block {
                margin-top: 150px !important;
                color: #333 !important;
                font-size: 0.7rem !important;
              }
              
              .contact-block .text-muted {
                color: #555 !important;
              }
              
              .contact-block .text-dark {
                color: #333 !important;
              }
              
              .contact-block .fw-semibold {
                font-weight: 600 !important;
              }
              
              /* FRONT PAGE TWO STYLES */
              .proposal-page {
                background: white !important;
                box-shadow: 0 0 10px rgba(0,0,0,0.1) !important;
                min-height: 100vh !important;
                max-height: 100vh !important;
                position: relative !important;
                overflow: hidden !important;
              }
              
              .hero-section {
                position: relative !important;
              }
              
              .hero-background {
                background: linear-gradient(135deg, rgba(79, 70, 229, 0.9), rgba(16, 185, 129, 0.8)), url('/backend/assets/images/proposal/angela.png') !important;
                height: 35vh !important;
                background-size: cover !important;
                background-position: center !important;
                background-blend-mode: multiply !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
              .hero-content {
                position: absolute !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                text-align: center !important;
                width: 100% !important;
              }
              
              .company-badge {
                margin: 0.5rem 0 !important;
              }
              
              .badge {
                display: inline-block !important;
                padding: 0.375rem 0.75rem !important;
                font-size: 0.75rem !important;
                font-weight: 700 !important;
                line-height: 1 !important;
                color: #212529 !important;
                text-align: center !important;
                white-space: nowrap !important;
                vertical-align: baseline !important;
                border-radius: 0.375rem !important;
              }
              
              .bg-warning {
                background-color: #1a1a1a !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
              .text-dark {
                color: #212529 !important;
              }
              
              .text-white {
                color: #fff !important;
              }
              
              .display-4 {
                font-size: calc(1.475rem + 2.7vw) !important;
                font-weight: 300 !important;
                line-height: 1.2 !important;
              }
              
              .fw-bold {
                font-weight: 700 !important;
              }
              
              .lead {
                font-size: 1.25rem !important;
                font-weight: 300 !important;
              }
              
              .project-section {
                width: 100% !important;
                color: white !important;
                padding: 1.5rem 0 !important;
                background: linear-gradient(135deg, #1f2937 0%, #374151 100%) !important;
                min-height: 20vh !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
              .contact-section {
                background-color: #1a1a1a !important;
                color: #212529 !important;
                padding: 1.5rem 0 !important;
                min-height: 40vh !important;
                max-height: 40vh !important;
                overflow: hidden !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
              }
              
              .prepared-for, .prepared-by {
                background-color: white !important;
                border-radius: 0.375rem !important;
                padding: 1.5rem !important;
                height: 100% !important;
                border: 1px solid #dee2e6 !important;
                box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
                text-align: left !important;
              }
              
              .prepared-for *, .prepared-by * {
                text-align: left !important;
              }
              
              .prepared-for h4, .prepared-by h4,
              .prepared-for h5, .prepared-by h5 {
                text-align: left !important;
                margin-bottom: 1rem !important;
                font-weight: 600 !important;
              }
              
              .prepared-for p, .prepared-by p, 
              .prepared-for div, .prepared-by div {
                text-align: left !important;
                margin-bottom: 0.5rem !important;
              }
              
              .client-details, .company-details {
                text-align: left !important;
              }
              
              .client-details *, .company-details * {
                text-align: left !important;
              }
              
              .border-bottom {
                border-bottom: 1px solid #dee2e6 !important;
              }
              
              .border-primary {
                border-color: #0d6efd !important;
              }
              
              .border-success {
                border-color: #198754 !important;
              }
              
              /* Bootstrap Grid Classes */
              .container {
                width: 100% !important;
                padding-right: var(--bs-gutter-x, 0.75rem) !important;
                padding-left: var(--bs-gutter-x, 0.75rem) !important;
                margin-right: auto !important;
                margin-left: auto !important;
              }
              
              .row {
                display: flex !important;
                flex-wrap: wrap !important;
                margin-top: calc(-1 * var(--bs-gutter-y, 0)) !important;
                margin-right: calc(-0.5 * var(--bs-gutter-x, 1.5rem)) !important;
                margin-left: calc(-0.5 * var(--bs-gutter-x, 1.5rem)) !important;
              }
              
              .col-md-6 {
                flex: 0 0 auto !important;
                width: 50% !important;
                padding-right: calc(var(--bs-gutter-x, 1.5rem) * 0.5) !important;
                padding-left: calc(var(--bs-gutter-x, 1.5rem) * 0.5) !important;
              }
              
              .col-lg-10 {
                flex: 0 0 auto !important;
                width: 83.33333333% !important;
                padding-right: calc(var(--bs-gutter-x, 1.5rem) * 0.5) !important;
                padding-left: calc(var(--bs-gutter-x, 1.5rem) * 0.5) !important;
              }
              
              /* Utility Classes */
              .d-flex { display: flex !important; }
              .d-block { display: block !important; }
              .justify-content-center { justify-content: center !important; }
              .align-items-center { align-items: center !important; }
              .text-center { text-align: center !important; }
              .mx-auto { margin-left: auto !important; margin-right: auto !important; }
              .my-3 { margin-top: 1rem !important; margin-bottom: 1rem !important; }
              .my-5 { margin-top: 3rem !important; margin-bottom: 3rem !important; }
              .mb-2 { margin-bottom: 0.5rem !important; }
              .mb-3 { margin-bottom: 1rem !important; }
              .mb-4 { margin-bottom: 1.5rem !important; }
              .mb-0 { margin-bottom: 0 !important; }
              .py-5 { padding-top: 3rem !important; padding-bottom: 3rem !important; }
              .px-3 { padding-left: 1rem !important; padding-right: 1rem !important; }
              .py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
              .p-5 { padding: 3rem !important; }
              .pb-2 { padding-bottom: 0.5rem !important; }
              .fs-6 { font-size: 1rem !important; }
              .fs-5 { font-size: 1.25rem !important; }
              .h-100 { height: 100% !important; }
              .w-100 { width: 100% !important; }
              .gap-2 { gap: 0.5rem !important; }
              .gap-4 { gap: 1.5rem !important; }
              .g-4 > * { padding: 0.75rem !important; }
              .lh-base { line-height: 1.5 !important; }
              .rounded { border-radius: 0.375rem !important; }
              .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; }
              .border { border: 1px solid #dee2e6 !important; }
              .border-3 { border-width: 3px !important; }
              .border-white { border-color: white !important; }
              .text-uppercase { text-transform: uppercase !important; }
              
              /* Background Visual */
              .background-visual {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url("/images/bg2.PNG") !important;
                background-size: cover;
                background-position: center;
                z-index: -1;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              /* Header Styles */
              .page-header {
                position: absolute;
                top: 40px;
                left: 40px;
                color: white;
                width: 300px;
              }
              
              .quote-text {
                font-size: 1.2rem;
                font-style: italic;
                font-weight: 300;
                line-height: 1.4;
                color: white;
              }
              
              .welcome-heading {
                font-size: 2.2rem;
                font-weight: 700;
                color: #a34651 !important;
                margin-bottom: 10px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .company-name {
                font-weight: 700;
              }
              
              /* Timeline Styles */
              .timeline-section {
                position: relative;
                padding-left: 20px;
              }
              
              .timeline-section::before {
                content: "";
                position: absolute;
                top: 0;
                left: -2px;
                width: 4px;
                height: 100%;
                background-color: #772c2c !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .timeline-item {
                display: flex;
                margin-bottom: 25px;
                position: relative;
              }
              
              .timeline-marker {
                position: absolute;
                left: -7px;
                width: 14px;
                height: 14px;
                background-color: #772c2c !important;
                border-radius: 50%;
                z-index: 1;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .timeline-left-side {
                width: 100px;
                flex-shrink: 0;
                padding-right: 20px;
              }
              
              .timeline-right-side {
                display: flex;
                margin-left: 20px;
                position: relative;
              }
              
              .timeline-details {
                margin-left: 20px;
              }
              
              .step-title, .item-title {
                font-size: 1.2rem;
                font-weight: 700;
                color: #555;
                margin: 0;
              }
              
              .item-subtitle {
                font-size: 1rem;
                font-weight: 700;
                color: #555;
                margin: 5px 0 10px 0;
              }
              
              .step-date, .step-duration, .item-date, .item-duration {
                font-size: 0.8rem;
                color: #777;
                margin: 0;
              }
              
              .item-description {
                font-size: 0.8rem;
                line-height: 1.6;
                color: #555;
                margin-top: 10px;
              }
              
              /* Payment Invoice & Budget Styles */
              .budget-category {
                margin-bottom: 20px;
                border-bottom: 1px solid #eee;
                padding-bottom: 15px;
              }
              
              .category-content {
                display: flex;
                border: 1px solid #eee;
                border-top: none;
              }
              
              .item-list-container {
                flex: 1;
                padding: 15px 20px;
                background: #fff;
              }
              
              .item-pricing {
                width: 300px;
                background: #f9f9f9;
                border-left: 1px solid #eee;
              }
              
              .pricing-row {
                display: flex;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid #eee;
              }
              
              .pricing-row:last-child {
                border-bottom: none;
              }
              
              .price-col {
                font-size: 0.85rem;
                color: #555;
                text-align: center;
                width: 100px;
                font-weight: 500;
              }
              
              .total-section {
                margin-top: 20px;
                padding: 15px 20px;
                border-top: 2px solid #772c2c;
                background: #f5f5f5;
                display: flex;
                justify-content: space-between;
                align-items: center;
              }
              
              .total-label {
                font-size: 1.3rem;
                font-weight: 700;
                color: #772c2c !important;
                flex: 1;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .total-pricing {
                display: flex;
                align-items: center;
                width: 300px;
              }
              
              .total-col {
                font-size: 1.1rem;
                font-weight: 700;
                color: #772c2c !important;
                text-align: center;
                width: 100px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              /* Category Header Styles */
              .category-header {
                background-color: #5c2121 !important;
                padding: 0px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                color: #fff !important;
                margin-bottom: 0;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .category-title {
                font-size: 1.2rem;
                font-weight: 700;
                color: #fff !important;
                margin: 0;
                flex: 1;
              }
              
              .category-table-header {
                display: flex;
                align-items: center;
                gap: 0;
                width: 300px;
              }
              
              .header-col {
                font-size: 0.9rem;
                font-weight: 600;
                text-align: center;
                width: 100px;
                color: #fff !important;
              }
              
              /* Term Number Circle */
              .term-number-circle {
                width: 30px;
                height: 30px;
                background-color: #9d4834 !important;
                color: #fff !important;
                border-radius: 5px;
                display: flex;
                justify-content: center;
                align-items: center;
                font-weight: bold;
                font-size: 1rem;
                flex-shrink: 0;
                margin-right: 15px;
                margin-top: 5px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .term-item {
                display: flex;
                margin-bottom: 25px;
                align-items: flex-start;
              }
              
              .term-heading {
                font-size: 1.1rem;
                color: #772c2c !important;
                margin: 0 0 5px 0;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .term-description {
                font-size: 0.9rem;
                color: #555;
                line-height: 1.6;
                margin: 0;
              }
              
              /* Content Areas */
            
              
              .main-content-area {
                position: absolute;
                top: 150px;
                left: 50px;
                right: 40px;
                bottom: 40px;
                color: #333;
              }
              
              .page-header-strip {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100px;
                display: flex;
                align-items: center;
                justify-content: center;
              }
              
              .header-text {
                margin-top: 112px;
                color: #111111;
                font-size: 0.8rem;
                line-height: 1.4;
                padding: 0 40px;
              }
              
              /* Images */
              img {
                max-width: 100%;
                height: auto;
                display: block;
              }
              
              /* Lists */
              .item-list {
                list-style: none;
                padding: 0;
                margin: 0;
              }
              
              .item-list li {
                font-size: 0.9rem;
                color: #555;
                margin-bottom: 8px;
                padding-left: 15px;
                position: relative;
                line-height: 1.4;
              }
              
              .item-list li:before {
                content: "•";
                color: #772c2c !important;
                position: absolute;
                left: 0;
                font-weight: bold;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              /* Bootstrap Colors and Elements */
              .bg-warning {
                background-color: #1a1a1a !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
              }
              
              .text-white {
                color: white !important;
              }
              
              .text-dark {
                color: #333 !important;
              }
              
              .border {
                border: 1px solid #dee2e6 !important;
              }
              
              .border-3 {
                border-width: 3px !important;
              }
              
              .border-white {
                border-color: white !important;
              }
              
              .shadow-sm {
                box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
              }
              
              /* Print specific */
              @media print { 
                .no-print { display: none !important; }
                @page { 
                  margin: 1cm; 
                  size: A4;
                }
                body { margin: 0; }
                * {
                  -webkit-print-color-adjust: exact !important;
                  color-adjust: exact !important;
                  print-color-adjust: exact !important;
                }
              }
            </style>
          </head>
          <body>
            ${previewEl.innerHTML}
          </body>
        </html>`;
      const w = window.open("", "_blank");
      w.document.open();
      w.document.write(html);
      w.document.close();
      w.focus();

      setTimeout(() => {
        try {
          w.print();
          w.close();
        } catch (e) {}
      }, 200);
    },
  },
};
</script>
<style scoped>
/* CSS Custom Properties for Theme Support */
.proposal-page-wrapper {
  /* Light theme variables (default) */
  --bg-primary: #f8f9fa;
  --bg-secondary: #ffffff;
  --bg-tertiary: #f1f5f9;
  --text-primary: #212529;
  --text-secondary: #6b7280;
  --text-muted: #6c757d;
  --border-color: #dee2e6;
  --shadow-color: rgba(16, 24, 40, 0.06);
  --header-bg: linear-gradient(90deg, #ffffff, #f1f5f9);
  --status-badge-bg: #d1e7dd;
  --status-badge-text: #0f5132;
  --btn-outline-border: #6c757d;
  --btn-outline-text: #6c757d;
  --form-control-bg: #e9ecef;
  --form-control-border: #ced4da;
  --form-control-text: #495057;

  background-color: var(--bg-primary);
  color: var(--text-primary);
  padding: 20px;
  border-radius: 10px;
  transition: background-color 0.3s ease, color 0.3s ease;
  height: 90vh;
  overflow-y: auto;
}

/* Dark theme variables */
.proposal-page-wrapper[data-theme="dark"] {
  --bg-primary: #1a1a1a;
  --bg-secondary: #2d2d2d;
  --bg-tertiary: #3a3a3a;
  --text-primary: #ffffff;
  --text-secondary: #b3b3b3;
  --text-muted: #999999;
  --border-color: #404040;
  --shadow-color: rgba(0, 0, 0, 0.3);
  --header-bg: linear-gradient(90deg, #2d2d2d, #3a3a3a);
  --status-badge-bg: rgba(40, 167, 69, 0.2);
  --status-badge-text: #28a745;
  --btn-outline-border: #6c757d;
  --btn-outline-text: #b3b3b3;
  --form-control-bg: #404040;
  --form-control-border: #555555;
  --form-control-text: #ffffff;
}

.enhanced-header {
  background: var(--header-bg);
  border-radius: 8px;
  padding: 18px 16px;
  margin-bottom: 18px;
  box-shadow: 0 6px 18px var(--shadow-color);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}

.enhanced-header .header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding-left: 8px;
  padding-right: 8px;
}

.enhanced-header .header-left {
  padding-left: 4px;
}

.header-title {
  margin: 0 0 6px 0;
  font-size: 1.35rem;
  color: var(--text-primary) !important;
  font-weight: 600;
}

.enhanced-header .lead {
  margin: 0 0 8px 0;
  color: var(--text-secondary);
  font-size: 0.95rem;
}

.enhanced-header .meta-row {
  display: flex;
  gap: 0.6rem;
  align-items: center;
  color: var(--text-secondary);
}

.meta-row .muted {
  color: var(--text-muted) !important;
}

.meta-row strong {
  color: var(--text-primary);
}

.enhanced-header .header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.enhanced-header .header-actions .action-left {
  flex: 1;
  display: flex;
  gap: 10px;
  align-items: center;
}

.enhanced-header .header-actions .action-right {
  display: flex;
  gap: 8px;
  align-items: center;
}

.enhanced-header .header-actions button {
  margin-left: 8px;
  transition: all 0.3s ease;
}

.status-badge {
  display: inline-block;
  padding: 4px 8px;
  background-color: var(--status-badge-bg);
  color: var(--status-badge-text);
  border-radius: 12px;
  font-size: 0.875rem;
  border: 1px solid var(--status-badge-text);
}

/* Theme Toggle Button */
.theme-toggle-btn {
  border-color: var(--btn-outline-border) !important;
  color: var(--btn-outline-text) !important;
  background-color: transparent !important;
  transition: all 0.3s ease;
}

.theme-toggle-btn:hover {
  background-color: var(--bg-tertiary) !important;
  border-color: var(--text-primary) !important;
  color: var(--text-primary) !important;
}

/* Template Select Styling */
.template-select {
  background-color: var(--form-control-bg) !important;
  border-color: var(--form-control-border) !important;
  color: var(--form-control-text) !important;
  transition: all 0.3s ease;
}

.template-select:focus {
  background-color: var(--form-control-bg) !important;
  border-color: #007bff !important;
  color: var(--form-control-text) !important;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.template-select option {
  background-color: var(--form-control-bg);
  color: var(--form-control-text);
}

/* Preview Section Styling */
.preview-section {
  background-color: white;
  border: 1px solid var(--border-color);
  border-radius: 10px;
  padding: 1rem;
  transition: all 0.3s ease;
}

/* Button Overrides for Dark Mode */
.proposal-page-wrapper[data-theme="dark"] .btn-outline-secondary {
  border-color: var(--btn-outline-border) !important;
  color: var(--btn-outline-text) !important;
}

.proposal-page-wrapper[data-theme="dark"] .btn-outline-secondary:hover {
  background-color: var(--bg-tertiary) !important;
  border-color: var(--text-primary) !important;
  color: var(--text-primary) !important;
}

.proposal-page-wrapper[data-theme="dark"] .btn-outline-primary {
  border-color: #007bff !important;
  color: #007bff !important;
}

.proposal-page-wrapper[data-theme="dark"] .btn-outline-primary:hover {
  background-color: #007bff !important;
  color: #ffffff !important;
}

/* Row and Column Adjustments */
.proposal-page-wrapper .row {
  margin: 0;
}

.proposal-page-wrapper .col-md-4,
.proposal-page-wrapper .col-md-8 {
  transition: all 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
  .enhanced-header .header-inner {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .enhanced-header .header-actions {
    width: 100%;
    flex-direction: column;
    gap: 1rem;
  }

  .enhanced-header .header-actions .action-left,
  .enhanced-header .header-actions .action-right {
    width: 100%;
    justify-content: center;
  }

  .w-25 {
    width: 100% !important;
  }

  .header-title {
    font-size: 1.1rem;
  }
}

@media (max-width: 576px) {
  .proposal-page-wrapper {
    padding: 10px;
  }

  .enhanced-header {
    padding: 12px;
  }

  .enhanced-header .header-actions .action-right {
    flex-wrap: wrap;
    gap: 4px;
  }

  .enhanced-header .header-actions button {
    margin-left: 0;
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
  }
}

/* Smooth theme transition for all child components */
.proposal-page-wrapper * {
  transition: background-color 0.3s ease, color 0.3s ease,
    border-color 0.3s ease;
}

/* Print Media Query - Always use light theme for printing */
@media print {
  .proposal-page-wrapper {
    --bg-primary: #ffffff !important;
    --bg-secondary: #ffffff !important;
    --bg-tertiary: #f8f9fa !important;
    --text-primary: #000000 !important;
    --text-secondary: #333333 !important;
    --text-muted: #666666 !important;
    --border-color: #dee2e6 !important;
    background-color: white !important;
    color: black !important;
  }

  .theme-toggle-btn {
    display: none !important;
  }

  .enhanced-header
    .header-actions
    .action-right
    button:not(.btn-info):not(.btn-primary) {
    display: none !important;
  }
}
</style>
