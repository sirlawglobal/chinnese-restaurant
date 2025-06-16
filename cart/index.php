<?php require_once __DIR__ . '/../BackEnd/config/init.php';  ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Golden Dish | Cart</title>
    <link rel="stylesheet" href="../assets/styles/main.css" />
    <link rel="stylesheet" href="../assets/styles/cart.css" />
    <!-- Include DOMPurify for sanitization -->
    <script src="https://cdn.jsdelivr.net/npm/dompurify@2.3.6/dist/purify.min.js"></script>
  </head>
  <body>
    <div id="sprite" class="hidden"></div>
    <div class="wrapper">
      <header class="header">
        <div class="header__top">
          <div class="flex column">
            <a href="../" class="header__logo">
              <img src="../assets/images/logo2.webp" alt="Golden Dish Logo" />
            </a>
          </div>
          <div class="flex justify-end align-center wrap">
            <div class="header__search">
              <input
                type="text"
                placeholder="Enter item you are looking for"
                class="search__input"
                aria-label="Search for menu items"
              />
              <button class="search__button" aria-label="Search">
                <svg class="icon"><use href="#search"></use></svg>
              </button>
            </div>
            <button
              class="button--cart flex align-center justify-center"
              aria-label="View cart"
            >
              <svg class="icon"><use href="#bag"></use></svg>
              <span class="cart-badge">0</span>
            </button>
            <div class="buttons">
            <?php  if(isLoggedIn()): ?> 
         <a class="button button-primary" href="../BackEnd/controller/auth/logout.php">Logout</a>
           <?php  else: ?> 
            <a class="button button-primary" href="../login/">Log in</a>
              <?php  endif; ?> 

              </div>
            <!-- <button class="button button--signin">Sign in</button> -->
          </div>
        </div>
      </header>
    </div>
    <main class="main">
      <section class="details">
        <div class="flex align-center justify-center">
          <div class="details__content flex align-center">
            <div class="details__image">
              <img src="../assets/images/cart-img.png" alt="Golden Dish" />
            </div>
            <div class="details__info">
              <h2 class="details__title">LunchBox - Meals and Thalis</h2>
              <div class="flex info justify-center">
                <div class="info__details">
                  <div class="rating flex align-center">
                    <svg class="icon"><use href="#star"></use></svg>
                    <span class="rating__text">4.0</span>
                  </div>
                  <p class="info__text">100+ ratings</p>
                </div>
                <div class="info__details">
                  <div class="info__text">30 Mins</div>
                  <div class="info__text">Delivery Time</div>
                </div>
                <div class="info__details">
                  <div class="info__text">£180</div>
                  <div class="info__text">Cost for two</div>
                </div>
              </div>
            </div>
          </div>
          <div class="details__offer">
            <h3>Offers</h3>
            <ul class="details_list">
              <li class="list__item">50% off up to £100 | Use code TRYNEW</li>
              <li class="list__item">20% off | Use code PARTY</li>
            </ul>
          </div>
        </div>
      </section>
      <section class="content">
        <div class="flex align-center justify-between search-container">
          <div class="header__search">
            <input
              class="search__input"
              placeholder="Search for dish"
              aria-label="Search for dish in cart"
            />
            <button class="search__button" aria-label="Search cart">
              <svg class="icon">
                <use href="#search"></use>
              </svg>
            </button>
          </div>
          <div class="favourite flex align-center justify-center">
            <svg class="icon">
              <use href="#fav"></use>
            </svg>
            Favorite
          </div>
        </div>
        <div class="flex justify-center">
          <div class="dishes">
            <!-- Dishes will be populated dynamically based on cart items -->
          </div>
          <div class="cart">
            <div class="flex justify-between align-center cart__header">
              <h3 class="cart__title">Cart</h3>
              <p class="cart__count">0 Items</p>
            </div>
            <div class="cart__content">
              <!-- Cart items will be populated dynamically -->
            </div>
            <div class="cart__subtotal">
              <div>
                <p class="cart__subtotal__text">Subtotal:</p>
                <small class="note">Extra charges may apply</small>
              </div>
              <p class="cart__subtotal__price">£0</p>
            </div>
            <a href="../checkout/" class="button--checkout">Checkout</a>
          </div>
        </div>
      </section>
    </main>
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
      // Load SVG sprite
      const sprite = document.getElementById("sprite");
      (async () => {
        try {
          const data = await fetch("../assets/icons-sprite.svg").then(
            (response) => response.text()
          );
          sprite.innerHTML = data;
        } catch (error) {
          console.error("Error loading SVG sprite:", error);
        }
      })();

      document.addEventListener("DOMContentLoaded", function () {
        const cartContainer = document.querySelector(".cart__content");
        const dishesContainer = document.querySelector(".dishes");
        const cartCount = document.querySelector(".cart__count");
        const cartSubtotal = document.querySelector(".cart__subtotal__price");
        const cartButton = document.querySelector(".button--cart");
        const cartBadge = cartButton.querySelector(".cart-badge");
        let cartItems = JSON.parse(localStorage.getItem("cart")) || [];

        // Function to render cart items and dishes
        function renderCartItems() {
          cartContainer.innerHTML = "";
          dishesContainer.innerHTML = "";
          
          if (cartItems.length === 0) {
            cartContainer.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
            dishesContainer.innerHTML = '<p class="empty-dishes-message">No items in your cart to display</p>';
            cartCount.textContent = "0 Items";
            cartSubtotal.textContent = "£0";
            cartBadge.textContent = "0";
            cartBadge.style.display = "none";
            return;
          }

          // Get categories from localStorage
          const categories = JSON.parse(localStorage.getItem("menu_categories")) || [];

          // Helper function to get category name by id
          function getCategoryNameById(id) {
            const category = categories.find(cat => cat.id === id);
            return category ? category.name : "Set Menu";
            // return category ? category.name : "Unknown";
          }

          // Render items in cart
          cartItems.forEach((item, index) => {
            console.log("Rendering item111:", item);
            const categoryName = getCategoryNameById(item.category);

            // Render in cart
            const cartItem = document.createElement("div");
            cartItem.className = "cart__item";
            cartItem.innerHTML = `
              <p class="dish__origin">Category: <span>${DOMPurify.sanitize(categoryName)}</span></p>
              <div class="flex justify-between align-center">
                <div class="item__details">
                  <h4 class="item__title">${DOMPurify.sanitize(item.name)} (${DOMPurify.sanitize(item.portion)})</h4>
                  <p class="item__price">£${(parseFloat(item.price) || 0).toFixed(2)}</p>
                </div>
                <div class="cart__actions">
                  <button class="action__button" aria-label="Decrease quantity of ${DOMPurify.sanitize(item.name)}">-</button>
                  <input class="item__count" type="number" min="1" value="${item.quantity}" aria-label="Quantity of ${DOMPurify.sanitize(item.name)}" />
                  <button class="action__button" aria-label="Increase quantity of ${DOMPurify.sanitize(item.name)}">+</button>
                </div>
              </div>
            `;
            cartContainer.appendChild(cartItem);

            // Render in dishes with full description
            const dishItem = document.createElement("div");
            dishItem.className = "dish flex align-center justify-between";
            dishItem.innerHTML = `
              <div class="dish__details">
                <h3 class="dish__title">${DOMPurify.sanitize(item.name)}</h3>
                <span class="dish__price">£${(parseFloat(item.price) || 0).toFixed(2)}</span>
                <p class="dish__description">${DOMPurify.sanitize(item.description || "No description available")}</p>
              </div>
              <button class="button button--add"  style='visibility:hidden'>Add +</button>
            `;
            dishesContainer.appendChild(dishItem);
          });

          updateCartBadge();
          updateTotalPrice();
          initCartItems();
          initDishButtons();
        }

        // Function to calculate and update the total price
        function updateTotalPrice() {
          let total = 0;
          cartItems.forEach((item) => {
            const price = parseFloat(item.price) || 0;
            const quantity = item.quantity || 1;
            total += price * quantity;
          });
          cartSubtotal.textContent = `£${total.toFixed(2)}`;
        }

        // Function to update cart badge and count
        function updateCartBadge() {
          const totalItems = cartItems.reduce((sum, item) => sum + (item.quantity || 1), 0);
          cartBadge.textContent = totalItems;
          cartBadge.style.display = totalItems > 0 ? "flex" : "none";
          cartCount.textContent = `${totalItems} Item${totalItems !== 1 ? "s" : ""}`;
          localStorage.setItem("cart", JSON.stringify(cartItems));
        }

        // Initialize quantity controls for each cart item
        function initCartItems() {
          const currentItems = document.querySelectorAll(".cart__item");
          currentItems.forEach((item, index) => {
            const minusBtn = item.querySelector(".action__button:first-child");
            const plusBtn = item.querySelector(".action__button:last-child");
            const quantityInput = item.querySelector(".item__count");

            // Remove existing listeners to prevent duplicates
            const newMinusBtn = minusBtn.cloneNode(true);
            const newPlusBtn = plusBtn.cloneNode(true);
            minusBtn.replaceWith(newMinusBtn);
            plusBtn.replaceWith(newPlusBtn);

            newMinusBtn.addEventListener("click", () => {
              let currentValue = parseInt(quantityInput.value);
              if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
                cartItems[index].quantity = currentValue - 1;
              } else {
                cartItems.splice(index, 1);
                renderCartItems();
              }
              updateCartBadge();
              updateTotalPrice();
            });

            newPlusBtn.addEventListener("click", () => {
              let currentValue = parseInt(quantityInput.value);
              quantityInput.value = currentValue + 1;
              cartItems[index].quantity = currentValue + 1;
              updateCartBadge();
              updateTotalPrice();
            });

            quantityInput.addEventListener("change", () => {
              let value = parseInt(quantityInput.value);
              if (isNaN(value) || value < 1) {
                quantityInput.value = 1;
                cartItems[index].quantity = 1;
              } else {
                cartItems[index].quantity = value;
              }
              updateCartBadge();
              updateTotalPrice();
            });
          });
        }

        // Initialize add buttons for dishes
        function initDishButtons() {
          document.querySelectorAll(".button--add").forEach((button, index) => {
            // Remove existing listeners to prevent duplicates
            const newButton = button.cloneNode(true);
            button.replaceWith(newButton);

            newButton.addEventListener("click", () => {
              const item = cartItems[index];
              item.quantity++;
              updateCartBadge();
              renderCartItems();
              cartBadge.classList.add("bump");
              setTimeout(() => cartBadge.classList.remove("bump"), 300);
            });
          });
        }

        // Initial render
        renderCartItems();

        // Observe cart container for dynamic updates
        const observer = new MutationObserver(() => {
          initCartItems();
          initDishButtons();
          updateTotalPrice();
        });

        if (cartContainer) {
          observer.observe(cartContainer, {
            childList: true,
            subtree: true,
          });
        }
      });
    </script>
  </body>
</html>