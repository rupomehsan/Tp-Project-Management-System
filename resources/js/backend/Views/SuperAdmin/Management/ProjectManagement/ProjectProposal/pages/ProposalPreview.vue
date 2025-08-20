<template>
  <div class="proposal-container">
    <!-- Professional Header Bar -->

    <section id="proposal-cover-page-section">
      <div :class="['header-bar', { 'header-solid': headerSolid }]">
        <div class="header-content">
          <div class="company-branding">
            <img
              src="/backend/assets/images/tplogo.png"
              alt="Techpark IT Logo"
              class="company-logo-modern"
            />
            <div class="company-details">
              <h1 class="company-name-modern">Techpark IT</h1>
              <p class="company-tagline">Digital Innovation Solutions</p>
            </div>
          </div>
          <div class="proposal-meta">
            <div class="proposal-id">
              {{ currentProposal.proposal_id || "PROP-2025-001" }}
            </div>
            <div class="proposal-date">{{ currentDate }}</div>
          </div>
        </div>
      </div>
      <div class="hero-section py-5">
        <!-- Background Elements -->
        <div class="hero-background">
          <div class="background-image-overlay">
            <!-- Use project-specific background image when provided -->
            <img
              :src="heroImageSrc"
              alt="Project Background"
              class="hero-background-image"
            />
          </div>
          <!-- Allow gradient to be driven by project colors if available -->
          <div class="gradient-overlay" :style="gradientStyle"></div>
          <div class="geometric-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
          </div>
        </div>

        <div class="hero-content">
          <div class="hero-left">
            <div class="proposal-badge">
              <i class="fas fa-rocket"></i>
              <span>PROJECT PROPOSAL</span>
            </div>

            <h1 class="hero-title">
              <span class="title-highlight">{{
                currentProposal.title || "Project name"
              }}</span>
            </h1>

            <p class="hero-description">
              {{
                currentProposal.executive_summary ||
                "A comprehensive digital transformation solution designed to streamline operations, enhance student engagement, and modernize administrative workflows for educational institutions."
              }}
            </p>
          </div>
          <div class="proposal-card">
            <div class="contact-section">
              <div class="contact-group">
                <div class="contact-label">
                  <i class="fas fa-user-tie"></i>
                  Prepared For
                </div>
                <div class="contact-details">
                  <div class="contact-name">
                    {{ currentProposal.client?.name || "Name of Client" }}
                  </div>
                  <div class="contact-position">
                    {{ currentProposal.client?.position || "Designation" }}
                  </div>
                  <div class="contact-company">
                    {{ currentProposal.client?.company || "company name" }}
                  </div>
                </div>
              </div>

              <div class="divider"></div>

              <div class="contact-group">
                <div class="contact-label">
                  <i class="fas fa-user-cog"></i>
                  Prepared By
                </div>
                <div class="contact-details">
                  <div class="contact-name">
                    {{ currentProposal.signature?.name || "Md. Abu Ehsan" }}
                  </div>
                  <div class="contact-position">
                    {{
                      currentProposal.signature?.title ||
                      "Software Engineer & Project Manager"
                    }}
                  </div>
                  <div class="contact-company">
                    {{ currentProposal.signature?.company || "Tech Park IT" }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="proposal-cover-letter-section" class="content-section">
      <div class="section-wrapper">
        <div class="section-header-modern">
          <div class="section-icon">
            <i class="fas fa-envelope-open"></i>
          </div>
          <div class="section-title-group">
            <h2>Cover Letter</h2>
            <p>Our commitment to your success</p>
          </div>
        </div>

        <div class="cover-letter-modern">
          <div class="letter-content">
            <div class="greeting">
              Dear
              {{
                currentProposal.client?.name
                  ? "Mr./Ms. " + currentProposal.client.name
                  : "Sir"
              }},
            </div>

            <p class="lead-paragraph" v-if="currentProposal.cover_letter">
              {{ currentProposal.cover_letter }}
            </p>
            <p class="lead-paragraph" v-else>
              We are pleased to submit our proposal for the
              <strong>{{
                currentProposal.title || "Coaching Management System"
              }}</strong
              >, prepared by the expert team at
              {{ currentProposal.signature?.company || "Tech Park IT" }}.
            </p>

            <p>
              Understanding your institution's commitment to excellence in
              education, we have developed a comprehensive digital solution that
              covers not only the technical requirements but also addresses key
              aspects of modern educational management. This initiative is
              designed to support your digital transformation, enhance
              operational efficiency, and improve student engagement.
            </p>

            <div class="features-highlight">
              <h4>Our solution includes:</h4>
              <div class="features-grid">
                <div class="feature-item">
                  <i class="fas fa-users"></i>
                  <span>Complete student management system</span>
                </div>
                <div class="feature-item">
                  <i class="fas fa-calendar-check"></i>
                  <span>Automated attendance and scheduling</span>
                </div>
                <div class="feature-item">
                  <i class="fas fa-chart-line"></i>
                  <span>Advanced analytics and reporting</span>
                </div>
                <div class="feature-item">
                  <i class="fas fa-credit-card"></i>
                  <span>Integrated payment processing</span>
                </div>
              </div>
            </div>

            <p>
              We are confident that this solution will serve as a valuable tool
              for administration, teaching staff, and stakeholders, ensuring
              clarity and consistency across all educational initiatives.
            </p>

            <p>
              We take pride in offering continuous support beyond
              delivery—including updates, technical consulting, and system
              optimization—as part of our long-term service partnership.
            </p>

            <p class="closing-paragraph">
              We sincerely appreciate your time and consideration and look
              forward to the opportunity of working together to transform your
              educational institution.
            </p>

            <div class="signature-section">
              <div class="signature-line"></div>
              <div class="signature-details">
                <div class="signature-name">Engr. Nayeem Hossain</div>
                <div class="signature-title">
                  Head of Marketing & Assistant Director
                </div>
                <div class="signature-company">Tech Park IT</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="executive-summary-section" class="content-section">
      <div class="section-wrapper">
        <div class="section-header-modern">
          <div class="section-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          <div class="section-title-group">
            <h2>Executive Summary</h2>
            <p>Strategic overview and key insights</p>
          </div>
        </div>

        <div class="modern-content">
          <div class="summary-grid">
            <div class="summary-main">
              <p class="lead-text" v-if="currentProposal.executive_summary">
                {{ currentProposal.executive_summary }}
              </p>
              <p class="lead-text" v-else>
                In the current digital landscape, coaching centers require
                robust management systems to streamline operations and maintain
                seamless communication between students, teachers, and
                administrators.
              </p>

              <p v-if="currentProposal.project_overview">
                {{ currentProposal.project_overview }}
              </p>
              <p v-else>
                This proposal outlines a modern
                <strong>{{ currentProposal.title || "Project Name" }}</strong>
                for
                {{ currentProposal.client?.company || "Company Name" }}
                that will digitize, centralize, and automate daily activities
                across multiple departments, transforming traditional
                educational operations into a streamlined digital ecosystem.
              </p>
            </div>

            <div class="summary-highlights">
              <div
                class="highlight-card"
                v-for="(feature, index) in displayFeatures"
                :key="index"
              >
                <div class="highlight-icon">
                  <i :class="feature.icon"></i>
                </div>
                <div class="highlight-content">
                  <h4>{{ feature.title }}</h4>
                  <p>{{ feature.description }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Project Objectives Section -->
    <section id="project-objectives-section" class="content-section">
      <div class="section-wrapper">
        <div class="section-header-modern">
          <div class="section-icon">
            <i class="fas fa-bullseye"></i>
          </div>
          <div class="section-title-group">
            <h2>Project Objectives</h2>
            <p>Clear goals for measurable success</p>
          </div>
        </div>

        <div class="modern-content">
          <p
            class="lead-text"
            v-if="
              currentProposal.objectives &&
              currentProposal.objectives.length > 0
            "
          >
            The primary objectives of this project are designed to achieve
            specific goals and measurable outcomes for
            {{ currentProposal.client?.company || "your organization" }}.
          </p>
          <p class="lead-text" v-else>
            The primary objectives of this project are to modernize
            administrative workflows, improve data accuracy, and enhance the
            overall experience for students and staff through automation and
            centralized tools.
          </p>

          <div class="objectives-grid">
            <div
              class="objective-card"
              v-for="(objective, index) in displayObjectives"
              :key="index"
            >
              <div class="objective-icon">
                <i :class="objective.icon"></i>
              </div>
              <div class="objective-content">
                <h4>{{ objective.title }}</h4>
                <p>{{ objective.description }}</p>
              </div>
            </div>
          </div>

          <div
            class="success-metrics"
            v-if="
              currentProposal.objectives &&
              currentProposal.objectives.length > 0
            "
          >
            <div class="metrics-grid">
              <div
                class="metric-card"
                v-for="(objective, index) in currentProposal.objectives.slice(
                  0,
                  3
                )"
                :key="index"
              >
                <div class="metric-value">
                  {{ objective.metric || index + 1 + "0%" }}
                </div>
                <div class="metric-label">{{ objective.title }}</div>
                <div class="metric-timeline">
                  {{
                    currentProposal.estimated_duration
                      ? "Within " + currentProposal.estimated_duration + " days"
                      : "Target timeline"
                  }}
                </div>
              </div>
            </div>
          </div>
          <div class="success-metrics" v-else>
            <div class="metrics-grid">
              <div class="metric-card">
                <div class="metric-value">30%</div>
                <div class="metric-label">Reduction in Admin Time</div>
                <div class="metric-timeline">Within 3 months of launch</div>
              </div>

              <div class="metric-card">
                <div class="metric-value">
                  {{ currentProposal.estimated_duration || "8-12" }}
                </div>
                <div class="metric-label">
                  {{ currentProposal.estimated_duration ? "Days" : "Weeks" }}
                  Delivery Timeline
                </div>
                <div class="metric-timeline">Core modules implementation</div>
              </div>

              <div class="metric-card">
                <div class="metric-value">95%</div>
                <div class="metric-label">User Satisfaction Target</div>
                <div class="metric-timeline">Post-implementation survey</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Project Overview section -->
    <section id="project-overview-section">
      <div class="cover-letter project-overview">
        <div class="section-header">
          <h3>Project Overview</h3>
        </div>
        <p>
          This project will deliver a modern, cloud-enabled Coaching Management
          System tailored for Probaho Coaching Center. The solution will provide
          modules for student management, attendance, scheduling, payments,
          reporting, and communication.
        </p>
        <ul>
          <li>
            <strong>Objectives:</strong> Improve administrative efficiency,
            centralize data, and enhance student engagement.
          </li>
          <li>
            <strong>Scope:</strong> Student & staff portals, scheduling,
            payments, reporting dashboards, and notification services.
          </li>
          <li>
            <strong>Timeline:</strong> Detailed timeline will be provided upon
            approval; typical delivery 8–12 weeks depending on scope.
          </li>
        </ul>
      </div>
    </section>

    <!-- Project Timeline section -->
    <section id="project-timeline-section">
      <div class="cover-letter project-overview">
        <div class="section-header">
          <h3>Project Timeline</h3>
        </div>

        <div class="timeline" aria-label="Project timeline">
          <div
            class="timeline-item"
            v-for="(phase, index) in displayTimeline"
            :key="index"
          >
            <div class="timeline-dot" aria-hidden="true"></div>
            <div class="timeline-content">
              <h4>{{ phase.title }}</h4>
              <p class="muted">
                {{ phase.duration }} — {{ phase.description }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Project Budget section -->
    <section id="project-budget-section">
      <div class="cover-letter project-overview">
        <div class="section-header">
          <h3>Project Budget</h3>
        </div>

        <p>
          Estimated cost breakdown (values are indicative and will be finalized
          after requirements):
        </p>

        <table class="budget-table" role="table">
          <thead>
            <tr>
              <th>Item</th>
              <th style="text-align: right">Estimated Cost (USD)</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(budgetItem, index) in displayBudget" :key="index">
              <td>{{ budgetItem.description }}</td>
              <td style="text-align: right">
                ${{
                  (budgetItem.quantity * budgetItem.unit_price).toLocaleString()
                }}
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="budget-total-row">
              <td><strong>Estimated Total</strong></td>
              <td style="text-align: right">
                <strong>${{ budgetTotal?.toLocaleString() }}</strong>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </section>

    <!-- Module Submission Milestones -->
    <section id="project-milestones-section">
      <div class="cover-letter project-overview">
        <div class="section-header">
          <h3>Module Submission Milestones</h3>
        </div>

        <p>
          Below are the planned submission milestones for each major module.
          Each milestone will be delivered for review and sign-off before
          proceeding to the next.
        </p>

        <table class="milestones-table" role="table">
          <thead>
            <tr>
              <th>Module</th>
              <th>Milestone</th>
              <th>Date</th>
              <th>Deliverables</th>
              <th style="text-align: right">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(milestone, index) in displayMilestones" :key="index">
              <td>{{ milestone.title }}</td>
              <td>M{{ index + 1 }}</td>
              <td>
                {{
                  milestone.due_date ? formatDate(milestone.due_date) : "TBD"
                }}
              </td>
              <td>{{ milestone.description || "Milestone deliverables" }}</td>
              <td style="text-align: right">
                {{ milestone.status || "Pending" }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Module Payment Invoices -->
    <section id="project-invoices-section">
      <div class="cover-letter project-overview">
        <div class="section-header">
          <h3>Module Payment Invoices</h3>
        </div>

        <p>
          Invoices are generated per milestone. The table below shows the
          planned invoice amounts tied to each milestone and their current
          status. Amounts are indicative and will be finalized upon approval.
        </p>

        <table class="invoices-table" role="table">
          <thead>
            <tr>
              <th>Module</th>
              <th>Milestone</th>
              <th>Date</th>
              <th style="text-align: right">Invoice Amount (USD)</th>
              <th>Status</th>
              <th style="text-align: right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Student Management</td>
              <td>M1</td>
              <td>2025-08-25</td>
              <td style="text-align: right">$1,600</td>
              <td>Issued</td>
              <td style="text-align: right" class="invoice-actions">
                <button class="btn-outline">Download</button>
                <button class="btn-primary">Mark Paid</button>
              </td>
            </tr>
            <tr>
              <td>Attendance & Scheduling</td>
              <td>M2</td>
              <td>2025-09-01</td>
              <td style="text-align: right">$2,400</td>
              <td>Pending</td>
              <td style="text-align: right" class="invoice-actions">
                <button class="btn-outline">Download</button>
                <button class="btn-primary">Pay Now</button>
              </td>
            </tr>
            <tr>
              <td>Payments & Billing</td>
              <td>M3</td>
              <td>2025-09-15</td>
              <td style="text-align: right">$6,400</td>
              <td>Pending</td>
              <td style="text-align: right" class="invoice-actions">
                <button class="btn-outline">Download</button>
                <button class="btn-primary">Pay Now</button>
              </td>
            </tr>
            <tr>
              <td>Reporting Dashboard</td>
              <td>M4</td>
              <td>2025-10-13</td>
              <td style="text-align: right">$2,400</td>
              <td>Pending</td>
              <td style="text-align: right" class="invoice-actions">
                <button class="btn-outline">Download</button>
                <button class="btn-primary">Pay Now</button>
              </td>
            </tr>
            <tr>
              <td>Communication & Notifications</td>
              <td>M5</td>
              <td>2025-10-27</td>
              <td style="text-align: right">$1,600</td>
              <td>Pending</td>
              <td style="text-align: right" class="invoice-actions">
                <button class="btn-outline">Download</button>
                <button class="btn-primary">Pay Now</button>
              </td>
            </tr>
            <tr>
              <td>Admin & Settings</td>
              <td>M6</td>
              <td>2025-11-03</td>
              <td style="text-align: right">$1,600</td>
              <td>Pending</td>
              <td style="text-align: right" class="invoice-actions">
                <button class="btn-outline">Download</button>
                <button class="btn-primary">Pay Now</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Terms & Conditions -->
    <section id="terms-section">
      <div class="cover-letter project-overview terms">
        <div class="section-header">
          <h3>Terms & Conditions</h3>
        </div>

        <div v-if="currentProposal.terms_conditions">
          <div v-html="currentProposal.terms_conditions"></div>
        </div>
        <ol v-else>
          <li>
            <strong>Agreement:</strong> This proposal along with any accepted
            change orders constitutes the agreement between the client and
            {{ currentProposal.signature?.company || "Tech Park IT" }} for the
            described scope.
          </li>
          <li>
            <strong>Payment:</strong>
            {{
              currentProposal.payment_terms ||
              "Invoices are payable within 15 days of issue. Late payments may incur interest or pause work until resolved."
            }}
          </li>
          <li>
            <strong>Scope Changes:</strong> Additional features or scope changes
            will be quoted and scheduled separately as change orders.
          </li>
          <li>
            <strong>Delivery & Acceptance:</strong> Each milestone will be
            submitted for review. Delivery is considered accepted when sign-off
            is provided or no issues are reported within 7 days.
          </li>
          <li>
            <strong>Confidentiality:</strong> Both parties agree to keep
            confidential information secure and not disclose to third parties
            without consent.
          </li>
          <li>
            <strong>Warranty & Support:</strong> We provide a 30-day warranty
            for defect fixes after delivery and offer optional ongoing support
            packages.
          </li>
          <li>
            <strong>Termination:</strong> Either party may terminate with 14
            days written notice; fees for work completed and non-recoverable
            costs will apply.
          </li>
        </ol>

        <p class="muted-note">
          By approving this proposal you acknowledge and accept these terms. For
          any questions or bespoke terms, please contact the project lead.
        </p>
      </div>
    </section>

    <!-- Agreement & Signatures -->
    <section id="agreement-section">
      <div class="cover-letter project-overview agreement">
        <div class="section-header">
          <h3>Agreement</h3>
        </div>

        <p>
          If your company agrees with the presented proposal then we will be
          happy to proceed with the project. Please sign below and provide the
          date.
        </p>

        <div class="signature-grid">
          <div class="signature-block">
            <div class="sig-line">…………………………………….</div>
            <div class="sig-name">Engr. Nayeem Hossain</div>
            <div class="sig-title">
              Head of Marketing & Assistant Director, Tech Park IT
            </div>
            <div class="sig-date">Signature & Date</div>
          </div>
        </div>
        <div class="signature-grid">
          <div class="signature-block">
            <div class="sig-line">……………………………………………..</div>
            <div class="sig-name">[Client Representative]</div>
            <div class="sig-title">Position / Company</div>
            <div class="sig-date">Signature & Date</div>
          </div>
        </div>

        <p class="muted-note">
          We are delighted to inform you that we agree with your proposal and
          look forward to implementing this project in partnership with your
          company.
        </p>
      </div>
    </section>
  </div>
</template>
<script>
import { mapActions, mapWritableState, mapState } from "pinia";
import { store } from "../store";

export default {
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
    ...mapState(store, [
      "currentProposal",
      "proposalPreviewData",
      "budgetTotal",
    ]),

    displayFeatures() {
      if (this.currentProposal.key_features) {
        // If key_features is a string, split by newlines
        const features =
          typeof this.currentProposal.key_features === "string"
            ? this.currentProposal.key_features
                .split("\n")
                .filter((f) => f.trim())
            : this.currentProposal.key_features;

        return features.slice(0, 3).map((feature, index) => {
          const icons = ["fas fa-rocket", "fas fa-cogs", "fas fa-users"];
          return {
            icon: icons[index] || "fas fa-check",
            text: feature.trim(),
          };
        });
      }

      // Default features
      return [
        {
          icon: "fas fa-rocket",
          title: "Digital Transformation",
          description: "Complete modernization of educational workflows",
        },
        {
          icon: "fas fa-cogs",
          title: "Process Automation",
          description: "Reduce manual work by 70% with smart automation",
        },
        {
          icon: "fas fa-users",
          title: "Enhanced Experience",
          description: "Improved engagement for students and staff",
        },
      ];
    },

    displayObjectives() {
      if (
        this.currentProposal.objectives &&
        this.currentProposal.objectives.length > 0
      ) {
        return this.currentProposal.objectives;
      }

      // Default objectives
      return [
        {
          icon: "fas fa-database",
          title: "Centralize Student Data",
          description:
            "Provide a single source of truth for student records, attendance and performance tracking with real-time updates.",
        },
        {
          icon: "fas fa-calendar-alt",
          title: "Automate Scheduling & Attendance",
          description:
            "Reduce manual work and errors with calendar syncing and automated attendance capture systems.",
        },
        {
          icon: "fas fa-credit-card",
          title: "Simplify Payments & Billing",
          description:
            "Streamline invoicing and online payment processing to improve cash flow and reduce administrative overhead.",
        },
        {
          icon: "fas fa-chart-bar",
          title: "Enable Reporting & Insights",
          description:
            "Provide comprehensive dashboards and exportable reports for data-driven management decisions.",
        },
        {
          icon: "fas fa-comments",
          title: "Improve Communication",
          description:
            "Reliable notifications (email/SMS/in-app) to keep all stakeholders informed and engaged.",
        },
        {
          icon: "fas fa-shield-alt",
          title: "Ensure Data Security",
          description:
            "Implement robust security measures to protect sensitive student and institutional data.",
        },
      ];
    },

    displayTimeline() {
      if (
        this.currentProposal.timeline &&
        this.currentProposal.timeline.length > 0
      ) {
        return this.currentProposal.timeline;
      }

      // Default timeline
      return [
        {
          title: "Discovery",
          duration: "1 week",
          description: "requirements, kickoff & research",
        },
        {
          title: "Design",
          duration: "2 weeks",
          description: "wireframes, UI/UX & review",
        },
        {
          title: "Development",
          duration: "4–6 weeks",
          description: "implementation & integrations",
        },
        {
          title: "Testing",
          duration: "1–2 weeks",
          description: "QA, bug fixes & acceptance",
        },
        {
          title: "Deployment",
          duration: "1 week",
          description: "launch & monitoring",
        },
        {
          title: "Support",
          duration: "Ongoing",
          description: "maintenance & updates",
        },
      ];
    },

    displayBudget() {
      if (
        this.currentProposal.budget_items &&
        this.currentProposal.budget_items.length > 0
      ) {
        return this.currentProposal.budget_items;
      }

      // Default budget items
      return [
        {
          description: "Discovery & Requirements",
          quantity: 1,
          unit_price: 1200,
        },
        { description: "Design (UI/UX)", quantity: 1, unit_price: 2400 },
        { description: "Development", quantity: 1, unit_price: 9000 },
        { description: "Integrations & API", quantity: 1, unit_price: 1800 },
        { description: "Testing & QA", quantity: 1, unit_price: 1200 },
        {
          description: "Deployment & Support (first month)",
          quantity: 1,
          unit_price: 1400,
        },
      ];
    },

    displayMilestones() {
      if (
        this.currentProposal.milestones &&
        this.currentProposal.milestones.length > 0
      ) {
        return this.currentProposal.milestones;
      }

      // Default milestones
      return [
        {
          title: "Student Management",
          due_date: "2025-08-25",
          description: "CRUD, enrollment workflow, profile import/export",
          status: "Pending",
        },
        {
          title: "Attendance & Scheduling",
          due_date: "2025-09-01",
          description: "Attendance tracking, timetable, calendar sync",
          status: "Pending",
        },
        {
          title: "Payments & Billing",
          due_date: "2025-09-15",
          description: "Invoicing, payment gateway integration, receipts",
          status: "Pending",
        },
        {
          title: "Reporting Dashboard",
          due_date: "2025-10-13",
          description: "Analytics panels, exportable reports, filters",
          status: "Pending",
        },
        {
          title: "Communication & Notifications",
          due_date: "2025-10-27",
          description: "Email/SMS triggers, in-app messaging, templates",
          status: "Pending",
        },
        {
          title: "Admin & Settings",
          due_date: "2025-11-03",
          description: "Roles, permissions, system configuration",
          status: "Pending",
        },
      ];
    },

    // Hero background image source: prefer proposal-specific image
    heroImageSrc() {
      const defaultUrl =
        "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80";
      return (
        (this.currentProposal && this.currentProposal.background_image) ||
        defaultUrl
      );
    },

    // Gradient overlay style — uses project colors when provided
    gradientStyle() {
      const primary =
        (this.currentProposal && this.currentProposal.primary_color) ||
        "rgba(37, 99, 235, 0.85)";
      const secondary =
        (this.currentProposal && this.currentProposal.secondary_color) ||
        "rgba(59, 130, 246, 0.75)";
      const tertiary =
        (this.currentProposal && this.currentProposal.tertiary_color) ||
        "rgba(147, 197, 253, 0.65)";

      return {
        background: `linear-gradient(135deg, ${primary} 0%, ${secondary} 30%, ${tertiary} 70%, ${primary} 100%)`,
      };
    },
  },

  methods: {
    toggleCover() {
      this.showCover = !this.showCover;
    },

    formatDate(dateString) {
      if (!dateString) return "TBD";
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },
  },

  mounted() {
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
  },
};
</script>
<style>
/* Modern CSS Variables */
:root {
  --primary-blue: #2563eb;
  --secondary-blue: #1e40af;
  --accent-blue: #3b82f6;
  --light-blue: #dbeafe;
  --dark-blue: #1e3a8a;

  --primary-gray: #1f2937;
  --secondary-gray: #374151;
  --light-gray: #f9fafb;
  --border-gray: #e5e7eb;

  --success-green: #10b981;
  --warning-orange: #f59e0b;
  --error-red: #ef4444;

  --white: #ffffff;
  --black: #000000;

  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1),
    0 4px 6px -4px rgb(0 0 0 / 0.1);
  --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1),
    0 8px 10px -6px rgb(0 0 0 / 0.1);

  --border-radius: 8px;
  --border-radius-lg: 12px;
  --border-radius-xl: 16px;

  --font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

/* Reset & Base Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.proposal-container {
  font-family: var(--font-family);
  line-height: 1.6;
  color: var(--primary-gray);
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  /* allow full page scrolling; hero will take 100vh */
  max-height: none;
  overflow: visible;
  position: relative;
  margin-bottom: 50px;
  border-radius: 10px;
}

