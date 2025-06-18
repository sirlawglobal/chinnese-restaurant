document.addEventListener("DOMContentLoaded", () => {
  const menuItemsContainer = document.querySelector(".menu-items-container");
  const categoryTabs = document.querySelectorAll(".category-tabs .tab");
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const sortDropdown = document.getElementById("sort-dropdown");
  const sortDropdownMenu = document.querySelector(".sort-dropdown-menu");
  const sortLinks = sortDropdownMenu.querySelectorAll("a");

  let currentCategory = "all";
  let searchTerm = "";
  let sortOption = "popular";
  let allMenuItems = [];

  // Static template for menu items (including price)
  const staticMenuItemTemplates = [
    {
      id: 1,
      category: "toast",
      difficulty: "easy",
      healthScore: 9,
      kcal: 320,
      carbs: 30,
      protein: 14,
      fats: 18,
      price: 5.99,
    },
    {
      id: 2,
      category: "shrimp",
      difficulty: "medium",
      healthScore: 8,
      kcal: 400,
      carbs: 45,
      protein: 28,
      fats: 12,
      price: 12.99,
    },
    {
      id: 3,
      category: "chicken",
      difficulty: "medium",
      healthScore: 9,
      kcal: 480,
      carbs: 50,
      protein: 40,
      fats: 15,
      price: 9.99,
    },
    {
      id: 4,
      category: "soups",
      difficulty: "easy",
      healthScore: 7,
      kcal: 250,
      carbs: 20,
      protein: 25,
      fats: 10,
      price: 6.99,
    },
    {
      id: 5,
      category: "noodles",
      difficulty: "medium",
      healthScore: 8,
      kcal: 380,
      carbs: 55,
      protein: 15,
      fats: 12,
      price: 8.99,
    },
    {
      id: 6,
      category: "rice",
      difficulty: "easy",
      healthScore: 9,
      kcal: 420,
      carbs: 40,
      protein: 30,
      fats: 18,
      price: 7.99,
    },
  ];

  // Fetch menu items from the database
  async function fetchMenuItems() {
    try {
      const response = await fetch('../menu2/get_menu_items.php');
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      const dbItems = await response.json();
      
      allMenuItems = dbItems.map((dbItem, index) => {
        console.log('DB Item:', dbItem);
        const staticTemplate = staticMenuItemTemplates[index % staticMenuItemTemplates.length];
        // Ensure price is a number, fallback to static template price or 0
        const price = parseFloat(dbItem.price) || staticTemplate.price || 0;
        return {
          id: dbItem.id,
          name: dbItem.name || `Placeholder Item ${index + 1}`,
          image: dbItem.image_url || '../../avarterdefault.jpg',
          category: staticTemplate.category,
          difficulty: staticTemplate.difficulty,
          healthScore: staticTemplate.healthScore,
          kcal: staticTemplate.kcal,
          carbs: staticTemplate.carbs,
          protein: staticTemplate.protein,
          fats: staticTemplate.fats,
          price: isNaN(price) ? staticTemplate.price : price, // Ensure price is valid
        };
      });

      filterMenuItems();
    } catch (error) {
      console.error('Error fetching menu items:', error);
      allMenuItems = staticMenuItemTemplates.map((item, index) => ({
        ...item,
        name: `Placeholder Item ${index + 1}`,
        image: `https://picsum.photos/300?random=${index + 5}`,
      }));
      filterMenuItems();
    }
  }

  function renderMenuItems(items) {
    menuItemsContainer.innerHTML = "";
    items.forEach((item) => {
      const card = document.createElement("div");
      card.classList.add("menu-item-card", "cream-card", "card");
      // Ensure price is a number before calling toFixed
      const price = isNaN(parseFloat(item.price)) ? 0 : parseFloat(item.price);
      card.innerHTML = `
        <div class="item-image">
          <img src="${item.image}" alt="${item.name}">
        </div>
        <div class="item-details">
          <div class="item-header">
            <div class="flex align-center">
              <span class="item-category ${item.category}">${
                item.category.charAt(0).toUpperCase() + item.category.slice(1)
              }</span>
              <span class="item-difficulty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.92-9.43"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> ${
                item.difficulty.charAt(0).toUpperCase() + item.difficulty.slice(1)
              }</span>
            </div>
            <div class="health-score">
              <span>Health Score:</span>
              <span class="score">${item.healthScore}/10</span>
              <span class="progress-bar">
                <span style="width: ${item.healthScore * 10}%;"></span>
              </span>
            </div>
          </div>
          <h3 class="item-name">${item.name}</h3>
          <div class="flex justify-between align-center">
            <div class="nutrition-info">
              <span class="nutrition-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="17" y1="5" x2="19" y2="7"></line><line x1="7" y1="5" x2="5" y2="7"></line><line x1="17" y1="17" x2="19" y2="19"></line><line x1="7" y1="17" x2="5" y2="19"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="1" y1="12" x2="3" y2="12"></line></svg> ${
                item.kcal
              } kcal</span>
              <span class="nutrition-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-leaf"><path d="M18.6 13.4c-.9 1.5-2.4 2.6-4.1 3.2l-6.2 2.5a2 2 0 0 1-2.3-.1c-2.1-.2-2.2-2-2.2-2.2l2.5-6.2c.3-1.7-.7-3.2-2.2-4.1-2.4-1.3 0 0 0 0z"></path><path d="M12 0l.5-3.2"></path><path d="M15 3l-3.2-.5"></path></svg> ${
                item.carbs
              }g carbs</span>
              <span class="nutrition-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-zap"><polygon points="13 2 3 14 12 15 11 22 21 10 12 9 13 2"></polygon></svg> ${
                item.protein
              }g protein</span>
              <span class="nutrition-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-egg"><path d="M13 6c0-3.5-5-3.5-5 0 0 2.1.8 4 2 5.3 1.2 1.5 3 2.2 5 2.2s3.8-.7 5-2.2c1.2-1.3 2-3.2 2-5.3 0-3.5-5-3.5-5 0"></path></svg> ${
                item.fats
              }g fats</span>
              <span class="nutrition-item">Price: $${price.toFixed(2)}</span>
            </div>
            <button class="edit-menu-button" data-id="${item.id}">Edit Menu</button>
          </div>
        </div>
      `;
      menuItemsContainer.appendChild(card);
    });

    // Add event listeners to edit buttons
    document.querySelectorAll(".edit-menu-button").forEach((button) => {
      button.addEventListener("click", (e) => {
        const itemId = e.target.dataset.id;
        const item = allMenuItems.find((item) => item.id == itemId);
        showEditForm(item);
      });
    });
  }

  // Create and manage the edit form modal
  function showEditForm(item) {
    let modal = document.getElementById("edit-menu-modal");
    if (!modal) {
      modal = document.createElement("div");
      modal.id = "edit-menu-modal";
      modal.classList.add("modal");
      modal.innerHTML = `
        <div class="modal-content">
          <span class="close-button">×</span>
          <h2>Edit Menu Item</h2>
          <form id="edit-menu-form">
            <div class="form-group">
              <label for="edit-name">Name:</label>
              <input type="text" id="edit-name" required>
            </div>
            <div class="form-group">
              <label for="edit-price">Price ($):</label>
              <input type="number" id="edit-price" step="0.01" min="0" required>
            </div>
            <input type="hidden" id="edit-id">
            <button type="submit">Save Changes</button>
          </form>
        </div>
      `;
      document.body.appendChild(modal);
    }

    // Populate form
    const form = modal.querySelector("#edit-menu-form");
    form.querySelector("#edit-name").value = item.name;
    // Ensure price is a number, fallback to 0 if invalid
    const price = isNaN(parseFloat(item.price)) ? 0 : parseFloat(item.price);
    form.querySelector("#edit-price").value = price.toFixed(2);
    form.querySelector("#edit-id").value = item.id;

    // Show modal
    modal.style.display = "block";

    // Close modal
    modal.querySelector(".close-button").onclick = () => {
      modal.style.display = "none";
    };

    // Handle form submission
    form.onsubmit = async (e) => {
      e.preventDefault();
      const updatedItem = {
        id: form.querySelector("#edit-id").value,
        name: form.querySelector("#edit-name").value,
        price: parseFloat(form.querySelector("#edit-price").value) || 0,
      };

      try {
        const response = await fetch('../menu2/update_menu_item.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(updatedItem),
        });
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        // Update local data
        const index = allMenuItems.findIndex((item) => item.id == updatedItem.id);
        if (index !== -1) {
          allMenuItems[index].name = updatedItem.name;
          allMenuItems[index].price = updatedItem.price;
        }
        filterMenuItems();
        modal.style.display = "none";
      } catch (error) {
        console.error('Error updating menu item:', error);
        alert('Failed to update menu item. Please try again.');
      }
    };
  }

  function filterMenuItems() {
    const filteredByCategory =
      currentCategory === "all"
        ? allMenuItems
        : allMenuItems.filter((item) => item.category === currentCategory);

    const filteredBySearch = searchTerm
      ? filteredByCategory.filter((item) =>
          item.name.toLowerCase().includes(searchTerm.toLowerCase())
        )
      : filteredByCategory;

    let sortedItems = [...filteredBySearch];
    if (sortOption === "name") {
      sortedItems.sort((a, b) => a.name.localeCompare(b.name));
    } else if (sortOption === "health") {
      sortedItems.sort((a, b) => b.healthScore - a.healthScore);
    } else if (sortOption === "popular") {
      sortedItems.sort((a, b) => a.id - b.id);
    }

    renderMenuItems(sortedItems);
  }

  categoryTabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      categoryTabs.forEach((t) => t.classList.remove("active"));
      this.classList.add("active");
      currentCategory = this.dataset.category;
      filterMenuItems();
    });
  });

  searchButton.addEventListener("click", () => {
    searchTerm = searchInput.value;
    filterMenuItems();
  });

  searchInput.addEventListener("keypress", (event) => {
    if (event.key === "Enter") {
      searchTerm = searchInput.value;
      filterMenuItems();
    }
  });

  sortDropdown.addEventListener("click", () => {
    sortDropdownMenu.style.display =
      sortDropdownMenu.style.display === "block" ? "none" : "block";
  });

  sortLinks.forEach((link) => {
    link.addEventListener("click", function (event) {
      event.preventDefault();
      sortOption = this.dataset.sort;
      sortDropdown.textContent = this.textContent;
      sortDropdownMenu.style.display = "none";
      filterMenuItems();
    });
  });

  fetchMenuItems();
});

// CSS for modal
const style = document.createElement('style');
style.innerHTML = `
  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
  }
  .modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
  }
  .close-button {
    float: right;
    font-size: 24px;
    cursor: pointer;
  }
  .form-group {
    margin-bottom: 15px;
  }
  .form-group label {
    display: block;
    margin-bottom: 5px;
  }
  .form-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  .modal-content button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  .modal-content button:hover {
    background-color: #45a049;
  }
`;
document.head.appendChild(style);