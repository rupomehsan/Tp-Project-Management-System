<template>
  <div class="proposal-preview-wrapper">
    <!-- DYNAMIC FRONT PAGES - Show based on selected template -->
    <FrontPageOne v-if="template === 'one'" :proposal="proposal" />
    <FrontPageTwo v-if="template === 'two'" :proposal="proposal" />

    <!-- COVER LETTER -->
    <section
      v-if="proposal.cover_letter && proposal.cover_letter.trim()"
      class="main-wrapper"
      id="coverLetterSection"
    >
      <div>
        <h2 class="section-title">COVER LETTER</h2>
      </div>
      <main class="text-dark">
        <div class="cover-letter-text p-5" v-html="proposal.cover_letter"></div>
      </main>
    </section>

    <!-- EXECUTIVE SUMMARY -->
    <section
      v-if="proposal.executive_summary && proposal.executive_summary.trim()"
      class="main-wrapper"
      id="executiveSummarySection"
    >
      <div>
        <h2 class="section-title">EXECUTIVE SUMMARY</h2>
      </div>
      <main class="text-dark">
        <div
          class="cover-letter-text p-5"
          v-html="proposal.executive_summary"
        ></div>
      </main>
    </section>

    <!-- TECHNOLOGIES USED -->
    <section
      v-if="proposal.technology_stack && proposal.technology_stack.trim()"
      class="main-wrapper"
      id="technologiesSection"
    >
      <div>
        <h2 class="section-title">Technology Stack</h2>
      </div>
      <main class="text-dark">
        <div
          class="cover-letter-text p-5"
          v-html="proposal.technology_stack"
        ></div>
      </main>
    </section>

    <!-- PROJECT OVERVIEW -->
    <section
      v-if="proposal.project_overview && proposal.project_overview.trim()"
      class="main-wrapper"
      id="projectOverviewSection"
    >
      <div>
        <h2 class="section-title">Project Overview</h2>
      </div>
      <main class="text-dark">
        <div
          class="cover-letter-text p-5"
          v-html="proposal.project_overview"
        ></div>
      </main>
    </section>

    <!-- TIMELINE -->
    <section
      v-if="proposal.timeline && proposal.timeline.length > 0"
      class="main-wrapper"
      id="timelineSection"
    >
      <div>
        <h2 class="section-title">TIMELINE</h2>
      </div>
      <main class="main-content-area">
        <div class="timeline-section">
          <div
            v-for="(item, idx) in proposal.timeline"
            :key="idx"
            class="timeline-item text-white"
          >
            <div class="timeline-left-side">
              <h4 class="step-title">{{ item.title }}</h4>
              <p class="step-date">{{ item.date }}</p>
              <p class="step-duration">{{ item.weeks }} weeks</p>
            </div>
            <div class="timeline-right-side">
              <div class="timeline-marker"></div>
              <div class="timeline-details">
                <p class="item-subtitle">{{ item.subtitle }}</p>
                <p class="item-description">{{ item.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </section>

    <!-- PAYMENT INVOICE -->
    <section
      v-if="proposal.payment_invoice && proposal.payment_invoice.length > 0"
      class="main-wrapper"
      id="paymentInvoiceSection"
    >
      <div>
        <h2 class="section-title">Payment Invoice</h2>
      </div>
      <div class="right-section">
        <header class="document-header">
          <p class="header-text-top">
            <!-- Optionally add summary or notes here -->
          </p>
        </header>
        <section
          v-for="(invoice, iidx) in proposal.payment_invoice"
          :key="iidx"
          class="budget-category"
        >
          <div class="category-header">
            <h2 class="category-title">{{ invoice.heading }}</h2>
            <div class="category-table-header">
              <span class="header-col">Date</span>
              <span class="header-col">Total</span>
            </div>
          </div>
          <div class="category-content">
            <div class="item-list-container">
              <ul class="item-list">
                <li v-for="(row, ridx) in invoice.rows" :key="ridx">
                  {{ row.title }}
                </li>
              </ul>
            </div>
            <div class="item-pricing">
              <div
                v-for="(row, ridx) in invoice.rows"
                :key="ridx"
                class="pricing-row"
              >
                <span class="price-col">{{ row.date }}</span>
                <span class="price-col">{{ row.total }}</span>
              </div>
            </div>
          </div>
        </section>
        <section class="total-section">
          <div class="total-label text-capitalize">
            Total: ({{ totalAmountInWords }})
          </div>
          <div class="total-pricing">
            <span class="total-col">{{ formatCurrency(totalAmount) }}</span>
          </div>
        </section>
      </div>
    </section>

    <!-- TERMS & CONDITIONS -->
    <section
      v-if="proposal.terms_conditions && proposal.terms_conditions.length > 0"
      class="main-wrapper"
      id="termsConditionsSection"
    >
      <div>
        <h2 class="section-title">TERMS & CONDITIONS</h2>
      </div>
      <div class="terms-content-container">
        <ul class="terms-list">
          <li
            v-for="(term, tidx) in proposal.terms_conditions"
            :key="tidx"
            class="term-item"
          >
            <div class="term-number-circle">{{ tidx + 1 }}</div>
            <div class="term-details">
              <h2 class="term-heading">{{ term.title }}</h2>
              <p class="term-description">{{ term.description }}</p>
            </div>
          </li>
        </ul>
      </div>
    </section>

    <!-- Agreement Section -->

    <!-- Agreement Section -->
    <section class="main-wrapper" id="agreementSection">
      <div>
        <h2 class="section-title">Agreement</h2>
      </div>
      <div class="p-5 text-dark">
        <strong>
          We, TechPark IT Solutions, hereby confirm our commitment to deliver
          the project as outlined in this proposal. Upon your acceptance of the
          terms and conditions stated herein, we are prepared to commence work
          immediately and ensure timely completion within the agreed timeline
          and budget.
        </strong>
        <br /><br />
        <br /><br />
        <strong>Authorized Signature:</strong> _________________________________
        <br /><br />
        <strong>Engr. Nayeem Hossain</strong><br />
        Head of Marketing & Assistant Director<br />
        TechPark IT Solutions<br />
        <br />
        <br />
        <strong>Date:</strong> _______________________
        <br />
        <br />
        <br />
        <br />

        <strong>CLIENT AGREEMENT:</strong><br /><br />
        <strong>
          We acknowledge receipt and review of this proposal. By signing below,
          we confirm our acceptance of the project scope, timeline, budget, and
          terms & conditions as presented. We authorize TechPark IT Solutions to
          proceed with the project implementation as outlined. be glad to
          implement this project with your company's assistance.
        </strong>

        <br /><br />
        <br /><br />
        <strong>Client Signature:</strong> _________________________________
        <br /><br />
        <strong>{{ proposal.prepare_for_name || "name" }}</strong
        ><br />
        {{ proposal.prepare_for_designation || "designation" }}<br />
        {{ proposal.prepare_for_company_name || "Company name" }}<br />
        <br />
        <br />

        <strong>Date:</strong> _______________________
        <br />
      </div>
    </section>
  </div>
</template>
<script>
import { mapState } from "pinia";
import { projectProposalStore } from "../store";
import FrontPageOne from "../components/FrontPageOne.vue";
import FrontPageTwo from "../components/FrontPageTwo.vue";
// Import the number to text plugin to ensure it's loaded
import "../../../../../../plugins/number_to_text.js";

export default {
  components: {
    FrontPageOne,
    FrontPageTwo,
  },
  data: () => ({
    showCover: true,
    headerSolid: false,
    currentDate: new Date().toLocaleDateString("en-US", {
      year: "numeric",
      month: "long",
      day: "numeric",
    }),
  }),
  computed: {
    ...mapState(projectProposalStore, {
      template: "template",
      proposal: "currentProposal",
      totalAmount: "totalAmount",
      totalAmountInWords: "totalAmountInWords",
    }),
  },
  methods: {
    // Format currency with commas
    formatCurrency(amount) {
      return new Intl.NumberFormat("en-BD", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
      }).format(amount);
    },
  },
  watch: {
    // Watch for changes in the proposal data and automatically reload from localStorage
    proposal: {
      handler() {
        // This will trigger when the store data changes
        this.$nextTick(() => {
          // Force a reactive update if needed
          this.$forceUpdate();
        });
      },
      deep: true,
    },
  },
  mounted() {
    // Load proposal data from localStorage on mount
    projectProposalStore().loadFromLocalStorage();

    // Set up a listener for localStorage changes from other components
    window.addEventListener("storage", (e) => {
      if (e.key === "projectProposal") {
        projectProposalStore().loadFromLocalStorage();
      }
    });

    // Remove the aggressive interval that was causing form input conflicts
    // The store reactivity and localStorage events are sufficient for updates
    // Listen to scroll and toggle header background when hero is passed
    this._onScroll = () => {
      const headerHeight = 72; // matches --header-height
      const hero = document.querySelector(".hero-section");
      if (!hero) return;
      const threshold = hero.getBoundingClientRect().height - headerHeight - 20;
      const scrolled = window.scrollY || window.pageYOffset;
      this.headerSolid = scrolled > threshold;
    };
    window.addEventListener("scroll", this._onScroll, { passive: true });
    // Add FontAwesome CSS if not already loaded
    if (!document.querySelector('link[href*="font-awesome"]')) {
      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.href =
        "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css";
      document.head.appendChild(link);
    }
  },
  beforeUnmount() {
    if (this._onScroll) window.removeEventListener("scroll", this._onScroll);
    // Remove intervalId cleanup since we're no longer using the interval
  },
};
</script>
<style scoped>
@import "../styles/theme.css";

.proposal-preview-wrapper {
  height: 90vh;
  overflow-y: auto;
}

/* ProposalPreview enhanced dark mode support */
/* Common Shared Styles */
.section-border {
  border: 1px solid #38242e3f;
}

.section-title {
  margin: 0 0 37px 0;
  padding: 10px;
  background: #772c2c !important;
  font-size: 1.5rem;
  font-weight: 700;
  color: #ffffff;
  text-align: center;
}

.page-wrapper {
  width: 210mm;
  height: 297mm;

  position: relative;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Cover Letter Page */
.main-wrapper {
  width: 100%;
  height: 297mm;
  position: relative;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.background-visual {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: url("/images/bg2.PNG");
  background-size: cover;
  background-position: center;
  z-index: -1;
}

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
}

.welcome-heading {
  font-size: 2.2rem;
  font-weight: 700;
  color: #a34651;
  margin-bottom: 10px;
}

.company-name {
  font-weight: 700;
}

.section-group {
  margin-top: 20px;
}

.section-paragraph {
  font-size: 0.85rem;
  line-height: 1.6;
  margin-bottom: 15px;
}

.page-footer {
  position: absolute;
  bottom: 25px;
  left: 40px;
  color: #666;
  width: calc(100% - 80px);
}

.page-info {
  display: flex;
  justify-content: space-between;
  font-size: 0.8rem;
}

.page-number {
  font-weight: bold;
}

/* Timeline Page */
.document-wrapper {
  width: 100%;
  height: 297mm;

  position: relative;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  background-image: url("http://googleusercontent.com/file_content/0");
  background-size: cover;
  background-position: center;
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

.header-main-text {
  margin: 30px;
}

.main-content-area {
  position: absolute;
  top: 150px;
  left: 50px;
  right: 40px;
  bottom: 40px;
  color: #333;
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
  background-color: #772c2c;
}

.timeline-item {
  display: flex;
  margin-bottom: 25px;
  position: relative;
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

.timeline-marker {
  position: absolute;
  left: -7px;
  width: 14px;
  height: 14px;
  background-color: #772c2c;
  border-radius: 50%;
  z-index: 1;
}

.timeline-details {
  margin-left: 20px;
}

.step-title,
.item-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: rgb(0, 0, 0);
  margin: 0;
}

.item-subtitle {
  font-size: 1rem;
  font-weight: 700;
  color: rgb(0, 0, 0);
  margin: 5px 0 10px 0;
}

.step-date,
.step-duration,
.item-date,
.item-duration {
  font-size: 0.8rem;
  color: #777;
  margin: 0;
}

.item-description {
  font-size: 0.8rem;
  line-height: 1.6;
  color: rgb(0, 0, 0);
  margin-top: 10px;
}

/* Pricing Plan Page */
.main-document {
  width: 100%;
  height: 297mm;

  position: relative;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  background: #1a1a1a;
}

.right-section {
  flex: 1;
  padding: 20px;
  background: #ffffff;
}

.document-header {
  margin-bottom: 20px;
}

.header-text-top {
  font-size: 0.9rem;
  line-height: 1.6;
  color: rgb(0, 0, 0);
  margin: 0;
}

.budget-category {
  margin-bottom: 20px;
  border-bottom: 1px solid #eee;
  padding-bottom: 15px;
}

.category-header {
  background-color: #5c2121;
  padding: 0px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #fff;
  margin-bottom: 0;
}

.category-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: #fff;
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
  width: 140px;
  color: #fff;
}

.category-content {
  display: flex;
  border: 1px solid #eee;
  border-top: none;
}

.item-list-container {
  flex: 1;
  padding: 15px 20px;
  background: #ffffff;
}

.item-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.item-list li {
  font-size: 0.9rem;
  color: rgb(0, 0, 0);
  margin-bottom: 8px;
  padding-left: 15px;
  position: relative;
  line-height: 1.4;
}

.item-list li:before {
  content: "•";
  color: #772c2c;
  position: absolute;
  left: 0;
  font-weight: bold;
}

.item-pricing {
  width: 300px;
  background: #ffffff;
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
  color: rgb(0, 0, 0);
  text-align: center;
  width: 130px;
  font-weight: 500;
}

.total-section {
  margin-top: 20px;
  padding: 15px 20px;
  border-top: 2px solid #772c2c;
  background: #d1d1d1;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.total-label {
  font-size: 1.3rem;
  font-weight: 700;
  color: #772c2c;
  flex: 1;
}

.total-pricing {
  display: flex;
  align-items: center;
  width: 300px;
}

.total-col {
  font-size: 1.1rem;
  font-weight: bolder;
  color: #772c2c;
  text-align: center;
  width: 100%;
}

/* Amount in Words Section */
.amount-words-section {
  margin-top: 15px;
  padding: 15px 20px;
  background: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 5px;
}

.amount-words-label {
  font-size: 1rem;
  font-weight: 600;
  color: #772c2c;
  margin-bottom: 8px;
}

.amount-words-text {
  font-size: 0.95rem;
  color: #333;
  font-style: italic;
  line-height: 1.4;
  padding: 5px 0;
}

/* Terms & Conditions Page */
.terms-conditions-page {
  width: 100%;
  height: 297mm;

  position: relative;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  background: #fff;
  display: flex;
  flex-direction: column;
}

.terms-content-container {
  flex: 1;
  padding: 20px 40px;
}

.terms-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.term-item {
  display: flex;
  margin-bottom: 25px;
  align-items: flex-start;
}

.term-number-circle {
  width: 30px;
  height: 30px;
  background-color: #9d4834;
  color: #fff;
  border-radius: 5px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: bold;
  font-size: 1rem;
  flex-shrink: 0;
  margin-right: 15px;
  margin-top: 5px;
}

.term-details {
  flex-grow: 1;
}

.term-heading {
  font-size: 1.1rem;
  color: #772c2c;
  margin: 0 0 5px 0;
}

.term-description {
  font-size: 0.9rem;
  color: rgb(0, 0, 0);
  line-height: 1.6;
  margin: 0;
}

/* Responsive Styles */
@media screen and (max-width: 768px) {
  .page-wrapper,
  .main-wrapper,
  .document-wrapper,
  .main-document,
  .terms-conditions-page {
    width: 100%;
    height: auto;
    margin: 0;
    box-shadow: none;
    padding: 20px;
    box-sizing: border-box;
  }

  .background-visual {
    position: fixed;
    height: 100vh;
    width: 100vw;
  }

  .page-header {
    position: relative;
    top: auto;
    left: auto;
    padding: 0;
    margin-bottom: 20px;
    color: #fff;
  }

  .quote-text {
    font-size: 1.1rem;
    text-align: center;
  }

  .content-body {
    position: relative;
    bottom: auto;
    left: auto;
    width: 100%;
    padding: 0;
    box-sizing: border-box;
  }

  .welcome-heading {
    font-size: 1.8rem;
    text-align: center;
    margin-top: 0;
  }

  .page-footer {
    position: relative;
    bottom: auto;
    left: auto;
    width: 100%;
    margin-top: 30px;
    text-align: center;
  }

  .main-content-area {
    position: static;
    padding: 20px;
    left: auto;
    right: auto;
    top: auto;
    bottom: auto;
  }

  .timeline-section {
    padding-left: 0;
  }

  .timeline-section::before {
    display: none;
  }

  .timeline-marker {
    display: none;
  }

  .timeline-details {
    margin-left: 0;
  }

  .timeline-item {
    flex-direction: column;
    text-align: left;
  }

  .timeline-left-side {
    width: 100%;
    padding-right: 0;
    margin-bottom: 10px;
    text-align: left;
  }

  .timeline-right-side {
    margin-left: 0;
    flex-direction: column;
  }

  .category-content {
    grid-template-columns: 1fr;
  }

  .category-header {
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
  }

  .category-table-head {
    display: none;
  }

  .item-table td {
    text-align: left;
  }

  .total-section {
    flex-direction: column;
    align-items: flex-start;
  }

  .amount-words-section {
    margin-top: 10px;
    padding: 10px 15px;
  }

  .amount-words-label {
    font-size: 0.9rem;
  }

  .amount-words-text {
    font-size: 0.85rem;
  }

  .total-table {
    width: 100%;
  }
}

@media screen and (max-width: 480px) {
  .quote-text {
    font-size: 1rem;
  }

  .welcome-heading {
    font-size: 1.5rem;
  }

  .page-info {
    flex-direction: column;
    align-items: center;
    gap: 5px;
  }
}
</style>
