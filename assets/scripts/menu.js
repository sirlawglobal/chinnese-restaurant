// Helper to decode HTML entities
    function decodeHTML(html) {
      const txt = document.createElement("textarea");
      txt.innerHTML = html;
      return txt.value;
    }

    // Helper to format current date as YYYY-MM-DD HH:MM:SS
    function getCurrentDateTime() {
      const now = new Date();
      return now.getFullYear() + '-' +
             String(now.getMonth() + 1).padStart(2, '0') + '-' +
             String(now.getDate()).padStart(2, '0') + ' ' +
             String(now.getHours()).padStart(2, '0') + ':' +
             String(now.getMinutes()).padStart(2, '0') + ':' +
             String(now.getSeconds()).padStart(2, '0');
    }

    document.addEventListener("DOMContentLoaded", function () {
      // Create and style the review modal
      const modal = document.createElement("div");
      modal.className = "review-modal";
      modal.style.display = "none";
      modal.style.position = "fixed";
      modal.style.top = "50%";
      modal.style.left = "50%";
      modal.style.transform = "translate(-50%, -50%)";
      modal.style.background = "#ffffff";
      modal.style.padding = "2rem";
      modal.style.borderRadius = "12px";
      modal.style.boxShadow = "0 8px 16px rgba(0,0,0,0.15)";
      modal.style.zIndex = "1000";
      modal.style.maxWidth = "450px";
      modal.style.width = "90%";
      modal.style.fontFamily = "'Inter', Arial, sans-serif";
      modal.innerHTML = `
        <style>
          .review-modal h3 {
            margin: 0 0 1rem;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a1a1a;
          }
          .review-modal p {
            margin: 0 0 1.5rem;
            font-size: 1rem;
            color: #4a4a4a;
          }
          .review-modal label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: #333;
          }
          .review-modal input, .review-modal select, .review-modal textarea {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.2s;
          }
          .review-modal input:focus, .review-modal select:focus, .review-modal textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.1);
          }
          .review-modal select option[value="1"] { color: #dc3545; }
          .review-modal select option[value="2"] { color: #fd7e14; }
          .review-modal select option[value="3"] { color: #ffc107; }
          .review-modal select option[value="4"] { color: #28a745; }
          .review-modal select option[value="5"] { color: #17a2b8; }
          .review-modal textarea {
            resize: vertical;
            min-height: 100px;
          }
          .review-modal .button-group {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
          }
          .review-modal button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
          }
          .review-modal button[type="submit"] {
            background: #28a745;
            color: white;
          }
          .review-modal button[type="submit"]:hover {
            background: #218838;
          }
          .review-modal button.close-modal {
            background: #dc3545;
            color: white;
          }
          .review-modal button.close-modal:hover {
            background: #c82333;
          }
        </style>
        <h3>Add Review for <span id="modal-dish-name"></span></h3>
        <p>Category: <span id="modal-dish-category"></span></p>
        <form id="review-form">
          <label for="reviewer-name">Your Name:</label>
          <input type="text" id="reviewer-name" name="reviewer-name" placeholder="Enter your name" required>
          <label for="rating">Rating:</label>
          <select id="rating" name="rating" required>
            <option value="" disabled selected>Select rating (1-5)</option>
            <option value="1">1 - Poor</option>
            <option value="2">2 - Fair</option>
            <option value="3">3 - Good</option>
            <option value="4">4 - Very Good</option>
            <option value="5">5 - Excellent</option>
          </select>
          <label for="review-text">Review:</label>
          <textarea id="review-text" name="review-text" rows="4" placeholder="Share your thoughts about this dish..." required></textarea>
          <div class="button-group">
            <button type="submit">Submit Review</button>
            <button type="button" class="close-modal">Cancel</button>
          </div>
        </form>
      `;
      document.body.appendChild(modal);

      // Create overlay
      const overlay = document.createElement("div");
      overlay.className = "modal-overlay";
      overlay.style.display = "none";
      overlay.style.position = "fixed";
      overlay.style.top = "0";
      overlay.style.left = "0";
      overlay.style.width = "100%";
      overlay.style.height = "100%";
      overlay.style.background = "rgba(0,0,0,0.5)";
      overlay.style.zIndex = "999";
      document.body.appendChild(overlay);

      // Modal handling
      function openModal(item, categoryName) {
        modal.dataset.itemId = item.id;
        modal.dataset.itemName = item.name;
        modal.dataset.categoryName = categoryName;
        document.getElementById("modal-dish-name").textContent = item.name;
        document.getElementById("modal-dish-category").textContent = categoryName;
        modal.style.display = "block";
        overlay.style.display = "block";
      }

      function closeModal() {
        modal.style.display = "none";
        overlay.style.display = "none";
        document.getElementById("review-form").reset();
      }

      // Handle clicks on overlay or close button
      overlay.addEventListener("click", closeModal);
      modal.querySelector(".close-modal").addEventListener("click", closeModal);

      // Handle review submission
    // ... (Previous code up to modal creation remains unchanged)

    // Handle review submission
    document.getElementById("review-form").addEventListener("submit", function (e) {
      e.preventDefault();
      const rating = document.getElementById("rating").value;
      const reviewText = document.getElementById("review-text").value;
      const reviewerName = document.getElementById("reviewer-name").value;
      const itemId = modal.dataset.itemId;
      const itemName = modal.dataset.itemName;
      const categoryName = modal.dataset.categoryName;
      const reviewDate = getCurrentDateTime();

      // Validate inputs
      if (!itemId || !itemName || !categoryName || !reviewerName || !rating || !reviewText || !reviewDate) {
        console.error("Missing required fields:", {
          itemId, itemName, categoryName, reviewerName, rating, reviewText, reviewDate
        });
        alert("Please fill in all fields correctly.");
        return;
      }

      const payload = {
        itemId,
        itemName,
        categoryName,
        reviewerName,
        rating,
        reviewText,
        reviewDate
      };
      console.log("Submitting review payload:", payload);

      fetch("/chinnese-restaurant/BackEnd/controller/reviews/submit_review.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      })
      .then(response => {
        console.log("Response status:", response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log("Review submission response:", data);
        if (data.success) {
          alert("Review submitted successfully!");
          closeModal();
        } else {
          console.error("Backend error:", data.message);
          alert("Error submitting review: " + data.message);
        }
      })
      .catch(error => {
        console.error("Error submitting review:", error.message);
        alert("Failed to submit review: " + error.message + ". Please try again.");
      });
    });

    // ... (Rest of the code remains unchanged)

      fetch("/chinnese-restaurant/BackEnd/controller/inventory/get_menu.php")
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          data = data.data;

          // Save categories to localStorage in the format: [{ id, name }]
          const simplifiedCategories = data.categories.map((category) => ({
            id: category.id,
            name: category.name,
          }));
          localStorage.setItem("menu_categories", JSON.stringify(simplifiedCategories));

          const navList = document.querySelector(".nav__list");
          const dishesSection = document.querySelector(".dishes");
          const dishesTitle = document.querySelector(".dishes__title");
          const dishesGrid = document.querySelector(".dishes__grid");
          navList.innerHTML = "";
          data.categories = data.categories.filter(category => category.name !== "SET MENU");

          // Create navigation items
          data.categories.forEach((category, index) => {
            const navItem = document.createElement("li");
            navItem.className = "nav__item";
            navItem.textContent = category.name.toLowerCase();

            navItem.addEventListener("click", () => {
              document.querySelectorAll(".nav__item").forEach((item) =>
                item.classList.remove("nav__item--active")
              );
              navItem.classList.add("nav__item--active");
              renderDishes(category);
            });

            navList.appendChild(navItem);

            if (index < data.categories.length - 1) {
              const separator = document.createElement("span");
              separator.textContent = " | ";
              navList.appendChild(separator);
            }
          });

          // Default to first category
          if (data.categories.length > 0) {
            navList.firstElementChild.classList.add("nav__item--active");
            renderDishes(data.categories[0]);
          }

          function renderDishes(category) {
            dishesTitle.textContent = category.name.toLowerCase();

            const existingSubtitle = dishesTitle.nextElementSibling;
            if (
              existingSubtitle &&
              existingSubtitle.classList.contains("category-subtitle")
            ) {
              existingSubtitle.remove();
            }

            if (category.note) {
              const subtitle = document.createElement("h4");
              subtitle.className = "category-subtitle";
              subtitle.style.marginTop = "-1rem";
              subtitle.style.marginBottom = "1rem";
              subtitle.textContent = category.note;
              dishesTitle.after(subtitle);
            }

            dishesGrid.innerHTML = "";

            category.items.forEach((item) => {

              cosn
              const dishCard = document.createElement("article");
              dishCard.className = "dish";

              // Build options HTML if available
              let optionsHTML = "";
              if (Array.isArray(item.options) && item.options.length > 0) {
                optionsHTML = `
                  <div class="dish__options">
                    ${item.options
                      .map(
                        (option) => `
                          <div class="dish__option flex align-center justify-between">
                            <span class="dish__option-name">${option.portion}</span>
                            <span class="dish__price justify-start">
                              <svg class="icon"><use href="#tag"></use></svg>
                              £${parseFloat(option.price).toFixed(2)}
                            </span>
                          </div>
                        `
                      )
                      .join("")}
                  </div>
                `;
              }

              // Decode and display description
              let descriptionHTML = "";
              if (item.description) {
                const decodedDesc = decodeHTML(item.description);
                const match = decodedDesc.match(/\[(.*)\]/);
                if (match) {
                  let arrayStr = match[0].replace(/""/g, '"');
                  try {
                    const parsedArray = JSON.parse(arrayStr);
                    descriptionHTML =
                      `<ul class="dish__desc-list">` +
                      parsedArray.map((item) => `<li>${item}</li>`).join("") +
                      `</ul>`;
                  } catch (e) {
                    console.error("Error parsing cleaned array string:", e);
                    descriptionHTML = `<p class="dish__vendor">${decodedDesc}</p>`;
                  }
                } else {
                  descriptionHTML = `<p class="dish__vendor">${decodedDesc}</p>`;
                }
              }

              // Safe price & portion extraction
              const hasOptions = Array.isArray(item.options) && item.options.length > 0;
              const firstOption = hasOptions ? item.options[0] : null;
              const itemPrice = hasOptions
                ? parseFloat(firstOption.price)
                : parseFloat(item.price || 0);
              const itemPortion = hasOptions ? firstOption.portion : "standard";

              const backendUploadsUrl = "/chinnese-restaurant/BackEnd";
              const imageSrc = item.image_url
                ? backendUploadsUrl + item.image_url
                : "/chinnese-restaurant/avarterdefault.jpg";

             dishCard.innerHTML = `
  <div class="dish__image">
    <img src="${imageSrc}" alt="${item.name}" />
  </div>

  <div class="dish__details"  style='border-bottom: 1px solid #ddd;margin-bottom: 9px; padding-bottom: 9px;'>
    <h3 class="dish__name">${item.name}</h3>
    ${descriptionHTML}
  </div>

  <div style="display: flex; gap: 0.5rem; align-items: end">
    <div class="dish__info">
      ${optionsHTML || `
        <span class="dish__price">
          <svg class="icon"><use href="#tag"></use></svg>
          £${itemPrice.toFixed(2)}
        </span>
      `}
    </div>

    <div class="dish__button">
      <button 
        class="dish__add"
        data-id="${item.id}"
        data-name="${item.name}"
        data-price="${itemPrice}"
        data-portion="${itemPortion}"
        data-category-id="${item.category_id}"
      >+</button>

      <button 
        class="dish__review"
        data-id="${item.id}"
        data-name="${item.name}"
        data-category-name="${category.name}"
      >Add Review</button>
    </div>
  </div>
`;


              dishesGrid.appendChild(dishCard);
            });

            // Add event listeners for review buttons
            document.querySelectorAll(".dish__review").forEach((button) => {
              button.addEventListener("click", () => {
                const itemData = JSON.parse(button.getAttribute("data-item"));
                openModal(itemData, itemData.categoryName);
              });
            });
          }
        })
        .catch((error) => {
          console.error("Error loading menu data:", error);
          fetch("../assets/data/menu.json")
            .then((response) => response.json())
            .then((data) => {
              console.log("Using fallback static data");
            });
        });

      // CART FUNCTIONALITY
      const cartButton = document.querySelector(".button--cart");
      const cartBadge = document.createElement("span");
      cartBadge.className = "cart-badge";
      cartBadge.textContent = "0";
      cartButton.appendChild(cartBadge);

      let cartItems = JSON.parse(localStorage.getItem("cart")) || [];


      console.log("Initial cart items:", cartItems);

      function updateCartBadge() {
        const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
        cartBadge.textContent = totalItems;
        cartBadge.style.display = totalItems > 0 ? "flex" : "none";
        localStorage.setItem("cart", JSON.stringify(cartItems));
      }

      document.addEventListener("click", function (e) {
        if (e.target.classList.contains("dish__add")) {
          const itemData = JSON.parse(e.target.getAttribute("data-item"));
          const existingItem = cartItems.find(
            (item) =>
              item.id === itemData.id && item.portion === itemData.portion
          );

          if (existingItem) {
            existingItem.quantity++;
          } else {
            cartItems.push({
              id: itemData.id,
              name: itemData.name,
              price: itemData.price,
              portion: itemData.portion,
              quantity: 1,
              category: itemData.categoryId,
            });
          }

          updateCartBadge();
          cartBadge.classList.add("bump");
          setTimeout(() => cartBadge.classList.remove("bump"), 300);
        }
      });

      updateCartBadge();
    });