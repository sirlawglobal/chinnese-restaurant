<?php
require_once 'C:/xampp/htdocs/chinnese-restaurant/BackEnd/config/db.php';
$categories = db_query('SELECT id, name FROM categories', [], 'array');
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
    <title>Inventory</title>
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
      .status.available { color: green; }
      .status.low { color: orange; }
      .status.out-of-stock { color: red; }
      .progress-bar-container { width: 50px; height: 8px; background: #e0e0e0; border-radius: 4px; margin-bottom: 4px; }
      .progress-bar { height: 100%; border-radius: 4px; background: green; }
      .progress-bar.low { background: orange; }
      .progress-bar.out-of-stock { background: red; }
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
                  <input type="text" placeholder="Search for menu" id="search-input" />
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
                    <button class="inventory-dropdown-toggle" id="category-filter">
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
                    <div class="dropdown-menu" id="category-menu"></div>
                  </div>
                  <div class="dropdown">
                    <button class="inventory-dropdown-toggle" id="status-filter">
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
                      <a href="#" data-status="">All Status</a>
                      <a href="#" data-status="Available">Available</a>
                      <a href="#" data-status="Low">Low</a>
                      <a href="#" data-status="Out of Stock">Out of Stock</a>
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
                  <form id="add-menu-item-form" enctype="multipart/form-data">
                    <div class="form-group">
                      <label for="category" class="form-label">Category</label>
                      <div class="select-wrapper">
                        <select id="category" name="category_id" class="form-input" required>
                          <option value="">Select Category</option>
                          <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>">
                              <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                          <?php endforeach; ?>
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
                    <div class="form-group">
                      <label for="stock_quantity" class="form-label">Quantity in Stock</label>
                      <input
                        type="number"
                        id="stock_quantity"
                        name="stock_quantity"
                        placeholder="e.g., 50"
                        class="form-input"
                        required
                      />
                    </div>
                    <div class="form-group">
                      <label for="reorder_quantity" class="form-label">Quantity in Reorder</label>
                      <input
                        type="number"
                        id="reorder_quantity"
                        name="reorder_quantity"
                        placeholder="e.g., 20"
                        class="form-input"
                        required
                      />
                    </div>
                    <div class="form-actions">
                      <button type="submit" class="primary-button">Add Menu Item</button>
                    </div>
                  </form>
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
              <tbody></tbody>
            </table>
            <div id="action-modal">
              <div class="modal-header">
                <h2 class="modal-title">Update Item</h2>
                <button id="modal-close-button" class="modal-close-button">
                  ×
                </button>
              </div>
              <form id="modal-form" class="modal-form">
                <div class="modal-form-group">
                  <label for="modal-item-name" class="modal-form-label">Item Name</label>
                  <input
                    type="text"
                    id="modal-item-name"
                    class="modal-form-input"
                    readonly
                  />
                </div>
                <div class="modal-form-group">
                  <label for="modal-new-stock" class="modal-form-label">New Stock Quantity</label>
                  <input
                    type="number"
                    id="modal-new-stock"
                    class="modal-form-input"
                    required
                  />
                </div>
                <div class="modal-actions">
                  <button type="submit" class="modal-action-button">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
    <script>
      let optionCount = 1;
      $(document).ready(function () {
        const table = $("#inventory-table").DataTable({
          paging: true,
          pageLength: 10,
          language: {
            paginate: {
              previous: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.9254 4.55806C13.1915 4.80214 13.1915 5.19786 12.9254 5.44194L8.4375 9.55806C8.17138 9.80214 8.17138 10.1979 8.4375 10.4419L12.9254 14.5581C13.1915 14.8021 13.1915 15.1979 12.9254 15.4419C12.6593 15.686 12.2278 15.686 11.9617 15.4419L7.47378 11.3258C6.67541 10.5936 6.67541 9.40641 7.47378 8.67418L11.9617 4.55806C12.2278 4.31398 12.6593 4.31398 12.9254 4.55806Z" fill="#1C1C1C"/></svg>`,
              next: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.07459 15.4419C6.80847 15.1979 6.80847 14.8021 7.07459 14.5581L11.5625 10.4419C11.8286 10.1979 11.8286 9.80214 11.5625 9.55806L7.07459 5.44194C6.80847 5.19786 6.80847 4.80214 7.07459 4.55806C7.34072 4.31398 7.77219 4.31398 8.03831 4.55806L12.5262 8.67418C13.3246 9.40641 13.3246 10.5936 12.5262 11.3258L8.03831 15.4419C7.77219 15.686 7.34072 15.686 7.07459 15.4419Z" fill="#1C1C1C"/></svg>`,
            },
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            search: "Search:",
            zeroRecords: "No matching records found",
          },
          columns: [
            { data: null, render: () => '<input type="checkbox" />' },
            { data: 'name' },
            { data: 'category' },
            {
              data: 'status',
              render: function (data) {
                const className = data.toLowerCase().replace(' ', '-');
                return `<span class="status ${className}">${data}</span>`;
              }
            },
            {
              data: null,
              render: function (data) {
                const max = Math.max(data.stock_quantity, data.reorder_quantity);
                const percentage = max ? (data.stock_quantity / max * 100).toFixed(2) : 0;
                const className = data.status.toLowerCase().replace(' ', '-');
                return `
                  <div class="progress-bar-container">
                    <div class="progress-bar ${className}" style="width: ${percentage}%"></div>
                  </div>
                  ${data.stock_quantity}
                `;
              }
            },
            { data: 'reorder_quantity' },
            {
              data: null,
              render: function (data) {
                return `
                  <button class="reorder-button" data-id="${data.id}">Reorder</button>
                  <button class="update-stock-button" data-id="${data.id}" data-name="${data.name}">Update Stock</button>
                `;
              }
            }
          ]
        });

        // Fetch inventory data
        function fetchInventory(categoryId = '', status = '') {
          $.ajax({
            url: 'api.php',
            method: 'GET',
            data: { category_id: categoryId, status: status },
            dataType: 'json',
            success: function (response) {
              if (response.success) {
                table.clear().rows.add(response.data).draw();
              } else {
                alert('Failed to load inventory: ' + response.message);
              }
            },
            error: function () {
              alert('Error fetching inventory data');
            }
          });
        }

        // Initial fetch
        fetchInventory();

        // Populate category filter
        $.ajax({
          url: 'api.php?action=categories',
          method: 'GET',
          dataType: 'json',
          success: function (response) {
            if (response.success) {
              const menu = $('#category-menu');
              menu.append('<a href="#" data-category="">All Category</a>');
              response.data.forEach(category => {
                menu.append(`<a href="#" data-category="${category.id}">${category.name}</a>`);
              });
            }
          }
        });

        // Filter by category
        $(document).on('click', '#category-menu a', function (e) {
          e.preventDefault();
          const categoryId = $(this).data('category');
          $('#category-filter').text($(this).text());
          fetchInventory(categoryId, $('#status-filter').data('status') || '');
        });

        // Filter by status
        $(document).on('click', '#status-menu a', function (e) {
          e.preventDefault();
          const status = $(this).data('status');
          $('#status-filter').text($(this).text()).data('status', status);
          fetchInventory($('#category-filter').data('category') || '', status);
        });

        // Handle form submission
        $('#add-menu-item-form').on('submit', function (e) {
          e.preventDefault();
          const formData = new FormData(this);
          
          // Handle portion options
          const options = [];
          $('.option-group').each(function (index) {
            const portion = $(this).find(`input[name="options[${index}][portion]"]`).val();
            const price = $(this).find(`input[name="options[${index}][price]"]`).val();
            if (portion && price) {
              options.push({ portion, price: parseFloat(price) });
            }
          });
          if (options.length > 0) {
            formData.set('options', JSON.stringify(options));
          }

          // Handle set menu
          if ($('#is_set_menu').is(':checked')) {
            const setMenu = {
              name: $('#set_menu_name').val(),
              price: parseFloat($('#set_menu_price').val() || 0),
              items: $('#set_menu_items').val().split(',').map(item => item.trim())
            };
            formData.set('set_menu', JSON.stringify(setMenu));
          }

          $.ajax({
            url: 'api.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
              if (response.success) {
                alert(`Item added successfully! Item ID: ${response.item_id}, Image URL: ${response.image_url || 'None'}`);
                $('#add-menu-item-form')[0].reset();
                $('#options-section, #set-menu-section').hide();
                $('#price, #image, #has_options').prop('disabled', false);
                $('#addModal').hide();
                fetchInventory();
              } else {
                alert('Error adding item: ' + response.message);
              }
            },
            error: function () {
              alert('Error adding item');
            }
          });
        });

        // Handle update stock
        $(document).on('click', '.update-stock-button', function () {
          const id = $(this).data('id');
          const name = $(this).data('name');
          $('#modal-item-name').val(name);
          $('#action-modal').show();
          
          $('#modal-form').off('submit').on('submit', function (e) {
            e.preventDefault();
            const newStock = $('#modal-new-stock').val();
            if (newStock !== '' && !isNaN(newStock)) {
              $.ajax({
                url: 'api.php',
                method: 'PATCH',
                contentType: 'application/json',
                data: JSON.stringify({ id, stock_quantity: parseInt(newStock) }),
                dataType: 'json',
                success: function (response) {
                  if (response.success) {
                    alert('Stock updated successfully');
                    $('#action-modal').hide();
                    fetchInventory();
                  } else {
                    alert('Error updating stock: ' + response.message);
                  }
                },
                error: function () {
                  alert('Error updating stock');
                }
              });
            }
          });
        });

        // Handle reorder (placeholder)
        $(document).on('click', '.reorder-button', function () {
          const id = $(this).data('id');
          alert(`Reordering item with ID: ${id}`);
          // Implement reorder logic via API if needed
        });

        // Modal controls
        $('#addProduct').on('click', function () {
          $('#addModal').show();
        });
        $('#close-modal, #modal-close-button').on('click', function () {
          $('#addModal, #action-modal').hide();
        });

        // Dynamic portion options
        $('#add-option').on('click', function () {
          const optionGroup = $(`
            <div class="form-group option-group">
              <div class="flex justify-between align-center">
                <div class="form-group">
                  <label for="portion_${optionCount}" class="form-label">Portion</label>
                  <input
                    type="text"
                    id="portion_${optionCount}"
                    name="options[${optionCount}][portion]"
                    placeholder="e.g., 1/4 (6 pancakes)"
                    class="form-input"
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
                  />
                </div>
              </div>
            </div>
          `);
          $('#options-section').prepend(optionGroup);
          optionCount++;
        });

        // Checkbox behavior
        $('#has_options').on('change', function () {
          $('#options-section').toggle(this.checked);
          const priceInput = $('#price');
          const imageInput = $('#image');
          if (this.checked) {
            priceInput.val('').prop('disabled', true);
            imageInput.prop('disabled', true);
          } else {
            priceInput.prop('disabled', false);
            imageInput.prop('disabled', false);
          }
        });

        $('#is_set_menu').on('change', function () {
          $('#set-menu-section').toggle(this.checked);
          const priceInput = $('#price');
          const imageInput = $('#image');
          const optionsCheckbox = $('#has_options');
          if (this.checked) {
            priceInput.val('').prop('disabled', true);
            imageInput.prop('disabled', true);
            optionsCheckbox.prop('disabled', true).prop('checked', false);
            $('#options-section').hide();
          } else {
            priceInput.prop('disabled', false);
            imageInput.prop('disabled', false);
            optionsCheckbox.prop('disabled', false);
          }
        });
      });
    </script>
    <script src="../../scripts/components.js"></script>
    <script src="../../scripts/inventory.js"></script>
  </body>
</html>