/* Professional Header Bar */
.header-bar {
  backdrop-filter: blur(10px);
  position: sticky; /* keep header visible while scrolling */
  top: 0;
  z-index: 100;
  /* expose header height as CSS variable so hero can compensate */
  --header-height: 72px;
  box-shadow: var(--shadow-sm);
}

/* Header overlay variant when above hero: keep a semi-opaque background for readability */
.header-bar:not(.header-solid) {
  background: rgba(255, 255, 255, 0.85);
  color: var(--primary-gray);
  backdrop-filter: blur(8px);
}

/* When scrolled past hero, header becomes solid */
.header-bar.header-solid {
  background: rgba(255, 255, 255, 0.98);
  color: var(--primary-gray);
  box-shadow: var(--shadow-sm);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0.75rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.company-branding {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.company-logo-modern {
  width: 48px;
  height: 48px;
  border-radius: var(--border-radius);
  object-fit: contain;
}

.company-name-modern {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-blue);
  margin: 0;
}

.company-tagline {
  font-size: 0.875rem;
  color: var(--secondary-gray);
  margin: 0;
}

.proposal-meta {
  text-align: right;
}

.proposal-id {
  font-weight: 600;
  color: var(--primary-gray);
}

.proposal-date {
  font-size: 0.875rem;
  color: var(--secondary-gray);
}

/* Hero Section */
.hero-section {
  position: relative;
  height: calc(
    100vh - var(--header-height)
  ); /* fill viewport minus sticky header */
  /* make room for the sticky header so content doesn't sit behind it */
  padding-top: 0;
  display: flex;
  align-items: center;
  overflow: hidden;
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1;
}

.background-image-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1;
}

