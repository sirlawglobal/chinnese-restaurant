<?php require_once __DIR__ . '/../BackEnd/config/init.php';  ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Golden Dish | Menu</title>
  <link rel="stylesheet" href="../assets/styles/main.css" />
  <link rel="stylesheet" href="../assets/styles/menu.css" />

  <style>
  /* Styles for dish actions, review button, modal, and tooltip *//* Styles for review modal and buttons */
.dish__review {
  background: #f0f0f0;
  border: none;
  border-radius: 4px;
  padding: 0.5rem 1rem;
  margin-left: 0.5rem;
  cursor: pointer;
  font-size: 0.9rem;
}

.dish__review:hover {
  background: #e0e0e0;
}

.review-modal label {
  display: block;
  margin-top: 1rem;
  font-weight: bold;
}

.review-modal input,
.review-modal textarea {
  width: 100%;
  padding: 0.5rem;
  margin-top: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.review-modal button {
  background: #28a745;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  margin: 0.5rem;
  border-radius: 4px;
  cursor: pointer;
}

.review-modal .close-modal {
  background: #dc3545;
}

.review-modal button:hover {
  opacity: 0.9;
}
</style>
  
</head>

<body>
  <div id="sprite" class="hidden"></div>
  <header class="flex justify-between">
    <div class="logo">
      <a href="../">
        <img src="../assets/images/logo.webp" alt="Golden Dish" />
      </a>
    </div>
    <div class="flex column">
      <nav class="flex justify-end">
        <ul class="flex justify-between">
          <li><a href="../menu/">Menu</a></li>
          <li><a href="#">About</a></li>
          <li><a href="../contact/">Contact</a></li>
        </ul>
        <div class="buttons">
          <!-- <a class="button button-primary" href="../login/">Order Now</a> -->
<?php  if(isLoggedIn()): ?> 
          <a class="button button-primary" href="../BackEnd/controller/auth/logout.php">Logout</a>
           <?php  else: ?> 
            <a class="button button-primary" href="../login/">Log in</a>
              <?php  endif; ?> 

          <!-- http://localhost/chinnese-restaurant/login/ -->
          <!-- <a class="button button-primary" href="./menu/">Order Now</a> -->
        </div>

        
      </nav>
      <div class="header__top full-width">
        <div class="flex justify-end align-center wrap full-width">
          <div class="header__search">
            <input type="text" placeholder="Enter item you are looking for" class="search__input" />
            <button class="search__button">
              <svg class="icon">
                <use href="#search"></use>
              </svg>
            </button>
          </div>
          <a href="../cart/" class="button--cart flex align-center justify-center">
            <svg class="icon">
              <use href="#bag"></use>
            </svg>
          </a>
          <!-- <button class="button button--signin" href="../login/">Sign in</button> -->
           <!-- <button class="button button--signin" onclick="window.location.href='../login/'">Sign in</button> -->

        </div>
      </div>
    </div>
  </header>
  <div class="wrapper">
    <header class="header"></header>

    <main class="main">
      <nav class="nav">
        <h3 class="nav__title">Dishes</h3>
        <ul class="nav__list">
          <li class="nav__item nav__item--active">Rice Dishes</li>
          <li class="nav__item">Noodles & Chow Mein</li>
          <li class="nav__item">Soups</li>
          <li class="nav__item">Chicken Dishes</li>
          <li class="nav__item">Beef & Pork Dishes</li>
          <li class="nav__item">Sweet & Sour Dishes</li>
          <li class="nav__item">Seafood Specials</li>
          <li class="nav__item">Vegetarian Dishes</li>
          <li class="nav__item">Dim Sum & Small Bites</li>
        </ul>
      </nav>
      <section class="dishes">
        <h2 class="dishes__title">Rice Dishes</h2>
        <div class="dishes__grid">
          <!-- Dynamically Populated with JS -->
        </div>
      </section>
    </main>
  </div>
  <footer class="footer flex align-center justify-between">
    <div class="footer__content">
      <div class="footer__socials">
        <a href="#" class="footer__icon">
          <svg class="icon">
            <use href="#facebook"></use>
          </svg>
        </a>
        <a href="#" class="footer__icon">
          <svg class="icon">
            <use href="#insta"></use>
          </svg>
        </a>
        <a href="#" class="footer__icon">
          <svg class="icon">
            <use href="#twitter"></use>
          </svg>
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

  <script src="../assets/scripts/menu.js"></script>

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

 


</body>

</html>