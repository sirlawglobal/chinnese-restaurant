<?php
require_once __DIR__ . '/../../../BackEnd/config/init.php';
requireAdmin();
$categories = getCategories(); // returns array of associative arrays by default
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
<style>
  .inventory-dropdown-toggle {
  padding: 8px 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: white;
  cursor: pointer;
  width: 150px;
}
     .modal {
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  padding: 20px;
  border-radius: 8px;
  z-index: 1000;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
}
.modal-content {
  position: relative;
}
.modal-close-button {
  background: transparent;
  border: none;
  font-size: 24px;
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
}
#modal-backdrop {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 999;
}
.modal.show, #modal-backdrop.show {
  display: block;
}
.modal-form-group {
  margin-bottom: 15px;
}
.modal-form-label {
  display: block;
  margin-bottom: 5px;
}
.modal-form-input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.modal-action-button {
  background-color: #28a745;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.modal-action-button:hover {
  background-color: #218838;
}
    </style>

  
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
                <!-- <div class="search-bar">
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
                </div> -->





<div class="search-bar">
  <input type="text" id="inventorySearch" placeholder="Search for menu" />
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
    <circle cx="11" cy="11" r="8"></circle>
    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
  </svg>
</div>
<div class="filter-dropdowns">
  <div class="dropdown">
    <select id="categoryFilter" class="inventory-dropdown-toggle">
      <option value="">All Category</option>

<?php if(isset($categories)):?>
  <?php foreach ($categories as $category): ?>
 <option value="<?=$category['name']?>"><?=$category['name'] ?></option>
<?php endforeach;?>
<?php endif;?>
    </select>
  </div>
<div class="dropdown">
    <select id="statusFilter" class="inventory-dropdown-toggle">
      <option value="">All Status</option>
      <option value="Available">Available</option>
      <option value="Low">Low</option>
      <option value="Out of Stock">Out of Stock</option>
    </select>
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
<?php if(isset($categories)):?>
  <?php foreach ($categories as $category): ?>
 <option value="<?=$category['id'] ?>"><?=$category['name'] ?></option>
<?php endforeach;?>
<?php endif;?>
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
<!-- Action Modal -->
<div class="modal" id="action-modal">
  <div class="modal-content">
    <button id="modal-close-button" class="modal-close-button">×</button>
    <h2 class="modal-title"></h2>
    <form id="modal-form" class="modal-form">
      <div class="modal-form-group">
        <label for="modal-item-name" class="modal-form-label">Item Name</label>
        <input type="text" id="modal-item-name" class="modal-form-input" readonly>
      </div>
      <div class="modal-form-group">
        <label for="modal-new-stock" class="modal-form-label">New Stock Quantity</label>
        <input type="number" id="modal-new-stock" class="modal-form-input" min="0" required>
      </div>
      <div class="modal-actions">
        <button type="submit" class="modal-action-button">Update</button>
      </div>
    </form>
  </div>
</div>



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
 <!-- Modal Backdrop -->
<div id="modal-backdrop"></div>

<!-- Update Stock Modal -->
<div class="modal" id="updateStockModal">
  <div class="modal-content">
    <button class="modal-close-button">×</button>
    <h2 class="modal-title">Update Stock</h2>
    <form id="updateStockForm" class="modal-form">
      <div class="modal-form-group">
        <label for="updateItemName" class="modal-form-label">Item Name</label>
        <input type="text" id="updateItemName" class="modal-form-input" readonly>
      </div>
      <div class="modal-form-group">
        <label for="newStockQuantity" class="modal-form-label">New Stock Quantity</label>
        <input type="number" id="newStockQuantity" class="modal-form-input" min="0" required>
      </div>
      <input type="hidden" id="updateItemId">
      <div class="modal-actions">
        <button type="submit" class="modal-action-button">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Reorder Modal -->
<div class="modal" id="reorderModal">
  <div class="modal-content">
    <button class="modal-close-button">×</button>
    <h2 class="modal-title">Reorder Item</h2>
    <form id="reorderForm" class="modal-form">
      <div class="modal-form-group">
        <label for="reorderItemName" class="modal-form-label">Item Name</label>
        <input type="text" id="reorderItemName" class="modal-form-input" readonly>
      </div>
      <div class="modal-form-group">
        <label for="reorderQuantity" class="modal-form-label">Reorder Quantity</label>
        <input type="number" id="reorderQuantity" class="modal-form-input" min="1" required>
      </div>
      <input type="hidden" id="reorderItemId">
      <div class="modal-actions">
        <button type="submit" class="modal-action-button">Place Reorder</button>
      </div>
    </form>
  </div>