.hero-background-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  opacity: 0.3;
}

.gradient-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 2;
  background: linear-gradient(
    135deg,
    rgba(37, 99, 235, 0.85) 0%,
    rgba(59, 130, 246, 0.75) 30%,
    rgba(147, 197, 253, 0.65) 70%,
    rgba(37, 99, 235, 0.8) 100%
  );
}

.geometric-shapes {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 3;
}

.shape {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  animation: float 6s ease-in-out infinite;
}

.shape-1 {
  width: 300px;
  height: 300px;
  top: 10%;
  right: 10%;
  animation-delay: 0s;
}

.shape-2 {
  width: 200px;
  height: 200px;
  bottom: 20%;
  left: 5%;
  animation-delay: 2s;
}

.shape-3 {
  width: 150px;
  height: 150px;
  top: 50%;
  left: 50%;
  animation-delay: 4s;
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-20px) rotate(180deg);
  }
}

.hero-content {
  position: relative;
  z-index: 10;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
}

.hero-left {
  color: white;
}

.proposal-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.2);
  padding: 0.5rem 1rem;
  border-radius: 50px;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 2rem;
  backdrop-filter: blur(10px);
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 1.5rem;
}

.title-highlight {
  background: linear-gradient(45deg, #fbbf24, #f59e0b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-description {
  font-size: 1.125rem;
  line-height: 1.7;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.hero-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  margin-top: 2rem;
}

.stat-item {
  text-align: center;
  background: rgba(255, 255, 255, 0.1);
  padding: 1.5rem 1rem;
  border-radius: var(--border-radius-lg);
  backdrop-filter: blur(10px);
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  display: block;
}

.stat-label {
  font-size: 0.875rem;
  opacity: 0.8;
  margin-top: 0.5rem;
}

/* Proposal Card */
.proposal-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: var(--border-radius-xl);
  padding: 2rem;
  box-shadow: var(--shadow-xl);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.card-header h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--primary-gray);
  margin: 0;
}

