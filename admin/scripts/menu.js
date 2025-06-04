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
  let allMenuItems = [
    {
      id: 1,
      name: "Avocado Toast with Poached Egg",
      category: "toast",
      difficulty: "easy",
      healthScore: 9,
      kcal: 320,
      carbs: 30,
      protein: 14,
      fats: 18,
      image: "https://picsum.photos/300?random=5",
    },
    {
      id: 2,
      name: "Grilled Shrimp Tacos with Mango Salsa",
      category: "shrimp",
      difficulty: "medium",
      healthScore: 8,
      kcal: 400,
      carbs: 45,
      protein: 28,
      fats: 12,
      image: "https://picsum.photos/300?random=6",
    },
    {
      id: 3,
      name: "Baked Chicken Breast with Quinoa and Kale",
      category: "chicken",
      difficulty: "medium",
      healthScore: 9,
      kcal: 480,
      carbs: 50,
      protein: 40,
      fats: 15,
      image: "https://picsum.photos/300?random=7",
    },
    {
      id: 4,
      name: "Chicken Noodle Soup",
      category: "soups",
      difficulty: "easy",
      healthScore: 7,
      kcal: 250,
      carbs: 20,
      protein: 25,
      fats: 10,
      image: "https://picsum.photos/300?random=8",
    },
    {
      id: 5,
      name: "Vegetable Chow Mein",
      category: "noodles",
      difficulty: "medium",
      healthScore: 8,
      kcal: 380,
      carbs: 55,
      protein: 15,
      fats: 12,
      image: "https://picsum.photos/300?random=9",
    },
    {
      id: 6,
      name: "Salmon Rice Bowl",
      category: "rice",
      difficulty: "easy",
      healthScore: 9,
      kcal: 420,
      carbs: 40,
      protein: 30,
      fats: 18,
      image: "https://picsum.photos/300?random=10",
    },
  ];

  function renderMenuItems(items) {
    menuItemsContainer.innerHTML = "";
    items.forEach((item) => {
      const card = document.createElement("div");
      card.classList.add("menu-item-card", "cream-card", "card");
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
            <span class="nutrition-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-leaf"><path d="M18.6 13.4c-.9 1.5-2.4 2.6-4.1 3.2l-6.2 2.5a2 2 0 0 1-2.3-2.3l2.5-6.2c.6-1.7 1.7-3.2 3.2-4.1 2.1-1.3 4.7-1.3 6.8 0 1.5.9 2.6 2.4 3.2 4.1z"></path><path d="M12 12l.5-3.2"></path><path d="M15 15l-3.2-.5"></path></svg> ${
              item.carbs
            }g carbs</span>
            <span class="nutrition-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-zap"><polygon points="13 2 3 14 12 15 11 22 21 10 12 9 13 2"></polygon></svg> ${
              item.protein
            }g protein</span>
            <span class="nutrition-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-egg"><path d="M13 6c0-3.5-5-3.5-5 0 0 2.1.8 4 2 5.3 1.2 1.5 3 2.2 5 2.2s3.8-.7 5-2.2c1.2-1.3 2-3.2 2-5.3 0-3.5-5-3.5-5 0"></path></svg> ${
              item.fats
            }g fats</span>
          </div>
          <button class="edit-menu-button">Edit Menu</button>
          </div>
        </div>
      `;
      menuItemsContainer.appendChild(card);
    });
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
      // In a real scenario, you might have a 'popularity' property
      // For now, we'll just keep the original order or sort by ID
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

  // Initial rendering
  renderMenuItems(allMenuItems);
});
