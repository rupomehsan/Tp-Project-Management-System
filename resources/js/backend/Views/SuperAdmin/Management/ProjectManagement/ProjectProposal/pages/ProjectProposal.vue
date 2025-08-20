<template>
  <div class="proposal-page-wrapper">
    <section class="header-section enhanced-header">
      <div class="container-fluid header-inner">
        <div class="header-left">
          <h5 class="text-dark">Project Proposal — Template Builder</h5>
          <p class="lead">
            Create and customize reusable proposal templates. Edit fields on the
            left and see a live preview on the right.
          </p>
          <div class="meta-row">
            <small class="muted"
              >Project: <strong>New Engagement</strong></small
            >
            <small class="muted">•</small>
            <small class="muted">Template: <strong>Standard</strong></small>
          </div>
        </div>

        <div class="header-actions">
          <div class="action-left">
            <span class="status-badge">Draft</span>
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
    <div class="row">
      <div class="col-md-4">
        <ProposalForm />
      </div>
      <div class="col-md-8 border py-3 border-radius-10">
        <ProposalPreview />
      </div>
    </div>
  </div>
</template>

<script>
import ProposalForm from "./ProposalForm.vue";
import ProposalPreview from "./ProposalPreview.vue";

export default {
  components: {
    ProposalForm,
    ProposalPreview,
  },
  methods: {
    downloadPDF() {
      // Open a print-friendly window with the preview content
      const previewEl =
        document.querySelector(".col-md-8 .proposal-container") ||
        document.querySelector(".proposal-container");
      if (!previewEl) return alert("Preview area not found.");
      const html = `
        <html>
          <head>
            <title>Proposal - Print</title>
            <meta charset="utf-8" />
            <style>
              body{font-family: Arial, Helvetica, sans-serif; margin:20px}
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
      // Let user print to PDF from browser
    },
    downloadDoc() {
      const previewEl =
        document.querySelector(".col-md-8 .proposal-container") ||
        document.querySelector(".proposal-container");
      if (!previewEl) return alert("Preview area not found.");
      const header = "\uFEFF"; // BOM for Word compatibility
      const html = `<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>${previewEl.innerHTML}</body></html>`;
      const blob = new Blob([header + html], { type: "application/msword" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = (document.title || "proposal") + ".doc";
      document.body.appendChild(a);
      a.click();
      a.remove();
      setTimeout(() => URL.revokeObjectURL(url), 1000);
    },
    printPreview() {
      // Open print window and auto-trigger print
      const previewEl =
        document.querySelector(".col-md-8 .proposal-container") ||
        document.querySelector(".proposal-container");
      if (!previewEl) return alert("Preview area not found.");
      const html = `
        <html>
          <head>
            <title>Proposal - Print</title>
            <meta charset="utf-8" />
            <style>
              @media print { .no-print { display: none !important; } }
              body{font-family: Arial, Helvetica, sans-serif; margin:20px}
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
      // Trigger print from the opener to avoid inline <script> in the string
      setTimeout(() => {
        try {
          w.print();
          w.close();
        } catch (e) {
          // printing may be blocked by popup blockers
        }
      }, 600);
    },
  },
};
</script>
<style>
.proposal-page-wrapper {
  background-color: #f8f9fa;
  padding: 20px;
  border-radius: 10px;
}
.enhanced-header {
  background: linear-gradient(90deg, #ffffff, #f1f5f9);
  border-radius: 8px;
  padding: 18px 16px;
  margin-bottom: 18px;
  box-shadow: 0 6px 18px rgba(16, 24, 40, 0.06);
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
.enhanced-header .header-left h1 {
  margin: 0 0 6px 0;
  font-size: 1.35rem;
}
.enhanced-header .lead {
  margin: 0 0 8px 0;
  color: #6b7280;
  font-size: 0.95rem;
}
.enhanced-header .meta-row {
  display: flex;
  gap: 0.6rem;
  align-items: center;
  color: #6b7280;
}
.enhanced-header .header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.enhanced-header .header-actions .action-left {
  flex: 1;
}
.enhanced-header .header-actions .action-right {
  display: flex;
  gap: 8px;
  align-items: center;
}
.enhanced-header .header-actions button {
  margin-left: 8px;
}
.status-badge {
  display: inline-block;
  padding: 4px 8px;
  background-color: #d1e7dd;
  color: #0f5132;
  border-radius: 12px;
  font-size: 0.875rem;
}
@media (max-width: 768px) {
  .enhanced-header .header-inner {
    flex-direction: column;
    align-items: flex-start;
  }
  .enhanced-header .header-actions {
    width: 100%;
    display: flex;
    gap: 8px;
    justify-content: flex-end;
  }
}
</style>