.status-badge {
  padding: 0.5rem 1rem;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.status-active {
  background: var(--light-blue);
  color: var(--primary-blue);
}

.contact-section {
  margin-bottom: 2rem;
}

.contact-group {
  padding: 1.5rem 0;
}

.contact-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--secondary-gray);
  margin-bottom: 1rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.contact-label i {
  color: var(--primary-blue);
}

.contact-name {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--primary-gray);
  margin-bottom: 0.25rem;
}

.contact-position {
  font-size: 0.875rem;
  color: var(--secondary-gray);
  margin-bottom: 0.125rem;
}

.contact-company {
  font-size: 0.875rem;
  color: var(--primary-blue);
  font-weight: 500;
}

.divider {
  height: 1px;
  background: var(--border-gray);
  margin: 1rem 0;
}

.card-footer {
  display: flex;
  gap: 1rem;
}

.btn-primary-modern {
  flex: 1;
  background: linear-gradient(
    135deg,
    var(--primary-blue),
    var(--secondary-blue)
  );
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: var(--border-radius);
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary-modern:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.btn-secondary-modern {
  flex: 1;
  background: white;
  color: var(--primary-blue);
  border: 2px solid var(--primary-blue);
  padding: 0.75rem 1rem;
  border-radius: var(--border-radius);
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-secondary-modern:hover {
  background: var(--primary-blue);
  color: white;
  transform: translateY(-2px);
}

/* Content Sections */
.content-section {
  background: white;
  margin: 2rem 0;
}

.section-wrapper {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.section-header-modern {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 2rem 0;
  border-bottom: 1px solid var(--border-gray);
  margin-bottom: 2rem;
}

.section-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(
    135deg,
    var(--primary-blue),
    var(--secondary-blue)
  );
  border-radius: var(--border-radius);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
}

.section-title-group h2 {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary-gray);
  margin: 0 0 0.25rem 0;
}

.section-title-group p {
  color: var(--secondary-gray);
  margin: 0;
}

/* Cover Letter Modern Styles */
.cover-letter-modern {
  padding: 2rem 0;
}

.letter-content {
  max-width: 800px;
}

.greeting {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--primary-gray);
  margin-bottom: 1.5rem;
}

