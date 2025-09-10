<template>
  <div class="proposal-background">
    <header
      class="proposal-header d-flex justify-content-center align-items-center mb-5"
    >
      <div class="header-logo d-flex align-items-center gap-2">
        <img
          src="/backend/assets/images/tplogo.png"
          height="100"
          width="100"
          alt="TechPark IT Logo"
        />
        <div class="company-name text-white">Techpark IT</div>
      </div>
    </header>

    <main class="proposal-content mt-5">
      <div class="title-block">
        <div class="creative-text">PROPOSAL</div>
        <h1 class="proposal-title">Project Proposal</h1>
      </div>

      <div class="lorem-block">
        <h2>LOREM IPSUM</h2>
        <p>
          Adis lorem nonsectetur adipiscing cursus, est sed malis morbi curae.
          voluptat insum quae ipiet dolectis acculliu, ptatian torestrom
        </p>
      </div>

      <div class="contact-block">
        <div class="text-muted lh-base">
          <h5 class="text-dark fw-semibold mb-3">Prepared for</h5>
          <p>name</p>
          <p>designation</p>
          <p>Company name</p>
        </div>
        <div class="text-muted lh-base">
          <h5 class="text-dark fw-semibold mt-3">Prepared By</h5>
          <p>Md. Abu Ehsan</p>
          <p>Software Engineer</p>
          <p>Tech Park IT</p>
        </div>
      </div>
    </main>
  </div>
</template>
<script>
export default {
  name: "FrontPageOne",
};
</script>
<style scoped>
/* FrontPageOne Specific Styles Only */
.proposal-background {
  background-image: url("/backend/assets/images/proposal/bg.png");
  background-size: cover;
  background-position: top center;
  min-height: 297mm;
  position: relative;
}

.proposal-header {
  position: absolute;
  top: 20mm;
  left: 0;
  right: 0;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.header-logo {
  display: flex;
  align-items: center;
  gap: 15px;
}

.header-logo img {
  width: 100px;
  height: 100px;
  object-fit: contain;
}

.company-name {
  font-size: 1.5rem;
  font-weight: bold;
  color: #444;
  letter-spacing: 0.5px;
}

.proposal-content {
  position: absolute;
  top: 50mm;
  left: 20mm;
  color: #fff;
}

.title-block {
  margin-bottom: 20mm;
}

.creative-text {
  background-color: #7d493d;
  color: #fff;
  padding: 5px 10px;
  font-size: 0.9rem;
  font-weight: bold;
  letter-spacing: 0.1em;
  display: inline-block;
}

.proposal-title {
  font-size: 2.5rem;
  font-weight: 900;
  margin: 5px 0 0;
  letter-spacing: 0.05em;
}

.lorem-block {
  margin-bottom: 20mm;
  color: #eee;
}

.lorem-block h2 {
  font-size: 1rem;
  font-weight: bold;
  color: #fff;
  margin-top: 0;
  margin-bottom: 5px;
}

.lorem-block p {
  width: 200px;
  font-size: 0.7rem;
  line-height: 1.4;
  margin-bottom: 0;
}

.contact-block {
  margin-top: 200px;
  color: #333;
  font-size: 0.7rem;
}

.contact-block strong {
  display: block;
  margin-top: 8px;
  color: #555;
  font-weight: bold;
}

.contact-info-text {
  margin-bottom: 3px;
}

.proposal-issued strong,
.proposal-valid strong {
  margin-top: 10px;
}

.proposal-footer {
  position: absolute;
  bottom: 15mm;
  left: 20mm;
  right: 20mm;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  border-top: 1px solid #ccc;
  padding-top: 10px;
}

.footer-block {
  flex-basis: 40%;
  color: #555;
}

.footer-block h3 {
  font-size: 0.8rem;
  color: #7d493d;
  margin-top: 0;
  margin-bottom: 5px;
  font-weight: bold;
}

.footer-block p {
  font-size: 0.6rem;
  line-height: 1.4;
  margin: 0;
}

.qr-code {
  width: 50px;
  height: 50px;
}

.qr-code img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

/* Responsive Styles for FrontPageOne */
@media screen and (max-width: 768px) {
  .proposal-footer {
    flex-direction: column;
    align-items: center;
    text-align: center;
    bottom: 57px;
    left: 0%;
    right: 49%;
  }

  .footer-block {
    flex-basis: 100%;
    margin-bottom: 20px;
  }

  .qr-code {
    margin-top: 10px;
  }
}
</style>
