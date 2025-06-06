<?php require_once __DIR__ . '/../BackEnd/config/init.php';  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta
      http-equiv="origin-trial"
      content="A7vZI3v+Gz7JfuRolKNM4Aff6zaGuT7X0mf3wtoZTnKv6497cVMnhy03KDqX7kBz/q/iidW7srW31oQbBt4VhgoAAACUeyJvcmlnaW4iOiJodHRwczovL3d3dy5nb29nbGUuY29tOjQ0MyIsImZlYXR1cmUiOiJEaXNhYmxlVGhpcmRQYXJ0eVN0b3JhZ2VQYXJ0aXRpb25pbmczIiwiZXhwaXJ5IjoxNzU3OTgwODAwLCJpc1N1YmRvbWFpbiI6dHJ1ZSwiaXNUaGlyZFBhcnR5Ijp0cnVlfQ=="
    />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    />

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Optional JavaScript for interactivity -->
    <script
      type="text/javascript"
      async=""
      charset="utf-8"
      src="https://www.gstatic.com/recaptcha/releases/hbAq-YhJxOnlU-7cpgBoAJHb/recaptcha__en.js"
      crossorigin="anonymous"
      integrity="sha384-0uUcqAX/lKvnfFMvCM7U5wcjfgBvv/1q+xxZKV6ZhBH4ikGcgTDEC4vEZPTt3l8O"
    ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
    <link rel="stylesheet" href="../assets/styles/main.css" />
    <!-- <link rel="stylesheet" href="../assets/styles/checkout.css" /> -->
    <link rel="stylesheet" href="../assets/styles/contact.css" />
    <script defer="" src="/script.js"></script>
    <!-- <script src="https://js.hcaptcha.com/1/api.js" async defer></script> -->
    <!-- Add reCAPTCHA script -->
    <script
      src="https://www.google.com/recaptcha/api.js"
      async=""
      defer=""
    ></script>

    <title>Contact Us</title>
  </head>
  <body>
    <div id="sprite" class="hidden"></div>
    <div class="wrapper">
      <header class="header">
        <div class="header__top">
          <div class="flex column">
            <a href="../" class="header__logo">
              <img src="../assets/images/logo2.webp" alt="" />
            </a>
          </div>
          <nav class="flex align-center">
            <ul class="flex justify-between align-center">
              <li><a href="../menu/">Menu</a></li>
              <li><a href="#">About</a></li>
              <li><a href="../contact/">Contact</a></li>
            </ul>
            <div class="buttons">
              <a class="button button-primary" href="./menu/">Order Now</a>
            </div>
          </nav>
        </div>
      </header>
      <main class="main">
  <div class="flex justify-center inner">
    <h3 class="main__title">Contact Us</h3>

    <div class="form__container">
      <h3 class="form__title">Get in touch</h3>
      <p class="form__text">
        Please complete the form below to contact us and we will respond
        to your request shortly.
      </p>

      <!-- Add form tag here -->
      <form
        id="contact-form"
        method="POST"
      >
        <div class="form__grid">
          <!-- Contact Info Blocks (optional to keep in form) -->
          <div class="grid__item">
            <div class="icon">
              <i class="fa fa-mobile" aria-hidden="true" style="font-size: 50px"></i>
            </div>
            <div class="grid__content">
              <h6 class="content__header">Phone</h6>
              <p class="content__text">020 8459 5555</p>
            </div>
          </div>

          <div class="grid__item">
            <div class="icon">
              <i class="fa fa-globe" aria-hidden="true" style="font-size: 30px"></i>
            </div>
            <div class="grid__content">
              <h6 class="content__header">Address</h6>
              <p class="content__text">
                113 High Street, Willesden <br /> London NW10 2SL
              </p>
            </div>
          </div>

          <!-- Form Inputs -->
          <div class="grid__item align-center">
            <div class="icon">
              <i class="fa fa-user" aria-hidden="true" style="font-size: 30px"></i>
            </div>
            <div class="grid__content">
              <input type="text" name="name" placeholder="Name" required />
            </div>
          </div>
          <div class="grid__item align-center">
            <div class="icon">
              <i class="fa fa-envelope" aria-hidden="true" style="font-size: 30px"></i>
            </div>
            <div class="grid__content">
              <input type="email" name="email" placeholder="Email" required />
            </div>
          </div>
          <div class="grid__item align-center">
            <div class="icon">
              <i class="fa fa-phone" aria-hidden="true" style="font-size: 30px"></i>
            </div>
            <div class="grid__content">
              <input type="tel" name="telephone" placeholder="Telephone" />
            </div>
          </div>
          <div class="grid__item align-center">
            <div class="icon">
              <i class="fa fa-commenting" aria-hidden="true" style="font-size: 30px"></i>
            </div>
            <div class="grid__content">
              <input
                type="text"
                name="message"
                placeholder="Please type in your request"
                required
              />
            </div>
          </div>
        </div>

        <!-- Submit Button -->
          <button id="submitBtn" class="button--submit" type="submit">
  <img src="../assets/images/send-mail.png" alt="" style="width: 30px; height: 30px" />
  Send
