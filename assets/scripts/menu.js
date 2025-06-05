    // Helper to decode HTML entities
    function decodeHTML(html) {
      const txt = document.createElement("textarea");
      txt.innerHTML = html;
      return txt.value;
    }

    document.addEventListener("DOMContentLoaded", function () {
      fetch("/chinnese-restaurant/BackEnd/controller/inventory/get_menu.php")
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          // console.log("Menu data loaded successfully:", data);
          // console.log("Categories:", data.data.categories);
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

          // Create navigation items
          data.categories.forEach((category, index) => {
            console.log("Category111:", category);
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

//           // Find the category with name "starter" (case insensitive)
// const starterCategory = data.categories.find(cat => cat.name.toLowerCase() === "starter");

// // If found, select it; otherwise fallback to first category
// const initialCategory = starterCategory || data.categories[0];

// if (initialCategory) {
//   // Set active nav item for the selected category
//   const navItems = navList.querySelectorAll(".nav__item");
//   navItems.forEach((navItem) => {
//     if (navItem.textContent.toLowerCase() === initialCategory.name.toLowerCase()) {
//       navItem.classList.add("nav__item--active");
//     } else {
//       navItem.classList.remove("nav__item--active");
//     }
//   });

//   // Render dishes for the starter category or fallback
//   renderDishes(initialCategory);
// }


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
  console.log("Rendering item:", item);

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

  // Decode and display description (handles JSON array or string)
  let descriptionHTML = "";
if (item.description) {
  const decodedDesc = decodeHTML(item.description);
  console.log("Decoded description:", decodedDesc);

  // Try to extract the array part inside brackets
  const match = decodedDesc.match(/\[(.*)\]/);
  if (match) {
    // Extracted content inside brackets, e.g.:
    // ""Crispy Seaweed"",""Sesame Prawn on Toast"",""Kung Po Chicken"", ...
    let arrayStr = match[0]; // includes brackets

    // Fix doubled double-quotes by replacing "" with "
    arrayStr = arrayStr.replace(/""/g, '"');

    try {
      const parsedArray = JSON.parse(arrayStr);
      // Render as list
      descriptionHTML =
        `<ul class="dish__desc-list">` +
        parsedArray.map((item) => `<li>${item}</li>`).join("") +
        `</ul>`;
    } catch (e) {
      console.error("Error parsing cleaned array string:", e);
      descriptionHTML = `<p class="dish__vendor">${decodedDesc}</p>`;
    }
  } else {
    // No array found, just display as is
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


 const backendUploadsUrl = "/chinese-food/BackEnd"; // adjust if needed

const imageSrc = item.image_url
  ? backendUploadsUrl + item.image_url
  : "/chinese-food/avarterdefault.jpg";


dishCard.innerHTML = `
  <div class="dish__image">
     <img src="${imageSrc}" alt="${item.name}" />
  </div>
  <div class="dish__details">
    <h3 class="dish__name">${item.name}</h3>
    ${descriptionHTML}
    <div class="dish__info">
      ${optionsHTML || `
        <span class="dish__price">
          <svg class="icon"><use href="#tag"></use></svg>
          £${itemPrice.toFixed(2)}
        </span>`}
    </div>
  </div>
  <div class="dish__button">
    <button class="dish__add" data-item='${JSON.stringify({
      id: item.id,
      name: item.name,
      price: itemPrice,
      portion: itemPortion,
        categoryId: item.category_id,
    })}'>+</button>
  </div>
`;


  dishesGrid.appendChild(dishCard);
});

        }
        })
        .catch((error) => {
          console.error("Error loading menu data:", error);
          // Fallback to static data
          fetch("../assets/data/menu.json")
            .then((response) => response.json())
            .then((data) => {
              console.log("Using fallback static data");
              // You can call renderDishes here as well if needed
            });
        });

      // CART FUNCTIONALITY
      const cartButton = document.querySelector(".button--cart");
      const cartBadge = document.createElement("span");
      cartBadge.className = "cart-badge";
      cartBadge.textContent = "0";
      cartButton.appendChild(cartBadge);

      let cartItems = JSON.parse(localStorage.getItem("cart")) || [];

      function updateCartBadge() {

        console.log('cartItems', cartItems);
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
      })

      updateCartBadge();
    });
