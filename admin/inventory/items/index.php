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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link
      rel="stylesheet"
      type="text/css"
      href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css"
    />
    <script
      type="text/javascript"
      charset="utf8"
      src="https://code.jquery.com/jquery-3.6.0.min.js"
    ></script>
    <script
      type="text/javascript"
      charset="utf8"
      src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"
    ></script>
    <script>
      $(document).ready(function () {
        $("#inventory-table").DataTable({
          // Pagination customization
          paging: true, // Enable pagination (default: true)
          // pagingType: "full_numbers",
          pageLength: 10, // Default number of records per page
          language: {
            paginate: {
              previous: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.9254 4.55806C13.1915 4.80214 13.1915 5.19786 12.9254 5.44194L8.4375 9.55806C8.17138 9.80214 8.17138 10.1979 8.4375 10.4419L12.9254 14.5581C13.1915 14.8021 13.1915 15.1979 12.9254 15.4419C12.6593 15.686 12.2278 15.686 11.9617 15.4419L7.47378 11.3258C6.67541 10.5936 6.67541 9.40641 7.47378 8.67418L11.9617 4.55806C12.2278 4.31398 12.6593 4.31398 12.9254 4.55806Z" fill="#1C1C1C"/></svg>`,
              next: `
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.07459 15.4419C6.80847 15.1979 6.80847 14.8021 7.07459 14.5581L11.5625 10.4419C11.8286 10.1979 11.8286 9.80214 11.5625 9.55806L7.07459 5.44194C6.80847 5.19786 6.80847 4.80214 7.07459 4.55806C7.34072 4.31398 7.77219 4.31398 8.03831 4.55806L12.5262 8.67418C13.3246 9.40641 13.3246 10.5936 12.5262 11.3258L8.03831 15.4419C7.77219 15.686 7.34072 15.686 7.07459 15.4419Z" fill="#1C1C1C"/></svg>
              `,
            },
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            search: "Search:",
            zeroRecords: "No matching records found",
          },
        });
      });
    </script>
    <title></title>
    <link rel="stylesheet" href="../../assets/styles/general.css" />
    <link rel="stylesheet" href="../../assets/styles/panels.css" />
    <link rel="stylesheet" href="../../assets/styles/inventory.css" />

    <style>
  .secondary-button {
    background-color: #6b7280;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  .secondary-button:hover {
    background-color: #4b5563;
  }
</style>
  </head>
  <body class="flex">
    <main>
      <div class="content flex">
        <div class="grid g-af2">
          <div class="card">
            <p>Supply Overview</p>
            <div class="title flex justify-between align-center">
              <h3>1,654</h3>
              <div class="filter">
                <strong class="flex align-center justify-content-xxl-between">
                  This Week
                  <svg class="icon caret"><use href="#caret-down"></use></svg>
                </strong>
                <div class="dropdown"></div>
              </div>
            </div>
            <div>
              <canvas id="supplyChart"></canvas>
            </div>
          </div>
          <div class="card">
            <div class="total flex align-center">
              <h2 id="totalProducts"></h2>
              Products
            </div>
            <div>
              <canvas id="stockChart"></canvas>
            </div>
            <div class="legend flex justify-between align-center">
              <div class="legend-item">
                <div class="flex align-center">
                  <span class="legend-color"></span> In Stock:
                </div>
                <div class="flex align-center">
                  <h1 id="inStock1"></h1>
                  Products
                </div>
              </div>
              <div class="legend-item">
                <div class="flex align-center">
                  <span class="legend-color"></span> In Stock:
                </div>
                <div class="flex align-center">
                  <h1 id="inStock2"></h1>
                  Products
                </div>
              </div>
              <div class="legend-item">
                <div class="flex align-center">
                  <span class="legend-color"></span> In Stock:
                </div>
                <div class="flex align-center">
                  <h1 id="inStock3"></h1>
                  Products
                </div>
              </div>
            </div>
          </div>
          <div class="inventory-overview card table">
            <div class="inventory-actions">
              <div class="tabs">
                <button class="tab active">Inventory</button>
                <a href="../purchase-orders" class="tab">Purchase Order</a>
              </div>
              <div class="search-filter-add">
                <div class="search-bar">
                  <input type="text" placeholder="Search for menu" />
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
                </div>
                <div class="filter-dropdowns">
                  <div class="dropdown">
                    <button class="inventory-dropdown-toggle">
                      All Category
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
                    <div class="dropdown-menu">
                      <a href="#">Food Ingredients</a>
                      <a href="#">Kitchen Tools</a>
                      <a href="#">Other</a>
                    </div>
                  </div>
                  <div class="dropdown">
                    <button class="inventory-dropdown-toggle">
                      All Status
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
                    <div class="dropdown-menu">
                      <a href="#">Available</a>
                      <a href="#">Low</a>
                      <a href="#">Out of Stock</a>
                    </div>
                  </div>
                </div>
                <button class="add-product-button" id="addProduct">
                  + Add Product
                </button>
                <div class="modal-container" id="addModal">
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
 <!-- <form id="add-menu-item-form" action="../../../BackEnd/controller/inventory/add_menu_item.php" method="POST" enctype="multipart/form-data"> -->
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