</button>

      </form>

    </div>

    <!-- Map -->
    <div class="map">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2481.162769232366!2d-0.23268282359578057!3d51.546914271822324!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48761052052094bd%3A0x1490f73b3b5b304f!2s113%20High%20Rd%2C%20London%20NW10%202SL%2C%20UK!5e0!3m2!1sen!2sng!4v1744643409548!5m2!1sen!2sng"
        style="border: 0"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
      ></iframe>
    </div>
  </div>
</main>

    </div>
    <footer class="footer flex align-center justify-between">
      <div class="footer__content">
        <div class="footer__socials">
          <a href="#" class="footer__icon">
            <svg class="icon"><use href="#facebook"></use></svg>
          </a>
          <a href="#" class="footer__icon">
            <svg class="icon"><use href="#insta"></use></svg>
          </a>
          <a href="#" class="footer__icon">
            <svg class="icon"><use href="#twitter"></use></svg>
          </a>
        </div>
      </div>
      <nav class="footer__nav">
        <a href="#" class="footer__link">About us</a>
        <a href="#" class="footer__link">Delivery</a>
        <a href="#" class="footer__link">Help & Support</a>
        <a href="#" class="footer__link">T&C</a>
      </nav>
    </footer>

    <script>
      // select the #sprite and get data from an external file and render it there
      const sprite = document.getElementById("sprite");
      (async () => {
        const data = await fetch("../assets/icons-sprite.svg").then(
          (response) => response.text()
        );
        sprite.innerHTML = data;
      })();
    </script>


<script>
  const contactForm = document.getElementById("contact-form");
  const submitBtn = document.getElementById("submitBtn");

  contactForm.addEventListener("submit", function (e) {
    e.preventDefault(); // Stop default form submission

    const formData = new FormData(contactForm);
    const payload = Object.fromEntries(formData.entries());
    payload.data_type = "message"; // include data_type for backend switch

    send_data("message", payload);
  });

  function send_data(data_type = "message", payload = {}) {
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Sending...";
    }

    const ajax = new XMLHttpRequest();
    ajax.onreadystatechange = function () {
      if (ajax.readyState === 4) {
        if (ajax.status === 200 && ajax.responseText.trim() !== "") {
          handle_result(ajax.responseText, data_type);
        }

        // Re-enable button regardless of result
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = "Send";
        }
      }
    };

  let ROOTS = "<?= ROOT ?>"; // This works ONLY inside a PHP file
  ajax.open("POST", ROOTS + "/contact/contact.php", true);
 // ajax.open("POST", "http://localhost/chinnese-restaurant/contact/contact.php", true);
    ajax.setRequestHeader("Content-Type", "application/json");
    ajax.send(JSON.stringify(payload));
  }

  function handle_result(result, data_type) {
    try {
      const obj = JSON.parse(result);
      if (obj.data_type !== data_type) return;

      if (data_type === "message") {
        alert(obj.message);
        contactForm.reset(); // clear form
      }
    } catch (e) {
      console.error("Invalid JSON response", result);
    }
  }
</script>


  </body>
</html>
