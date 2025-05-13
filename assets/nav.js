const nav = `
  <nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
      <img
        src="assets/images/logo.png"
        alt="Logo"
        width="80px"
      />
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mx-auto justify-content-center">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="index.html">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="about.html">About Us</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="allcourses.html">All Courses</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="pricing-faq.html">Pricing & FAQ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="contact.html">Contact</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
`;

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("#nav").forEach((e) => {
    e.innerHTML = nav;
  });
});
