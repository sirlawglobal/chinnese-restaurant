<?php
require_once __DIR__ . '/../../../BackEnd/config/init.php';

UserSession::requireLogin();
UserSession::requireRole(['staff', 'admin', 'super_admin']);
$first_name = UserSession::getFirstName();
$userRole = UserSession::get('role');
$profilePicture = UserSession::getProfilePicture();
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
  <h3 id="weeklyTotal">0</h3> <!-- Dynamic total -->
  <div class="filter">
    <strong id="dateRangeLabel" class="flex align-center justify-content-xxl-between">
      This Week
      <svg class="icon caret"><use href="#caret-down"></use></svg>
    </strong>
    <div class="dropdown"></div>
  </div>
</div>

         <div style="height: 250px;">
  <canvas id="supplyChart" style="height: 100%; width: 100%;"></canvas>
</div>
          </div>
          <div class="card">
            <div class="total flex align-center">
              <h2 id="totalProducts"></h2>
              Products
            </div>
          
            <div style="height: 250px;">
  <canvas id="stockChart" style="height: 100%; width: 100%;"></canvas>
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
                  <span class="legend-color"></span> Low:
                </div>
                <div class="flex align-center">
                  <h1 id="inStock2"></h1>
                  Products
                </div>
              </div>
              <div class="legend-item">
                <div class="flex align-center">
                  <span class="legend-color"></span> Out of Stock:
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

<form id="add-stock-item-form" method="POST">
  <div class="form-group">
    <label for="category" class="form-label">Category</label>
    <div class="select-wrapper">
      <select id="category" name="category_id" class="form-input" required>
        <option value="">Select Category</option>
        <?php if(isset($categories)):?>
          <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
          <?php endforeach; ?>
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
      placeholder="e.g., Garlic Sauce"
      class="form-input"
      required
    />
  </div>

  <div class="form-group">
    <label for="qty" class="form-label">Initial Quantity</label>
    <input
      type="number"
      id="qty"
      name="qty"
      placeholder="e.g., 100"
      class="form-input"
      required
    />
  </div>

  <div class="form-group">
    <label for="record_qty" class="form-label">Reorder Threshold</label>
    <input
      type="number"
      id="record_qty"
      name="record_qty"
      placeholder="e.g., 20"
      class="form-input"
      required
    />
  </div>

  <div class="form-actions">
    <button type="submit" class="primary-button">Add Stock Item</button>
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
      <div class="modal-form-group">
        <label for="actionS" class="modal-form-label">Action</label>
<select id="actionS" class="modal-form-input" required>
        <option value="">Select Action</option>
        <option value="1">Subtract</option>
        <option value="2">Addtion</option>
        </select>
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
       <div class="modal-form-group">
        <label for="actionS" class="modal-form-label">Action</label>
<select id="record" class="modal-form-input" required>
        <option value="">Select Action</option>
        <option value="1">Subtract</option>
        <option value="2">Addtion</option>
        </select>
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
  const actionS = $('#actionS');
  const updateItemId = $('#updateItemId');
  const reorderItemName = $('#reorderItemName');
  const reorderQuantity = $('#reorderQuantity');
  const record = $('#record');
  const reorderItemId = $('#reorderItemId');
let ROOTS = "<?= ROOT ?>";
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
    actionS.val('');
    updateStockModal.add(modalBackdrop).addClass('show');
  });

  $('#inventory-table').on('click', '.reorder-button', function () {
    const itemId = $(this).data('id');
    const itemName = $(this).data('name');
    reorderItemId.val(itemId);
    reorderItemName.val(itemName);
    reorderQuantity.val('');
    record.val('');
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
    const actionSe = actionS.val();
    if (!actionSe || !newStock || isNaN(newStock) || newStock < 0) {
      alert('Please enter a valid stock quantity.');
      return;
    }
    fetch('../../../BackEnd/controller/inventory/update_stock.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        item_id: itemId,
        actionSe:actionSe,
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

reorderForm.on('submit', function (e) {
  e.preventDefault();
  const itemId = reorderItemId.val();
  const reorderQty = reorderQuantity.val();
  const records = record.val();

  if (!reorderQty ||isNaN(reorderQty) || reorderQty < 1) {
    alert('Please enter a valid reorder quantity.');
    return;
  }
  const requestBody = {
    item_id: itemId,
    recorD: records,
    reorder_quantity: parseInt(reorderQty)
  };
  // console.log('Sending request:', requestBody);
  // console.log('Request URL:', '/chinnese-restaurant/BackEnd/controller/inventory/place_reorder.php'); // Adjusted URL
  fetch('../../../BackEnd/controller/inventory/place_reorder.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(requestBody)
  })
    .then(res => {
      console.log('Response:', {
        status: res.status,
        ok: res.ok,
        url: res.url
      });
      if (!res.ok) {
        throw new Error(`HTTP error! Status: ${res.status} ${res.statusText}`);
      }
      return res.text(); // Use text to debug raw response
    })
    .then(text => {
      console.log('Raw response:', text);
      if (!text) {
        throw new Error('Empty response from server');
      }
      try {
        const data = JSON.parse(text);
        console.log('Parsed JSON:', data);
        return data;
      } catch (e) {
        throw new Error(`JSON parse error: ${e.message}, Raw response: ${text}`);
      }
    })
    .then(data => {
      console.log('Data received:', data);
      if (typeof data !== 'object' || data === null) {
        throw new Error('Invalid JSON object');
      }
      if (data.success) {
        alert('Reorder placed successfully!');
        reorderModal.add(modalBackdrop).removeClass('show');
        table.ajax.reload(null, false);
      } else {
        alert('Error: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(err => {
      console.error('Fetch error:', err);
      alert('Request failed: ' + err.message);
    });
});



  
});

document.getElementById("add-stock-item-form").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("../../../BackEnd/controller/inventory/add_stock_item.php", {
    method: "POST",
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      alert(data.message);
      if (data.success) {
        // Optionally refresh table/chart here
        this.reset();
      }
    })
    .catch(err => {
      console.error("Error adding stock item:", err);
    });
});


  const username = '<?php echo addslashes($first_name); ?>';
      const userRole = '<?php echo addslashes($userRole); ?>';
      const profilePicture = '<?php echo addslashes($profilePicture); ?>';
    </script>
    <script src="../../scripts/components.js"></script>
    <script src="../../scripts/inventory.js"></script>

  </body>
</html>




















