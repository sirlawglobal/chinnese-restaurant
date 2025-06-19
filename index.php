<?php 
require_once __DIR__ . '/BackEnd/config/init.php';

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./assets/styles/main.css" />
    <link rel="stylesheet" href="./assets/styles/index.css" />
    <title>Golden Dish</title>
  </head>
  <body>
    <header class="flex justify-between">
      <div class="logo">
        <a href="index.html">
          <img src="./assets/images/logo.webp" alt="Golden Dish" />
        </a>
      </div>
      <nav class="flex">
        <ul class="flex justify-between">
          <li><a href="./menu/">Menu</a></li>
          <li><a href="#">About</a></li>
          <li><a href="./contact/">Contact</a></li>
        </ul>
 

        <div class="buttons"> 
          <?php  if(isLoggedIn()): ?> 
         <a class="button button-primary" href="BackEnd/controller/auth/logout.php">Logout</a>
           <?php  else: ?> 
            <a class="button button-primary" href="login/">Log in</a>
              <?php  endif; ?> 
          <!-- <a class="button button-primary" href="./menu/">Order Now</a> -->
        </div>
      </nav>
    </header>
    <main>
      <section id="hero" class="flex justify-between align-center">
        <div class="text">
          <h1>Your Favorite Chinese Dishes, Just a Tap Away!</h1>
          <p>
            Savor the taste of tradition with fresh, chef-prepared meals at your
            doorstep. <br> Order now and experience real Chinese cuisine!
          </p>
          <a class="button button-secondary" href="./menu/">Menu</a>
        </div>
        <div class="images flex align-center justify-between">
          <img src="./assets/images/p1.webp" alt="Golden Dish" />
          <img src="./assets/images/p2.webp" alt="Golden Dish" />
          <img src="./assets/images/p3.webp" alt="Golden Dish" />
        </div>
      </section>
    </main>
  </body>
</html>
