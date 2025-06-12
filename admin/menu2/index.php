<?php
session_start();

if (!isset($_SESSION['user']['id']) || !isset($_SESSION['user']['role'])) {
    header("Location: /chinnese-restaurant/login/");
    exit();
}



$username = $_SESSION['user']['name'] ?? '';
// var_dump($username); // Debug: Check session data 

$parts = explode(" ", $username);
$first_name = $parts[0];

$userRole = $_SESSION['user']['role'] ?? '';
$profilePicture = $_SESSION['user']['profile_picture'] ?? 'https://picsum.photos/40';

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Billing Alerts</title>
    <link rel="stylesheet" href="../assets/styles/general.css" />
    <link rel="stylesheet" href="../assets/styles/panels.css" />
    <link rel="stylesheet" href="../assets/styles/menu.css" />
    <link rel="stylesheet" href="../assets/styles/inventory.css" />
    <style>
     
      .modal-container {
    background-color: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    padding: 1rem;
    width: 100%;
    max-width: 23rem;
    margin: auto;
    position: fixed;
    right: 0; 
    top:20%;
    z-index: 10;
    display: none;
    opacity:  unset!important;
    visibility: unset!important;
    transition: all 0.3s;
    pointer-events: unset!important;
}
      .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
      }
      .add-product-button {
        margin-left: auto;
      }
      /* Basic form styling for better appearance */
      .form-group {
        margin-bottom: 15px;
      }
      .form-label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
      }
      .form-input,
      .select-wrapper select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
      }
      .secondary-button {
        background-color: #6c757d;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
      }
      .primary-button {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
      }
      .form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
      }
      .close-button {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
      }
      .close-button svg {
        width: 24px;
        height: 24px;
      }

      .modal-container.active {
  display: flex !important;
}
form{
  z-index: 1000;
}

    </style>
  </head>
  <body class="flex">
    <main>
      <div class="content flex">
        <div class="inner-content card">
          <div class="all-menu">
            <div class="menu-controls">
              <div class="flex align-center justify-between">
                <h6>All Menu</h6>
                <div class="menu-actions">
                  <button class="add-product-button" id="addProduct">
                    + Add Product
                  </button>
                  <div class="search-bar">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="feather feather-search"
                    >
                      <circle cx="11" cy="11" r="8"></circle>
                      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input
                      type="text"
                      placeholder="Search for menu"
                      id="search-input"
                    />
                  </div>
                  <button class="search-button" id="search-button">
                    Search
                  </button>
                  <button class="filter-button">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="feather feather-filter"
                    >
                      <polygon
                        points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"
                      ></polygon>
                    </svg>
                    Filter
                  </button>
                  <button class="grid-view">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="feather feather-grid"
                    >
                      <rect x="3" y="3" width="7" height="7"></rect>
                      <rect x="14" y="3" width="7" height="7"></rect>
                      <rect x="3" y="14" width="7" height="7"></rect>
                      <rect x="14" y="14" width="7" height="7"></rect>
                    </svg>
                  </button>
                </div>
              </div>
              <div class="flex justify-between align-center">
                <div class="category-tabs">
                  <button class="tab active" data-category="all">All</button>
                  <button class="tab" data-category="chicken">
                    Chicken Dishes
                  </button>
                  <button class="tab" data-category="soups">Soups</button>
                  <button class="tab" data-category="noodles">
                    Noodles & Chow Mein
                  </button>
                  <button class="tab" data-category="rice">Rice Dishes</button>
                </div>
                <div class="sort-options">
                  <span>Sort by:</span>
                  <button class="sort-dropdown" id="sort-dropdown">
                    Popular
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="feather feather-chevron-down"
                    >
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </button>
                  <div class="sort-dropdown-menu" style="display: none">
                    <a href="#" data-sort="popular">Popular</a>
                    <a href="#" data-sort="name">Name</a>
                    <a href="#" data-sort="health">Health Score</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="menu-items-container"></div>
          </div>
          <div class="modal-container" id="addModal">
            <div class="modal-content">
              <div class="modal-header">
                <h2 class="modal-title">Add Product</h2>
                <button id="close-modal" class="close-button">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div>
              <form id="add-menu-item-form" enctype="multipart/form-data" method="POST">
                <div class="form-group">
                  <label for="category" class="form-label">Category</label>
                  <div class="select-wrapper">
                    <select id="category" name="category_id" class="form-input" required>
                      <option value="">Select Category</option>
                      <option value="1">STARTERS</option>
                      <option value="2">SOUP</option>
                      <option value="3">CHICKEN</option>
                      <option value="4">BEEF</option>
                      <option value="5">LAMB</option>
                      <option value="6">PORK & ROAST PORK</option>
                      <option value="7">DUCK</option>
                      <option value="8">SEAFOOD</option>
                      <option value="9">KING PRAWNS</option>
                      <option value="10">VEGETABLES</option>
                      <option value="11">CHOP SUEY (BEANSPROUTS)</option>
                      <option value="12">SWEET & SOUR</option>
                      <option value="13">CURRY</option>
                      <option value="14">RICE</option>
                      <option value="15">NOODLES</option>
                      <option value="16">NOODLE SOUP</option>
                      <option value="17">OMELETTE</option>
                      <option value="18">EXTRAS & DESSERTS</option>
                      <option value="19">SPECIAL HOUSE MEALS</option>
                      <option value="20">SET MENU</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="name" class="form-label">Item Name</label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="e.g., Crispy Seaweed"
                    class="form-input"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="description" class="form-label">Description (Optional)</label>
                  <textarea
                    id="description"
                    name="description"
                    placeholder="e.g., With Sweet & Sour Sauce"
                    class="form-input"
                  ></textarea>
                </div>
                <div class="form-group">
                  <label for="price" class="form-label">Price (£)</label>
                  <input
                    type="number"
                    id="price"
                    name="basePrice"
                    placeholder="e.g., 5.5"
                    step="0.01"
                    class="form-input"
                  />
                </div>
                <div class="form-group">
                  <label for="image" class="form-label">Item Image (Optional)</label>
                  <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/*"
                    class="form-input"
                  />
                </div>
                <div class="form-group">
                  <label class="form-label">
                    <input type="checkbox" id="has_options" name="has_options" value="1" />
                    Has Portion Options (e.g., 1/4, 1/2, Whole)
                  </label>
                </div>
                <div id="options-section" style="display: none;">
                  <div class="form-group option-group">
                    <div class="flex justify-between align-center">
                      <div class="form-group">
                        <label for="portion_0" class="form-label">Portion</label>
                        <input
                          type="text"
                          id="portion_0"
                          name="options[0][portion]"
                          placeholder="e.g., 1/4 (6 pancakes)"
                          class="form-input"
                        />
                      </div>
                      <div class="form-group">
                        <label for="portion_price_0" class="form-label">Price (£)</label>
                        <input
                          type="number"
                          id="portion_price_0"
                          name="options[0][price]"
                          placeholder="e.g., 13.5"
                          step="0.01"
                          class="form-input"
                        />
                      </div>
                    </div>
                  </div>
                  <button type="button" id="add-option" class="secondary-button">Add Another Option</button>
                </div>
                <div class="form-group">
                  <label class="form-label">
                    <input type="checkbox" id="is_set_menu" name="is_set_menu" value="1" />
                    Is Set Menu (e.g., Menu for 1 Person)
                  </label>
                </div>
                <div id="set-menu-section" style="display: none;">
                  <div class="form-group">
                    <label for="set_menu_name" class="form-label">Set Menu Name</label>
                    <input
                      type="text"
                      id="set_menu_name"
                      name="set_menu[name]"
                      placeholder="e.g., Menu F - For 4 Persons"
                      class="form-input"
                    />
                  </div>
                  <div class="form-group">
                    <label for="set_menu_price" class="form-label">Set Menu Price (£)</label>
                    <input
                      type="number"
                      id="set_menu_price"
                      name="set_menu[price]"
                      placeholder="e.g., 60.0"
                      step="0.01"
                      class="form-input"
                    />
                  </div>
                  <div class="form-group">
                    <label for="set_menu_items" class="form-label">Set Menu Items</label>
                    <textarea
                      id="set_menu_items"
                      name="set_menu[items]"
                      placeholder="e.g., Crispy Seaweed, Chicken Curry"
                      class="form-input"
                    ></textarea>
                  </div>
                </div>
                <div class="form-actions">
                  <button type="submit" class="primary-button">Add Menu Item</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
    <script>
      const username = '<?php echo addslashes($first_name); ?>';
const userRole = '<?php echo addslashes($userRole); ?>';
const profilePicture = '<?php echo addslashes($profilePicture); ?>';
    </script>
    <script src="../scripts/components.js"></script>
    <script src="../scripts/menu.js"></script>
   

    <script>
      document.addEventListener('DOMContentLoaded', () => {
  // Cache DOM elements
  const elements = {
    addProduct: document.getElementById('addProduct'),
    addModal: document.getElementById('addModal'),
    closeModal: document.getElementById('close-modal'),
    form: document.getElementById('add-menu-item-form'),
    category: document.getElementById('category'),
    name: document.getElementById('name'),
    price: document.getElementById('price'),
    image: document.getElementById('image'),
    hasOptions: document.getElementById('has_options'),
    optionsSection: document.getElementById('options-section'),
    addOption: document.getElementById('add-option'),
    isSetMenu: document.getElementById('is_set_menu'),
    setMenuSection: document.getElementById('set-menu-section'),
    sortDropdown: document.getElementById('sort-dropdown'),
    sortDropdownMenu: document.querySelector('.sort-dropdown-menu'),
  };

  // Ensure modal is hidden initially
  elements.addModal.classList.remove('active');

  // Modal visibility
  elements.addProduct.addEventListener('click', (e) => {
    e.stopPropagation();
    console.log('Add Product clicked');
    console.log('DOM addModal cached:', elements.addModal);
    console.log('DOM elements cached:', elements.form);
    elements.addModal.classList.add('active');
    console.log('Modal classList:', elements.addModal.classList);
    console.log('Modal computed display:', window.getComputedStyle(elements.addModal).display);
  });
  elements.closeModal.addEventListener('click', () => {
    elements.addModal.classList.remove('active');
  });
  elements.addModal.addEventListener('click', (e) => {
    if (e.target === elements.addModal) {
      elements.addModal.classList.remove('active');
    }
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && elements.addModal.classList.contains('active')) {
      elements.addModal.classList.remove('active');
    }
  });

  // Sort dropdown toggle
  elements.sortDropdown.addEventListener('click', () => {
    elements.sortDropdownMenu.style.display =
      elements.sortDropdownMenu.style.display === 'none' || elements.sortDropdownMenu.style.display === ''
        ? 'block'
        : 'none';
  });
  document.addEventListener('click', (e) => {
    if (!elements.sortDropdown.contains(e.target) && !elements.sortDropdownMenu.contains(e.target)) {
      elements.sortDropdownMenu.style.display = 'none';
    }
  });

  // Dynamic form behavior: Has Portion Options
  elements.hasOptions.addEventListener('change', function () {
    elements.optionsSection.style.display = this.checked ? 'block' : 'none';
    elements.price.disabled = this.checked;
    elements.image.disabled = this.checked;
    if (this.checked) {
      elements.price.value = '';
      elements.isSetMenu.disabled = true;
      elements.setMenuSection.style.display = 'none';
    } else {
      elements.price.disabled = false;
      elements.image.disabled = false;
      elements.isSetMenu.disabled = false;
    }
  });

  // Dynamic form behavior: Is Set Menu
  elements.isSetMenu.addEventListener('change', function () {
    elements.setMenuSection.style.display = this.checked ? 'block' : 'none';
    elements.price.disabled = this.checked;
    elements.image.disabled = this.checked;
    if (this.checked) {
      elements.price.value = '';
      elements.hasOptions.disabled = true;
      elements.optionsSection.style.display = 'none';
    } else {
      elements.price.disabled = false;
      elements.image.disabled = false;
      elements.hasOptions.disabled = false;
    }
  });

  // Add portion options dynamically
  let optionCount = 1;
  elements.addOption.addEventListener('click', () => {
    const optionGroup = document.createElement('div');
    optionGroup.className = 'form-group option-group';
    optionGroup.innerHTML = `
      <div class="flex justify-between align-center">
        <div class="form-group">
          <label for="portion_${optionCount}" class="form-label">Portion</label>
          <input
            type="text"
            id="portion_${optionCount}"
            name="options[${optionCount}][portion]"
            placeholder="e.g., 1/4 (6 pancakes)"
            class="form-input"
            required
          />
        </div>
        <div class="form-group">
          <label for="portion_price_${optionCount}" class="form-label">Price (£)</label>
          <input
            type="number"
            id="portion_price_${optionCount}"
            name="options[${optionCount}][price]"
            placeholder="e.g., 13.5"
            step="0.01"
            class="form-input"
            required
          />
        </div>
        <button type="button" class="remove-option secondary-button" style="margin-top: 20px;">Remove</button>
      </div>
    `;
    elements.optionsSection.insertBefore(optionGroup, elements.addOption);
    optionGroup.querySelector('.remove-option').addEventListener('click', () => optionGroup.remove());
    optionCount++;
  });

  // Form submission
  elements.form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitButton = e.target.querySelector('.primary-button');
    submitButton.disabled = true;
    submitButton.textContent = 'Adding...';

    // Validation
    if (!elements.category.value) {
      alert('Please select a category.');
      submitButton.disabled = false;
      submitButton.textContent = 'Add Menu Item';
      return;
    }
    if (!elements.name.value) {
      alert('Please enter an item name.');
      submitButton.disabled = false;
      submitButton.textContent = 'Add Menu Item';
      return;
    }
    if (!elements.hasOptions.checked && !elements.isSetMenu.checked && !elements.price.value) {
      alert('Please enter a price or select portion options/set menu.');
      submitButton.disabled = false;
      submitButton.textContent = 'Add Menu Item';
      return;
    }

    const formData = new FormData();
    formData.append('category_id', elements.category.value);
    formData.append('name', sanitizeInput(elements.name.value));
    formData.append('description', sanitizeInput(document.getElementById('description').value));
    if (!elements.hasOptions.checked && !elements.isSetMenu.checked) {
      formData.append('price', elements.price.value ? parseFloat(elements.price.value) : '');
    } else {
      formData.append('price', '');
    }
    formData.append('has_options', elements.hasOptions.checked ? '1' : '0');
    formData.append('is_set_menu', elements.isSetMenu.checked ? '1' : '0');
    if (elements.image.files[0] && !elements.hasOptions.checked && !elements.isSetMenu.checked) {
      formData.append('image', elements.image.files[0]);
    }

    if (elements.hasOptions.checked) {
      const options = [];
      const optionGroups = document.querySelectorAll('.option-group');
      optionGroups.forEach((group, i) => {
        const portionInput = group.querySelector(`[name="options[${i}][portion]"]`);
        const priceInput = group.querySelector(`[name="options[${i}][price]"]`);
        const portion = portionInput?.value;
        const price = priceInput ? parseFloat(priceInput.value) : '';
        if (portion && !isNaN(price) && price !== '') {
          options.push({ portion, price });
        }
      });
      if (options.length === 0) {
        alert('Please add at least one valid portion option (portion and price).');
        submitButton.disabled = false;
        submitButton.textContent = 'Add Menu Item';
        return;
      }
      formData.append('options', JSON.stringify(options));
    }

    if (elements.isSetMenu.checked) {
      const setMenu = {
        name: sanitizeInput(document.getElementById('set_menu_name').value),
        price: parseFloat(document.getElementById('set_menu_price').value),
        items: document.getElementById('set_menu_items')
          .value.split(',')
          .map(item => sanitizeInput(item.trim()))
          .filter(item => item !== ''),
      };
      if (!setMenu.name || isNaN(setMenu.price) || setMenu.items.length === 0) {
        alert('Please fill all set menu fields including name, price, and at least one item.');
        submitButton.disabled = false;
        submitButton.textContent = 'Add Menu Item';
        return;
      }
      formData.append('set_menu', JSON.stringify(setMenu));
    }

    for (const [key, value] of formData.entries()) {
      console.log(key, value);
    }

    try {
      const response = await fetch('../../BackEnd/controller/inventory/add_menu_item.php', {
        method: 'POST',
        body: formData,
      });
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      const result = await response.json();
      if (!result.success) {
        throw new Error(result.message || 'Unknown error');
      }
      alert(`Menu item added successfully! Item ID: ${result.item_id || 'N/A'}, Image URL: ${result.image_url || 'None'}`);
      elements.form.reset();
      elements.optionsSection.style.display = 'none';
      elements.setMenuSection.style.display = 'none';
      elements.price.disabled = false;
      elements.image.disabled = false;
      elements.hasOptions.disabled = false;
      elements.isSetMenu.disabled = false;
      elements.addModal.classList.remove('active');
      const currentOptionGroups = document.querySelectorAll('.option-group');
      currentOptionGroups.forEach((group, index) => {
        if (index > 0) group.remove();
      });
      optionCount = 1;
    } catch (error) {
      console.error('Error adding menu item:', error);
      alert(`Error adding menu item: ${error.message}`);
    } finally {
      submitButton.disabled = false;
      submitButton.textContent = 'Add Menu Item';
    }
  });

  // Input sanitization
  const sanitizeInput = (input) => {
    return input.trim().replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
  };
});
    </script>
  </body>
</html>