<script>
  // JavaScript to handle dynamic form behavior
  document.getElementById('has_options').addEventListener('change', function () {
  document.getElementById('options-section').style.display = this.checked ? 'block' : 'none';
  const priceInput = document.getElementById('price');
  if (this.checked) {
    priceInput.value = ''; // Clear the price input
    priceInput.disabled = true; // Disable the price input
    document.getElementById('image').disabled = true; // Disable image for items with options
  } else {
    priceInput.disabled = false; // Enable the price input if unchecked
    document.getElementById('image').disabled = false; // Enable image if unchecked
  }
});

document.getElementById('is_set_menu').addEventListener('change', function () {
  document.getElementById('set-menu-section').style.display = this.checked ? 'block' : 'none';
  const priceInput = document.getElementById('price');
  if (this.checked) {
    priceInput.value = ''; // Clear the price input
    priceInput.disabled = true; // Disable the price input
    document.getElementById('has_options').disabled = true; // Disable has_options checkbox
    document.getElementById('options-section').style.display = 'none'; // Hide options section
    document.getElementById('image').disabled = true; // Disable image for set menus
  } else {
    priceInput.disabled = false; // Enable the price input if unchecked
    document.getElementById('has_options').disabled = false; // Enable has_options checkbox
    document.getElementById('image').disabled = false; // Enable image if unchecked
  }
});
  

  document.getElementById('add-menu-item-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData();
    formData.append('category_id', document.getElementById('category').value);
    formData.append('name', document.getElementById('name').value);
    formData.append('description', document.getElementById('description').value);
    formData.append('price', document.getElementById('price').value || null);
    formData.append('has_options', document.getElementById('has_options').checked);
    formData.append('is_set_menu', document.getElementById('is_set_menu').checked);
    const imageFile = document.getElementById('image').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }

    const options = [];
    let optionCount = document.querySelectorAll('.option-group').length;
    if (formData.get('has_options') === 'true') {
        for (let i = 0; i < optionCount; i++) {
            const portion = document.getElementById(`portion_${i}`)?.value;
            const portionPrice = document.getElementById(`portion_price_${i}`)?.value;
            if (portion && portionPrice) {
                options.push({ portion, price: parseFloat(portionPrice) });
            }
        }
        formData.append('options', JSON.stringify(options));
    }

    if (formData.get('is_set_menu') === 'true') {
        const setMenu = {
            name: document.getElementById('set_menu_name').value,
            price: parseFloat(document.getElementById('set_menu_price').value),
            items: document.getElementById('set_menu_items').value.split(',').map(item => item.trim())
        };
        formData.append('set_menu', JSON.stringify(setMenu));
    }

    for (const [key, value] of formData.entries()) {
  console.log(key, value);
}

    try {
        const response = await fetch('../../../BackEnd/controller/inventory/add_menu_item.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message);
        }
        alert(`Menu item added successfully! Item ID: ${result.item_id || 'N/A'}, Image URL: ${result.image_url || 'None'}`);
        document.getElementById('add-menu-item-form').reset();
        // Reset checkbox-related sections
        document.getElementById('options-section').style.display = 'none';
        document.getElementById('set-menu-section').style.display = 'none';
        document.getElementById('price').disabled = false;
        document.getElementById('image').disabled = false;
        document.getElementById('has_options').disabled = false;
    } catch (error) {
      console.log('Error adding menu item: ' + error);
        alert('Error adding menu item: ' + error.message);
    }
});

// Handle checkbox behavior
document.getElementById('has_options').addEventListener('change', function () {
    document.getElementById('options-section').style.display = this.checked ? 'block' : 'none';
    const priceInput = document.getElementById('price');
    if (this.checked) {
        priceInput.value = ''; // Clear the price input
        priceInput.disabled = true; // Disable the price input
        document.getElementById('image').disabled = true; // Disable image for items with options
    } else {
        priceInput.disabled = false; // Enable the price input if unchecked
        document.getElementById('image').disabled = false; // Enable image if unchecked
    }
});