.lead-paragraph {
  font-size: 1.125rem;
  line-height: 1.7;
  margin-bottom: 1.5rem;
  color: var(--primary-gray);
}

.features-highlight {
  background: var(--light-gray);
  padding: 2rem;
  border-radius: var(--border-radius-lg);
  margin: 2rem 0;
  border-left: 4px solid var(--primary-blue);
}

.features-highlight h4 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--primary-gray);
  margin-bottom: 1.5rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  background: white;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-sm);
}

.feature-item i {
  color: var(--primary-blue);
  font-size: 1.25rem;
  flex-shrink: 0;
}

.closing-paragraph {
  font-weight: 500;
  color: var(--primary-gray);
}

.signature-section {
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-gray);
}

.signature-line {
  width: 300px;
  height: 1px;
  background: var(--border-gray);
  margin-bottom: 1rem;
}

.signature-name {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--primary-gray);
}

.signature-title {
  color: var(--secondary-gray);
  font-size: 0.875rem;
}

.signature-company {
  color: var(--primary-blue);
  font-weight: 500;
  font-size: 0.875rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-content {
    grid-template-columns: 1fr;
    gap: 2rem;
    text-align: center;
  }

  .hero-title {
    font-size: 2.5rem;
  }

  .hero-stats {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .card-footer {
    flex-direction: column;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .header-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 2rem;
  }

  .section-wrapper {
    padding: 0 1rem;
  }

  .proposal-card {
    padding: 1.5rem;
  }
}

/* Modern Content Styles */
.modern-content {
  padding: 2rem 0;
}

.lead-text {
  font-size: 1.125rem;
  line-height: 1.7;
  color: var(--primary-gray);
  margin-bottom: 2rem;
  font-weight: 500;
}

/* Summary Grid */
.summary-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 3rem;
  align-items: start;
}

