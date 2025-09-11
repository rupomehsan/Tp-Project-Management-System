<template>
  <div class="proposal-page bg-white shadow-lg print-optimized">
    <!-- Top Section: Compact Hero Background -->
    <div class="hero-section position-relative">
      <div
        class="hero-background w-100"
        style="
          background: linear-gradient(
              135deg,
              rgba(79, 70, 229, 0.9),
              rgba(16, 185, 129, 0.8)
            ),
            url('backend/assets/images/proposal/angela.png');
          height: 45vh;
          background-size: cover;
          background-position: center;
          background-blend-mode: multiply;
        "
      >
        <!-- Hero Content -->
        <div
          class="hero-content position-absolute top-50 start-50 translate-middle text-center w-100"
        >
          <div class="container">
            <!-- Company Badge -->
            <div class="company-badge my-3">
              <span
                class="my-5 badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase fs-6"
                style="letter-spacing: 1px"
              >
                PROJECT PROPOSAL
              </span>
              <img
                height="100"
                width="100"
                class="d-block mx-auto my-3 border border-3 border-white shadow-sm"
                src="backend/assets/images/tplogo.png"
                alt=""
              />
              <h1
                class="display-4 fw-bold text-white my-5"
                style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3)"
              >
                TechPark IT
              </h1>
            </div>

            <!-- Main Title -->

            <!-- Subtitle -->
            <p class="lead text-white fs-5 mb-3" style="opacity: 0.9">
              Innovation in technology solutions for a digital future
            </p>

            <!-- Quick Stats -->
          </div>
        </div>
      </div>
    </div>

    <!-- Middle Section: Compact Project Details -->
    <div
      class="project-section w-100 text-white py-5"
      style="
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        min-height: 25vh;
      "
    >
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-10">
            <div class="project-content text-center">
              <!-- Project Title -->
              <h2 class="h3 fw-bold mb-2">Project title</h2>

              <!-- Project Description -->
              <p class="mb-3" style="opacity: 0.9">
                Professional digital transformation solutions with cutting-edge
                technology
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Section: Prepared For/By Layout -->
    <div
      class="contact-section bg-warning text-dark py-3"
      style="max-height: 30vh; overflow: hidden"
    >
      <div class="container">
        <div class="row g-4">
          <!-- Left Side: Prepared For -->
          <div class="col-md-6">
            <div
              class="prepared-for bg-white rounded p-4 h-100 border shadow-sm"
            >
              <div class="client-details">
                <h4
                  class="fw-bold text-dark mb-3 pb-2 border-bottom border-primary"
                >
                  Prepared For
                </h4>
                <div class="text-muted lh-base">
                  <h5 class="text-dark fw-semibold mb-2">Client Name</h5>
                  <p class="mb-1 fs-6">1584 Melody Lane</p>
                  <p class="mb-2 fs-6">Whitestone, NY 11357</p>
                  <p class="mb-1 fs-6">
                    <strong>Phone:</strong> (804) 440-1946
                  </p>
                  <p class="mb-0 fs-6"><strong>Mobile:</strong> 757-976-0395</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Side: Prepared By -->
          <div class="col-md-6">
            <div
              class="prepared-by bg-white rounded p-4 h-100 border shadow-sm"
            >
              <div class="company-details">
                <h4
                  class="fw-bold text-dark mb-3 pb-2 border-bottom border-success"
                >
                  Prepared By
                </h4>
                <div class="text-muted lh-base">
                  <h5 class="text-dark fw-semibold mb-2">
                    TechPark IT Solutions
                  </h5>
                  <p class="mb-1 fs-6">123 Innovation Drive</p>
                  <p class="mb-2 fs-6">Tech City, TC 12345</p>
                  <p class="mb-1 fs-6">
                    <strong>Phone:</strong> +1 (555) 123-4567
                  </p>
                  <p class="mb-1 fs-6">
                    <strong>Email:</strong> info@techpark.com
                  </p>
                  <p class="mb-0 fs-6">
                    <strong>Website:</strong> www.techpark.com
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Proposal Details Row -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "FrontPageTwo",
  mounted() {
    // Initialize professional animations
    this.initScrollAnimations();
    this.initHoverEffects();
  },
  methods: {
    initScrollAnimations() {
      // Smooth scroll animation for sections
      const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("animate-in");
          }
        });
      }, observerOptions);

      // Observe sections for animation
      const sections = document.querySelectorAll(
        ".contact-content, .project-content"
      );
      sections.forEach((section) => {
        observer.observe(section);
      });
    },

    initHoverEffects() {
      // Add interactive hover effects
      const featureItems = document.querySelectorAll(".feature-item");
      featureItems.forEach((item) => {
        item.addEventListener("mouseenter", () => {
          item.style.transform = "translateY(-5px)";
        });
        item.addEventListener("mouseleave", () => {
          item.style.transform = "translateY(0)";
        });
      });
    },
  },
};
</script>

