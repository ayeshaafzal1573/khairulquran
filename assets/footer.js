const footer = 
     ` <footer>
  <div class="container-fluid foot">
  <div class="row foot-row">
    <div class="col-md-3 ft-col1">
      <h4 class="f-h-4 mt-4">Plant Palace</h4>
      <hr>
      <p>Discover the joy of nurturing life and creating serene, lush environments with Plant Palace.</p>
    </div>
    <div class="col-md-3 ft-use">
      <h4 class="f-h-4 mt-4">Site Map</h4>
      <hr>
      <ul class="ful">
         <li><a href="index.html">Home</a>
                        <p style="margin-left: 30px;">|</p>
                        <span><a href="index.html#ourgallery" style="margin-left: -60px;">Our Gallery</a> 
<a href="index.html#ourgallery" style="margin-left: 30px;">Our Gallery</a></span>
                    </li>
                     <li class="has-submenu">
                        <a href="category.html">Our Products</a>
                        <div class="submenu">
                            <a href="category.html">Indoor</a>
                            <a href="category.html">Outdoor</a>
                            <a href="category.html">Flowering Shrubs</a>
                            <a href="category.html">Succulents</a>
                        </div>
                    </li>
                  <li>
                    <a href="feedback.html">Feedback</a></li>
        <li><a href="contact.html">Contact</a></li>
      </ul>
    </div>
    <div class="col-md-3 abf">
      <h4 class="f-h-4 mt-4">About</h4>
      <hr>
      <ul class="ful">

        <li><i class="fa-solid fa-location-dot "></i> <a href="https://goo.gl/maps/sy3xNu3pbDFkzRFR7" target="_blank">
            Metro Star Gate ,Karachi</a></li>
        <li> <i class="fa-solid fa-phone "></i> <a href="tel:+021-111-222" target="_blank">03478846555</a></li>
        <li> <i class="fa-solid fa-envelope fa-lg"></i> <a href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox"
            target="_blank">plantpalace@gmail.com</a></li>
      </ul>
    </div>
    <div class="col-md-3 picg">
      <h4 class="f-h-4 mt-4">Photo Gallery</h4>
      <hr>
      <ul class="ful" style=" display: flex !important;
        flex-direction: row !important ; margin-bottom: 20px; ">
        <li><a href="index.html#ourgallery"> <img class="ph" src="assets/images/banner-images/gallery-one.jpg"></a></li>
        <li><a href="index.html#ourgallery"><img class="ph" src="assets/images/banner-images/gallery-two.jpg"></a></li>
        <li><a href="index.html#ourgallery"><img class="ph" src="assets/images/banner-images/gallery-three.jpg"></a></li>
      </ul>
      <ul class="ful" style=" display: flex !important;
         flex-direction: row !important ;">
        <li><a href="index.html#ourgallery"><img class="ph" src="assets/images/banner-images/gallery-four.jpg"></a></li>
        <li><a href="index.html#ourgallery"><img class="ph" src="assets/images/banner-images/gallery-five.jpg"></a></li>
        <li><a href="index.html#ourgallery"><img class="ph" src="assets/images/banner-images/gallery-six.jpg"></a></li>
      </ul>


    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-12" id="footericon">
      <a class="btn btn-outline-light btn-floating m-1" href="#!" role="button" target="_blank"><i
          class="fab fa-twitter"></i></a>
      <a class="btn btn-outline-light btn-floating m-1" href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox"
        target="_blank" role="button"><i class="fab fa-google"></i></a>
      <a class="btn btn-outline-light btn-floating m-1" href="www.instagram.com" role="button" target="_blank"><i
          class="fab fa-instagram"></i></a>
      <a class="btn btn-outline-light btn-floating m-1" href="www.linkedin.com" target="_blank" role="button"><i
          class="fab fa-linkedin-in"></i></a>
      <a class="btn btn-outline-light btn-floating m-1" href="www.github.com" target="_blank" role="button"><i
          class="fab fa-github"></i></a>
    </div>

  </div>
</footer>`

  ;

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("#footer").forEach((e) => {
      const extractedFooter = footer.slice(
        footer.indexOf("<footer>"),
        footer.lastIndexOf("</footer>") + 9
      );
      e.innerHTML = extractedFooter;
    });
  });