.summary-main p {
  margin-bottom: 1.5rem;
  line-height: 1.7;
}

.summary-highlights {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.highlight-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  background: var(--light-gray);
  border-radius: var(--border-radius-lg);
  border-left: 4px solid var(--primary-blue);
}

.highlight-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(
    135deg,
    var(--primary-blue),
    var(--secondary-blue)
  );
  border-radius: var(--border-radius);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  flex-shrink: 0;
}

.highlight-content h4 {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: var(--primary-gray);
}

.highlight-content p {
  margin: 0;
  font-size: 0.875rem;
  color: var(--secondary-gray);
}

/* Objectives Grid */
.objectives-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 1.5rem;
  margin: 2rem 0;
}

.objective-card {
  background: white;
  padding: 2rem;
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-gray);
  transition: all 0.3s ease;
}

.objective-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary-blue);
}

.objective-icon {
  width: 56px;
  height: 56px;
  background: linear-gradient(
    135deg,
    var(--primary-blue),
    var(--secondary-blue)
  );
  border-radius: var(--border-radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
}

.objective-content h4 {
  margin: 0 0 1rem 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--primary-gray);
}

.objective-content p {
  margin: 0;
  line-height: 1.6;
  color: var(--secondary-gray);
}

/* Success Metrics */
.success-metrics {
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-gray);
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.metric-card {
  text-align: center;
  background: linear-gradient(
    135deg,
    var(--primary-blue),
    var(--secondary-blue)
  );
  color: white;
  padding: 2rem;
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-md);
}