<style scoped>
/* Print-Optimized Professional Proposal Styling */
.proposal-page {
  font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    sans-serif;
  line-height: 1.5;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.print-optimized {
  height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Hero Section - Optimized for A4 */
.hero-section {
  position: relative;
  overflow: hidden;
  flex: 0 0 45%;
}

.hero-background {
  position: relative;
  height: 100% !important;
}

.hero-content {
  z-index: 2;
}

/* Project Section - Optimized spacing */
.project-section {
  position: relative;
  overflow: hidden;
  flex: 0 0 25%;
  display: flex;
  align-items: center;
}

/* Contact Section - Fill remaining space */
.contact-section {
  position: relative;
  background: linear-gradient(135deg, #1f1e1b 0%, #252422 100%);
  flex: 1;
  display: flex;
  align-items: center;
  max-height: 30vh;
  overflow: hidden;
}

/* Card Styling */
.prepared-for,
.prepared-by,
.proposal-footer {
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.prepared-for:hover,
.prepared-by:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Icon Wrapper Styling */
.icon-wrapper {
  width: 40px;
  height: 40px;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Feature Items */
.feature-item {
  transition: all 0.3s ease;
  cursor: pointer;
}

.feature-item:hover {
  transform: translateY(-3px);
}

/* Badge Enhancements */
.badge {
  font-weight: 600;
  border: none;
  transition: all 0.3s ease;
}

/* Quick Stats */
.quick-stats {
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  padding: 0.5rem 1rem;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Professional Typography */
h1,
h2,
h3,
h4,
h5,
h6 {
  font-weight: 700;
  letter-spacing: -0.025em;
}

.lead {
  font-weight: 400;
  opacity: 0.9;
}

/* Responsive Design for Screen */
@media (max-width: 768px) and (max-height: 1024px) {
  .hero-background {
    height: 300px !important;
  }

  .display-4 {
    font-size: 1.8rem !important;
  }

  .lead.fs-5 {
    font-size: 1rem !important;
  }

  .project-section {
    padding: 1rem 0 !important;
  }

  .contact-section {
    padding: 1rem 0 !important;
  }
}

/* Print Optimization - Single Page */
@media print {
  .proposal-page {
    height: 100vh !important;
    overflow: visible !important;
    font-size: 12px !important;
    line-height: 1.3 !important;
  }

  .hero-background {
    height: 280px !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .project-section {
    padding: 1rem 0 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .contact-section {
    padding: 1rem 0 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .display-4 {
    font-size: 2rem !important;
    margin-bottom: 0.5rem !important;
  }

  .lead.fs-5 {
    font-size: 1rem !important;
    margin-bottom: 0.5rem !important;
  }

  .h3 {
    font-size: 1.5rem !important;
  }

  .prepared-for,
  .prepared-by {
    padding: 1rem !important;
    margin-bottom: 0.5rem !important;
  }

  .proposal-footer {
    padding: 0.75rem !important;
  }

  .container {
    max-width: 100% !important;
    padding: 0 1rem !important;
  }

  .row {
    margin: 0 !important;
  }

  .col-md-6,
  .col-md-3 {
    padding: 0.25rem !important;
  }

  /* Hide interactive elements for print */
  .quick-stats {
    background: rgba(255, 255, 255, 0.2) !important;
    backdrop-filter: none !important;
  }

  /* Ensure colors print correctly */
  .bg-warning {
    background-color: #fbbf24 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .text-white {
    color: #ffffff !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .bg-white {
    background-color: #ffffff !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  /* Compact spacing for print */
  .mb-4 {
    margin-bottom: 0.5rem !important;
  }

  .mb-3 {
    margin-bottom: 0.375rem !important;
  }

  .mb-2 {
    margin-bottom: 0.25rem !important;
  }

  .py-4 {
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
  }

  .p-4 {
    padding: 0.75rem !important;
  }

  .mt-4 {
    margin-top: 0.5rem !important;
  }

  /* Force page break prevention */
  .contact-section {
    page-break-inside: avoid !important;
  }

  .prepared-for,
  .prepared-by {
    page-break-inside: avoid !important;
  }

  /* Shadow removal for print */
  .shadow-lg,
  .prepared-for,
  .prepared-by,
  .proposal-footer {
    box-shadow: none !important;
    border: 1px solid #dee2e6 !important;
  }

  /* Icon adjustments for print */
  .icon-wrapper {
    width: 30px !important;
    height: 30px !important;
  }

  .fs-5 {
    font-size: 0.9rem !important;
  }

  .small {
    font-size: 0.8rem !important;
  }

  /* Badge adjustments */
  .badge {
    font-size: 0.7rem !important;
    padding: 0.25rem 0.5rem !important;
  }

  /* Feature items compact */
  .feature-item {
    margin-bottom: 0 !important;
  }

  .key-features .row {
    margin-bottom: 0 !important;
  }

  /* Company details font size */
  .client-details h4,
  .company-details h4 {
    font-size: 1.1rem !important;
  }

  .contact-info p,
  .company-contact p {
    font-size: 0.8rem !important;
    margin-bottom: 0.25rem !important;
  }
}

/* Professional Color Variables */
:root {
  --primary-blue: #4f46e5;
  --secondary-green: #10b981;
  --accent-yellow: #fbbf24;
  --text-dark: #1f2937;
  --text-muted: #6b7280;
  --bg-light: #f9fafb;
}

/* Smooth Transitions (disabled for print) */
@media screen {
  * {
    transition: all 0.3s ease;
  }
}

/* Professional spacing */
.section-spacing {
  margin-bottom: 1rem;
}

/* Contact items hover effects */
.contact-info p:hover,
.company-contact p:hover {
  color: var(--primary-blue);
  cursor: pointer;
}

/* Ensure proper text contrast for print */
.text-primary {
  color: var(--primary-blue) !important;
}

.text-success {
  color: var(--secondary-green) !important;
}

.text-warning {
  color: var(--accent-yellow) !important;
}

/* Print page size optimization */
@page {
  size: A4;
  margin: 0.3in;
}

/* Print Optimization - Single Page A4 */
@media print {
  .proposal-page {
    height: 100vh !important;
    min-height: 100vh !important;
    overflow: visible !important;
    font-size: 14px !important;
    line-height: 1.4 !important;
    display: flex !important;
    flex-direction: column !important;
  }

  .hero-section {
    flex: 0 0 40% !important;
    height: 40vh !important;
  }

  .hero-background {
    height: 100% !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .project-section {
    flex: 0 0 25% !important;
    height: 25vh !important;
    padding: 2rem 0 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    display: flex !important;
    align-items: center !important;
  }

  .contact-section {
    flex: 1 !important;
    height: 35vh !important;
    padding: 2rem 0 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    display: flex !important;
    align-items: center !important;
  }

  .prepared-for,
  .prepared-by {
    padding: 1.5rem !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
  }

  .container {
    max-width: 100% !important;
    padding: 0 1rem !important;
    height: 100% !important;
  }

  .row {
    margin: 0 !important;
    height: 100% !important;
  }

  .col-md-6 {
    padding: 0.5rem !important;
    height: 100% !important;
  }

  /* Typography adjustments for print */
  .display-4 {
    font-size: 2.5rem !important;
    margin-bottom: 1rem !important;
  }

  .lead.fs-5 {
    font-size: 1.2rem !important;
    margin-bottom: 1rem !important;
  }

  .h3 {
    font-size: 1.8rem !important;
  }

  h4 {
    font-size: 1.3rem !important;
  }

  h5 {
    font-size: 1.1rem !important;
  }

  .fs-6 {
    font-size: 1rem !important;
  }

  /* Ensure colors print correctly */
  .bg-warning {
    background-color: #fbbf24 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .text-white {
    color: #ffffff !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .bg-white {
    background-color: #ffffff !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .border-primary {
    border-color: #4f46e5 !important;
  }

  .border-success {
    border-color: #10b981 !important;
  }

  /* Remove shadows for print but keep borders */
  .shadow-sm,
  .shadow-lg {
    box-shadow: none !important;
    border: 2px solid #dee2e6 !important;
  }

  /* Force page break prevention */
  .proposal-page,
  .hero-section,
  .project-section,
  .contact-section {
    page-break-inside: avoid !important;
  }

  /* Badge adjustments */
  .badge {
    font-size: 0.9rem !important;
    padding: 0.5rem 1rem !important;
  }

  /* Spacing adjustments */
  .py-5 {
    padding-top: 1.5rem !important;
    padding-bottom: 1.5rem !important;
  }

  .p-5 {
    padding: 1.5rem !important;
  }

  .mb-4 {
    margin-bottom: 1rem !important;
  }

  .mb-3 {
    margin-bottom: 0.75rem !important;
  }

  .mb-2 {
    margin-bottom: 0.5rem !important;
  }

  .g-4 {
    gap: 1rem !important;
  }

  /* Text adjustments */
  .lh-base {
    line-height: 1.5 !important;
  }

  .client-details,
  .company-details {
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
  }
}
</style>