</div>
          </div>
        </div>
      </div>
    </main>

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
  const updateStockModal = $('#updateStockModal');
  const reorderModal = $('#reorderModal');
  const modalBackdrop = $('#modal-backdrop');
  const updateStockForm = $('#updateStockForm');
  const reorderForm = $('#reorderForm');
  const updateItemName = $('#updateItemName');
  const newStockQuantity = $('#newStockQuantity');
  const updateItemId = $('#updateItemId');
  const reorderItemName = $('#reorderItemName');
  const reorderQuantity = $('#reorderQuantity');
  const reorderItemId = $('#reorderItemId');

  // Initialize DataTable
  const table = $('#inventory-table').DataTable({
    ajax: '../../../BackEnd/controller/inventory/fetch_inventory.php',
    columns: [
      { data: null, defaultContent: '<input type="checkbox" />' }, // Checkbox
      { data: 1 }, // Item
      { data: 2 }, // Category
      {
        data: 3, // Status
        render: function (data, type, row) {
          if (type === 'filter' || type === 'sort') {
            return data ? $(data).text().trim() : ''; // Extract plain text
          }
          return data; // Use HTML for display
        }
      },
      { data: 4 }, // Qty in Stock
      { data: 5 }, // Qty in Reorder
      { data: 6 }  // Action
    ],
    paging: true,
    pageLength: 10,
    language: {
      paginate: {
        previous: '‹',
        next: '›'
      },
      lengthMenu: "Show _MENU_ entries",
      info: "Showing _START_ to _END_ of _TOTAL_ entries",
      search: "",
      zeroRecords: "No matching records found"
    }
  });

  // Search bar functionality
  $('#inventorySearch').on('keyup', function () {
    table.search(this.value).draw();
  });

  // Custom filtering for category and status
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    const categoryFilter = $('#categoryFilter').val();
    const statusFilter = $('#statusFilter').val();
    const category = data[2] ? data[2].trim() : '';
    const status = data[3] ? data[3].trim() : ''; // Plain text from render
    console.log('Category Filter:', categoryFilter, 'Row Category:', category);
    console.log('Status Filter:', statusFilter, 'Row Status:', status);
    const categoryMatch = !categoryFilter || category === categoryFilter;
    const statusMatch = !statusFilter || status === statusFilter;
    return categoryMatch && statusMatch;
  });

  // Apply filters when dropdowns change
  $('#categoryFilter, #statusFilter').on('change', function () {
    table.draw();
  });

  // Open modals on button clicks
  $('#inventory-table').on('click', '.update-stock-button', function () {
    const itemId = $(this).data('id');
    const itemName = $(this).data('name');
    updateItemId.val(itemId);
    updateItemName.val(itemName);
    newStockQuantity.val('');
    updateStockModal.add(modalBackdrop).addClass('show');
  });

  $('#inventory-table').on('click', '.reorder-button', function () {
    const itemId = $(this).data('id');
    const itemName = $(this).data('name');
    reorderItemId.val(itemId);
    reorderItemName.val(itemName);
    reorderQuantity.val('');
    reorderModal.add(modalBackdrop).addClass('show');
  });

  // Close modals
  $('.modal-close-button').on('click', function () {
    $(this).closest('.modal').add(modalBackdrop).removeClass('show');
  });

  // Close modal when clicking backdrop
  modalBackdrop.on('click', function () {
    updateStockModal.add(reorderModal).add(modalBackdrop).removeClass('show');
  });

  // Submit update stock form
  updateStockForm.on('submit', function (e) {
    e.preventDefault();
    const itemId = updateItemId.val();
    const newStock = newStockQuantity.val();
    if (!newStock || isNaN(newStock) || newStock < 0) {
      alert('Please enter a valid stock quantity.');
      return;
    }
    fetch('../../../BackEnd/controller/inventory/update_stock.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        item_id: itemId,
        new_stock: parseInt(newStock)
      })
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('Stock updated successfully!');
          updateStockModal.add(modalBackdrop).removeClass('show');
          table.ajax.reload(null, false);
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => {
        alert('Request failed: ' + err.message);
      });
  });

  // Submit reorder form
  reorderForm.on('submit', function (e) {
    e.preventDefault();
    const itemId = reorderItemId.val();
    const reorderQty = reorderQuantity.val();
    if (!reorderQty || isNaN(reorderQty) || reorderQty < 1) {
      alert('Please enter a valid reorder quantity.');
      return;
    }
    fetch('../../../BackEnd/controller/inventory/place_reorder.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        item_id: itemId,
        reorder_quantity: parseInt(reorderQty)
      })
    })
      .then(res => {
        res.text().then(text => {
          console.log('Raw response from place_reorder.php:', text);
          try {
            return JSON.parse(text);
          } catch (e) {
            throw new Error('JSON parse failed: ' + text);
          }
        });
      })
      .then(data => {
        if (data.success) {
          alert('Reorder placed successfully!');
          reorderModal.add(modalBackdrop).removeClass('show');
          table.ajax.reload(null, false);
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => {
        alert('Request failed: ' + err.message);
      });
  });
});
    </script>
    <script src="../../scripts/components.js"></script>
    <script src="../../scripts/inventory.js"></script>

  </body>
</html>