.metric-value {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.metric-label {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.metric-timeline {
  font-size: 0.875rem;
  opacity: 0.9;
}

/* Responsive adjustments for modern components */
@media (max-width: 768px) {
  .summary-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .objectives-grid {
    grid-template-columns: 1fr;
  }

  .metrics-grid {
    grid-template-columns: 1fr;
  }

  .objective-card,
  .highlight-card {
    padding: 1.5rem;
  }
}

/* Legacy styles for remaining sections */
.cover-letter {
  background: rgba(255, 255, 255, 0.98);
  color: #222;
  padding: 22px 26px;
  box-shadow: 0 8px 28px rgba(2, 6, 23, 0.15);
  line-height: 1.6;
}

.cover-letter h3 {
  margin-bottom: 8px;
  color: #111;
}

.cover-letter ul {
  margin: 8px 0 16px 20px;
}

.section-header {
  width: 100%;
  background: linear-gradient(
    90deg,
    rgba(46, 163, 242, 1) 0%,
    rgba(24, 124, 200, 1) 100%
  );
  color: #fff;
  padding: 14px 18px;
  border-radius: 6px 6px 0 0;
  box-shadow: 0 6px 18px rgba(2, 6, 23, 0.08);
  margin-bottom: 12px;
}

.section-header h3 {
  color: #fff;
  font-size: 20px;
  font-weight: 600;
  margin: 0;
}

.project-overview {
  padding: 20px 24px;
}

/* Timeline styles */
.timeline {
  display: flex;
  flex-direction: column;
  gap: 18px;
  position: relative;
  padding: 12px 6px;
}

.timeline::before {
  content: "";
  position: absolute;
  left: 28px;
  top: 16px;
  bottom: 16px;
  width: 2px;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.06), rgba(0, 0, 0, 0.02));
  z-index: 0;
}