document.getElementById('is_set_menu').addEventListener('change', function () {
    document.getElementById('set-menu-section').style.display = this.checked ? 'block' : 'none';
    const priceInput = document.getElementById('price');
    if (this.checked) {
        priceInput.value = ''; // Clear the price input
        priceInput.disabled = true; // Disable the price input
        document.getElementById('has_options').disabled = true; // Disable has_options checkbox
        document.getElementById('options-section').style.display = 'none'; // Hide options section
        document.getElementById('image').disabled = true; // Disable image for set menus
    } else {
        priceInput.disabled = false; // Enable the price input if unchecked
        document.getElementById('has_options').disabled = false; // Enable has_options checkbox
        document.getElementById('image').disabled = false; // Enable image if unchecked
    }
});

let optionCount = 1;
document.getElementById('add-option').addEventListener('click', function () {
    const optionGroup = document.createElement('div');
    optionGroup.className = 'form-group option-group';
    optionGroup.innerHTML = `
        <div class="flex justify-between align-center">
            <div class="form-group">
                <label for="portion_${optionCount}" class="form-label">Portion</label>
                <input
                    type="text"
                    id="portion_${optionCount}"
                    placeholder="e.g., 1/4 (6 pancakes)"
                    class="form-input"
                />
            </div>
            <div class="form-group">
                <label for="portion_price_${optionCount}" class="form-label">Price (£)</label>
                <input
                    type="number"
                    id="portion_price_${optionCount}"
                    placeholder="e.g., 13.5"
                    step="0.01"
                    class="form-input"
                />
            </div>
        </div>
    `;
    document.getElementById('options-section').insertBefore(optionGroup, document.getElementById('add-option'));
    optionCount++;
});
</script>



                
                </div>
              </div>
            </div>
            <table id="inventory-table" class="display">
              <thead>
                <tr>
                  <th></th>
                  <th>Item</th>
                  <th>Category</th>
                  <th>Status</th>
                  <th>Qty in Stock</th>
                  <th>Qty in Reorder</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><input type="checkbox" /></td>
                  <td>Fresh Salmon</td>
                  <td>Food Ingredients</td>
                  <td><span class="status available">Available</span></td>
                  <td class="stock-level" data-stock="45" data-reorder="20">
                    <div class="progress-bar-container">
                      <div class="progress-bar" style="width: 69.23%"></div>
                    </div>
                    45
                  </td>
                  <td>20</td>
                  <td class="action">
                    <button class="reorder-button">Reorder</button>
                    <button class="update-stock-button">Update Stock</button>
                  </td>
                </tr>
                <tr>
                  <td><input type="checkbox" /></td>
                  <td>Fresh Salmon</td>
                  <td>Food Ingredients</td>
                  <td><span class="status low">Low</span></td>
                  <td class="stock-level" data-stock="10" data-reorder="50">
                    <div class="progress-bar-container">
                      <div class="progress-bar low" style="width: 16.67%"></div>
                    </div>
                    10
                  </td>
                  <td>50</td>
                  <td class="action">
                    <button class="reorder-button">Reorder</button>
                    <button class="update-stock-button">Update Stock</button>
                  </td>
                </tr>
              </tbody>
            </table>

            <div id="action-modal">
              <div class="modal-header">
                <h2 class="modal-title">Update Item</h2>
                <button id="modal-close-button" class="modal-close-button">
                  &times;
                </button>
              </div>
              <form id="modal-form" class="modal-form">
                <div class="modal-form-group">
                  <label for="modal-item-name" class="modal-form-label"
                    >Item Name</label
                  >
                  <input
                    type="text"
                    id="modal-item-name"
                    class="modal-form-input"
                    readonly
                  />
                </div>
                <div class="modal-form-group">
                  <label for="modal-new-stock" class="modal-form-label"
                    >New Stock Quantity</label
                  >
                  <input
                    type="number"
                    id="modal-new-stock"
                    class="modal-form-input"
                  />
                </div>
                <div class="modal-actions">
                  <button type="submit" class="modal-action-button">
                    Update
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
     <script>
// Pass PHP variables to JavaScript
const username = '<?php echo addslashes($first_name); ?>';
const userRole = '<?php echo addslashes($userRole); ?>';
const profilePicture = '<?php echo addslashes($profilePicture); ?>';
</script>
    <script src="../../scripts/components.js"></script>
    <script src="../../scripts/inventory.js"></script>
  </body>
</html>