.timeline-item {
  position: relative;
  z-index: 2;
  padding: 14px 18px 14px 58px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.98);
  box-shadow: 0 8px 20px rgba(2, 6, 23, 0.06);
  display: block;
}

.timeline-dot {
  position: absolute;
  left: 18px;
  top: 22px;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: var(--primary-blue);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.28);
}

.timeline-content h4 {
  margin: 0 0 6px 0;
  font-size: 16px;
  color: #0b2540;
}

.timeline-content .muted {
  margin: 0;
  color: #4b5563;
  font-size: 13px;
}

/* Table styles */
.budget-table,
.milestones-table,
.invoices-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 12px;
  font-size: 14px;
}

.budget-table thead th,
.milestones-table thead th,
.invoices-table thead th {
  text-align: left;
  padding: 10px 12px;
  color: #0b2540;
  font-weight: 600;
  background: rgba(11, 37, 64, 0.03);
}

.budget-table tbody td,
.milestones-table tbody td,
.invoices-table tbody td {
  padding: 12px 12px;
  border-top: 1px solid rgba(11, 37, 64, 0.04);
  color: #0b2540;
}

.budget-total-row td {
  background: linear-gradient(
    90deg,
    rgba(46, 163, 242, 0.08),
    rgba(24, 124, 200, 0.06)
  );
  padding: 12px;
}

.invoice-actions button {
  margin-left: 8px;
}

.btn-primary {
  background: linear-gradient(90deg, #2ea3f2, #187cc8);
  color: #fff;
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
}

.btn-primary:hover {
  transform: translateY(-1px);
}

.btn-outline {
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: var(--white);
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.18s ease;
}

.btn-outline:hover {
  background: rgba(255, 255, 255, 0.04);
  transform: translateY(-1px);
}
</style>
