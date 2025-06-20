<?php 
require_once __DIR__ . '/../../../BackEnd/config/init.php';
//requireAdmin();
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

    <title>User Reports</title>
    <link rel="stylesheet" href="../../assets/styles/general.css" />
    <link rel="stylesheet" href="../../assets/styles/panels.css" />
    <link rel="stylesheet" href="../../assets/styles/inventory.css" />
    <script src="./mockup.js"></script>
  </head>
  <body class="flex">
    <style>
      /* General Table Styles */
      #inventory-table_wrapper {
        position: relative;
        overflow: visible;
      }

      .inventory-overview,
      .content,
      main {
        position: relative;
        overflow: visible;
      }

      .action-block {
        display: none;
        position: absolute;
        z-index: 2000;
        background-color: #ffeb3b;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 5px;
        flex-direction: row;
        gap: 5px;
      }

      .action-block.show {
        display: flex;
      }

      #inventory-table th,
      #inventory-table td {
        padding: 10px;
        text-align: left;
        vertical-align: middle;
        position: relative;
      }

      /* Status Column Styles */
      .status {
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .status::before {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
      }

      .status.pending::before {
        background-color: black;
      }

      .status.shipped::before {
        background-color: #f5a623;
      }

      .status.delivered::before {
        background-color: #f5a623;
      }

      /* Delivery Progress Bar Styles */
      .delivery-progress {
        display: flex;
        flex-direction: column;
        gap: 5px;
      }

      .progress-bar-container {
        width: 100px;
        height: 8px;
        background-color: #e0e0e0;
        border-radius: 4px;
      }

      .progress-bar {
        height: 100%;
        border-radius: 4px;
      }

      .progress-bar.pending {
        background-color: black;
      }

      .progress-bar.shipped {
        background-color: #f5a623;
      }

      .progress-bar.delivered {
        background-color: #f5a623;
      }

      /* Action Button Styles */
      .receive-button {
        padding: 5px 10px;
        border: 1px solid #e0e0e0;
        background-color: white;
        border-radius: 4px;
        cursor: pointer;
      }

      .receive-button.received {
        background-color: #f5a623;
        color: white;
        border: none;
      }

      /* Action Trigger and Block Styles */
      .action-trigger,
      .action-icon {
        cursor: pointer;
        font-size: 18px;
        color: #007bff;
      }

      .action-trigger:hover,
      .action-icon:hover {
        color: #0056b3;
      }

      .action-trigger {
        margin-right: 5px;
      }

      .action-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 2px 5px;
        border-radius: 4px;
        transition: background-color 0.3s;
      }

      .action-btn:hover {
        background-color: #f0f0f0;
      }

      /* Modal and Backdrop Styles */
      .modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 8px;
        z-index: 1001;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
        max-height: 80vh;
        overflow-y: auto;
        width: 650px;
      }

      .modal.show,
      #modal-backdrop.show {
        display: block !important;
      }

      #modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
      }

      /* Item Management Styles */
      .add-item-button {
        background-color: #28a745;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
        font-size: 14px;
        transition: background-color 0.3s ease;
      }

      .add-item-button:hover {
        background-color: #218838;
      }

      .item-row {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 4px;
        align-items: flex-start;
      }

      .item-group {
        flex: 1;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
      }

      .remove-item {
        background-color: #dc3545;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        align-self: center;
        margin-top: 20px !important;
      }

      .remove-item:hover {
        background-color: #c82333;
      }

      .category-custom {
        margin-top: 5px;
      }

      /* Enhanced Modal Form Styles for Consistency */
      .modal-form-group {
        display: flex;
        flex-direction: column;
        min-width: 150px;
      }

      .modal-form-label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 5px;
        color: #333;
      }

      .modal-form-input {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        width: 100%;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
      }

      .modal-form-input:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
      }

      .category-select, .modal-form-input[type="text"], .modal-form-input[type="number"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        width: 100%;
        box-sizing: border-box;
      }

      .category-select:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
      }

      .modal-action-button {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s ease;
      }

      .modal-action-button:hover {
        background-color: #218838;
      }
    </style>
    <main>
      <div class="content">
        <div class="top tabs">
          <a href="#" class="tab">Inventory</a>
          <button class="tab active">Purchase Order</button>
        </div>
        <div class="inventory-overview card table">
          <div class="inventory-actions">
            <div class="tabs">
              <button class="tab active">All</button>
              <button class="tab">Pending</button>
              <button class="tab">Shipped</button>
              <button class="tab">Delivered</button>
            </div>
            <div class="search-filter-add">
              <div class="search-bar">
                <input type="text" placeholder="Search for menu" />
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                  <circle cx="11" cy="11" r="8"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
              </div>
              <div class="filter-dropdowns">
                <div class="dropdown">
                  <button class="inventory-dropdown-toggle">
                    All Category
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </button>
                  <div class="dropdown-menu">
                    <a href="#">Food Ingredients</a>
                    <a href="#">Kitchen Tools</a>
                    <a href="#">Other</a>
                  </div>
                </div>
            
              </div>
              <button class="add-product-button">+ Add Product</button>
            </div>
          </div>
          <div class="modal" id="actionModal" style="display: none;">
            <div class="modal-content" style="width: 300px;">
              <button class="modal-close-button">×</button>
              <h2 class="modal-title">Order Actions</h2>
              <div class="modal-actions">
                <button type="button" class="modal-action-button" id="viewAction">View</button>
                <button type="button" class="modal-action-button" id="editAction">Edit</button>
                <button type="button" class="modal-action-button" id="deleteAction">Delete</button>
              </div>
            </div>
          </div>
          <table id="inventory-table" class="display">
            <thead>
              <tr>
                <th></th>
                <th>Order ID</th>
                <th>Item</th>
                <th>Vendor/Supplier</th>
                <th>Status</th>
                <th>Delivery</th>
                <th>Unit Price</th>
                <th>Qty</th>
                <th>Total Price</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </main>
    <div id="modal-backdrop"></div>
    <!-- Add Order Modal -->
    <div class="modal" id="addOrderModal">
      <div class="modal-content">
        <button class="modal-close-button">×</button>
        <h2 class="modal-title">Add Purchase Order Items</h2>
        <form id="addOrderForm" class="modal-form">
          <div class="items-container">
            <div class="item-row" data-index="0">
              <div class="item-group">
                <div class="modal-form-group">
                  <label for="itemName_0" class="modal-form-label">Item Name</label>
                  <input type="text" id="itemName_0" name="itemName_0" class="modal-form-input" placeholder="e.g., Tso's Chicken" required>
                </div>
                <div class="modal-form-group">
                  <label for="unitPrice_0" class="modal-form-label">Unit Price ($)</label>
                  <input type="number" id="unitPrice_0" name="unitPrice_0" class="modal-form-input" step="0.01" placeholder="e.g., 12.00" required>
                </div>
                <div class="modal-form-group">
                  <label for="vendor_0" class="modal-form-label">Vendor/Supplier</label>
                  <input type="text" id="vendor_0" name="vendor_0" class="modal-form-input" placeholder="e.g., General Food" required>
                </div>
              </div>
              <div class="item-group">
                <div class="modal-form-group">
                  <label for="category_0" class="modal-form-label">Category</label>
                  <select id="categorySelect_0" name="categorySelect_0" class="modal-form-input category-select" required>
             <option value="">Select Category</option>
    <option value="2">Food Ingredients</option>
    <option value="1">Kitchen Tools</option>
    <option value="3">Other</option>
    <option value="4">Custom</option>n>
                  </select>
                  <input type="text" id="categoryCustom_0" name="categoryCustom_0" class="modal-form-input category-custom" placeholder="Enter custom category" style="display: none;" required>
                </div>
                <div class="modal-form-group">
                  <label for="quantity_0" class="modal-form-label">Quantity</label>
                  <input type="number" id="quantity_0" name="quantity_0" class="modal-form-input" min="1" placeholder="e.g., 1" required>
                </div>
              </div>
              <button type="button" class="remove-item">Remove</button>
            </div>
          </div>
          <button type="button" id="addItemButton" class="add-item-button">+ Add Another Item</button>
          <div class="items-summary" style="margin-top: 15px; display: none;">
            <h3>Items to Add</h3>
            <ul id="itemsSummary"></ul>
          </div>
          <div class="modal-actions">
            <button type="submit" class="modal-action-button">Submit All Items</button>
          </div>
        </form>
      </div>
    </div>
<script>
  $(document).ready(function () {
    // Utility function to escape HTML and special characters
    function escapeHtml(str) {
      if (!str) return '';
      return str.replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
    }

    // Format date in WAT (UTC+1)
    function formatWATDate(dateStr) {
      if (!dateStr) return 'TBD';
      try {
        var date = new Date(dateStr);
        if (isNaN(date.getTime())) return 'TBD';
        return date.toLocaleDateString('en-US', {
          month: 'short',
          day: '2-digit',
          year: 'numeric',
          timeZone: 'Africa/Lagos'
        });
      } catch (e) {
        console.error('Date parsing error:', e, dateStr);
        return 'TBD';
      }
    }

    // Initialize DataTable
    var table = $("#inventory-table").DataTable({
      paging: true,
      pageLength: 10,
      language: {
        paginate: {
          previous: '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.9254 4.55806C13.1915 4.80214 13.1915 5.19786 12.9254 5.44194L8.4375 9.55806C8.17138 9.80214 8.17138 10.1979 8.4375 10.4419L12.9254 14.5581C13.1915 14.8021 13.1915 15.1979 12.9254 15.4419C12.6593 15.686 12.2278 15.686 11.9617 15.4419L7.47378 11.3258C6.67541 10.5936 6.67541 9.40641 7.47378 8.67418L11.9617 4.55806C12.2278 4.31398 12.6593 4.31398 12.9254 4.55806Z" fill="#1C1C1C"/></svg>',
          next: '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.07459 15.4419C6.80847 15.1979 6.80847 14.8021 7.07459 14.5581L11.5625 10.4419C11.8286 10.1979 11.8286 9.80214 11.5625 9.55806L7.07459 5.44194C6.80847 5.19786 6.80847 4.80214 7.07459 4.55806C7.34072 4.31398 7.77219 4.31398 8.03831 4.55806L12.5262 8.67418C13.3246 9.40641 13.3246 10.5936 12.5262 11.3258L8.03831 15.4419C7.77219 15.686 7.34072 15.686 7.07459 15.4419Z" fill="#1C1C1C"/></svg>',
        },
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "Showing 0 to 0 of 0 entries",
        infoFiltered: "(filtered from _MAX_ total entries)",
        search: "Search:",
        zeroRecords: "No matching records found",
      },
      columns: [
        {
          data: null,
          render: function () {
            return '<input type="checkbox" />';
          },
          orderable: false,
        },
        { data: "order_id" },
        {
          data: null,
          render: function (data) {
            if (!data) return 'N/A';
            return data.item ? escapeHtml(data.item) + '<br /><small>' + escapeHtml(data.category || 'N/A') + '</small>' : 'N/A';
          },
        },
        { data: "vendor", render: function (data) { return escapeHtml(data || 'N/A'); } },
        {
          data: "status",
          render: function (data) {
            if (!data) return '<span class="status">N/A</span>';
            return '<span class="status ' + data.toLowerCase() + '">' + escapeHtml(data) + '</span>';
          },
        },
        {
          data: null,
          render: function (data) {
            if (!data) {
              console.warn('No data for delivery column', data);
              return 'N/A';
            }
            try {
              var progress = Math.max(0, Math.min(100, parseFloat(data.progress) || 0));
              var status = (data.status || 'pending').toLowerCase();
              var date = formatWATDate(data.schedule_date);
              var statusText;
              switch (status) {
                case 'delivered':
                  statusText = 'Arrived';
                  progress = progress || 100;
                  break;
                case 'shipped':
                  statusText = 'In Transit';
                  progress = progress || 50;
                  break;
                case 'pending':
                default:
                  statusText = 'Arrive';
                  progress = progress || 0;
                  break;
              }
              return '<div class="delivery-progress">' +
                     '<div class="flex justify-between align-center">' +
                     '<div class="progress-bar-container">' +
                     '<div class="progress-bar ' + status + '" style="width: ' + progress + '%"></div>' +
                     '</div>' + progress + '%</div>' +
                     '<small>' + escapeHtml(statusText) + ' ' + date + '</small></div>';
            } catch (e) {
              console.error('Delivery render error for order_id', data.order_id, e);
              return 'Error rendering delivery';
            }
          },
        },
        {
          data: "unit_price",
          render: function (data) {
            try {
              return '$' + parseFloat(data || 0).toFixed(2);
            } catch (e) {
              console.error('Unit price error', e);
              return '$0.00';
            }
          },
        },
        { data: "quantity" },
        {
          data: "total_price",
          render: function (data) {
            try {
              return '$' + parseFloat(data || 0).toFixed(2);
            } catch (e) {
              console.error('Total price error', e);
              return '$0.00';
            }
          },
        },
        {
          data: null,
          render: function (data) {
            if (!data || !data.order_id) return '';
            return '<span class="action-trigger" data-order-id="' + data.order_id + '">⋯</span>';
          },
          orderable: false,
        },
      ],
    });

    // AJAX to load data
 $.ajax({
        url: './api.php',
        method: 'GET',
        dataType: 'json',
        cache: false,
        success: function (response) {
            console.log('API Response:', JSON.stringify(response, null, 2));
            if (response && response.success && Array.isArray(response.data)) {
                response.data.forEach(function (row, index) {
                    console.log('Row ' + index + ':', JSON.stringify(row, null, 2));
                    if (!row.order_id || isNaN(parseInt(row.order_id))) {
                        console.warn('Invalid order_id in row ' + index + ':', row.order_id);
                    }
                    if (!row.order_date && !row.schedule_date) {
                        console.warn('Missing date in row ' + index + ':', row);
                    }
                });
                table.clear().rows.add(response.data).draw();
                console.log('Table rows added:', table.data().length);
            } else {
                console.error('API Error or invalid data:', response.message || 'No message', response.error || 'No error');
                table.clear().draw();
                alert('Failed to load data: ' + (response.message || 'Unknown error'));
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', status, error, xhr.responseText);
            table.clear().draw();
            alert('Error loading data. Check console for details.');
        },
    });

    // Action block logic
    var currentActionBlock = null;
    var currentTrigger = null;

    function positionActionBlock($trigger, $actionBlock) {
      if (!$trigger || !$trigger.length || !$actionBlock || !$actionBlock.length) {
        console.error('Invalid trigger or actionBlock:', { trigger: $trigger, actionBlock: $actionBlock });
        return;
      }

      var $cell = $trigger.closest('td');
      if (!$cell.length) {
        console.error('No parent td found for trigger:', $trigger);
        return;
      }

      var triggerOffset = $trigger.offset() || { top: 0, left: 0 };
      var cellOffset = $cell.offset() || { top: 0, left: 0 };
      var tableOffset = $('#inventory-table').offset() || { top: 0, left: 0 };
      var viewportWidth = $(window).width();
      var actionBlockWidth = 100;
      var leftPos = triggerOffset.left - cellOffset.left;
      var topPos = $trigger.outerHeight() + 5;

      if (triggerOffset.left + actionBlockWidth > viewportWidth) {
        leftPos = cellOffset.left - tableOffset.left - actionBlockWidth + $trigger.outerWidth();
      }

      try {
        $actionBlock.css({
          position: 'absolute',
          top: topPos + 'px',
          left: leftPos + 'px',
          zIndex: 2000,
          display: 'flex',
          backgroundColor: '#ffeb3b',
          border: '1px solid #ccc',
          borderRadius: '4px',
          padding: '5px',
          gap: '5px'
        });
        console.log('Positioned action block:', { top: topPos, left: leftPos });
      } catch (e) {
        console.error('Error setting action block CSS:', e);
      }
    }

    $('#inventory-table').on('click', '.action-trigger', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var $trigger = $(this);
      var orderId = $trigger.data('order-id');
      var rowData = table.row($trigger.closest('tr')).data();
      var $cell = $trigger.closest('td');

      if (!rowData || !orderId) {
        console.error('Invalid rowData or orderId:', { orderId, rowData });
        return;
      }

      if (currentActionBlock && currentTrigger && currentTrigger[0] !== $trigger[0]) {
        currentActionBlock.removeClass('show').remove();
        currentActionBlock = null;
        currentTrigger = null;
      }

      if (currentActionBlock) {
        currentActionBlock.removeClass('show').remove();
        currentActionBlock = null;
        currentTrigger = null;
      } else {
        var $actionBlock = $('<div class="action-block"></div>').html(
          '<button class="action-btn" data-action="view" title="View">👁️</button>' +
          '<button class="action-btn" data-action="edit" title="Edit">✏️</button>' +
          '<button class="action-btn" data-action="delete" title="Delete">🗑️</button>'
        );
        $cell.append($actionBlock);
        $actionBlock.data('order-id', orderId).data('row-data', rowData);
        currentActionBlock = $actionBlock;
        currentTrigger = $trigger;

        positionActionBlock($trigger, $actionBlock);
        $actionBlock.addClass('show');

        console.log('Added action block for orderId:', orderId, rowData);
      }

      $(document).one('click', function (e) {
        if (!$(e.target).closest('.action-block').length && !$(e.target).closest('.action-trigger').length) {
          console.log('Closing action block due to outside click');
          if (currentActionBlock) {
            currentActionBlock.removeClass('show').remove();
            currentActionBlock = null;
            currentTrigger = null;
          }
        }
      });
    });

    $(window).on('scroll', function () {
      if (currentActionBlock && currentTrigger && currentTrigger.length) {
        var triggerOffset = currentTrigger.offset() || { top: 0 };
        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();

        if (triggerOffset.top < viewportTop || triggerOffset.top > viewportBottom) {
          console.log('Closing action block due to scroll out of viewport');
          currentActionBlock.removeClass('show').remove();
          currentActionBlock = null;
          currentTrigger = null;
        } else {
          positionActionBlock(currentTrigger, currentActionBlock);
        }
      }
    });

    // Action handlers
    $('#inventory-table').on('click', '.action-btn', function (e) {
      e.stopPropagation();
      var $actionBlock = $(this).closest('.action-block');
      var orderId = $actionBlock.data('order-id');
      var rowData = $actionBlock.data('row-data');
      var action = $(this).data('action');

      if (!rowData || !orderId) {
        console.error('Invalid action data:', { orderId, rowData });
        alert('Error: Invalid order data.');
        return;
      }

      // Close action block
      $actionBlock.removeClass('show').remove();
      currentActionBlock = null;
      currentTrigger = null;

      if (action === 'view') {
        alert('Viewing: ' + JSON.stringify(rowData, null, 2));
      } else if (action === 'edit') {
        $('#addOrderModal').addClass('show');
        $('#modal-backdrop').addClass('show');
        $('#addOrderForm')[0].reset();
        $('#addOrderModal .modal-title').text('Edit Purchase Order Item');

        var itemName = rowData.item || '';
        var unitPrice = parseFloat(rowData.unit_price || 0) || 0;
        var vendor = rowData.vendor || '';
        var quantity = parseInt(rowData.quantity, 10) || 1;
        var category = rowData.category || '';
        var status = rowData.status || 'pending';
        var isCustomCategory = category && !['Food Ingredients', 'Kitchen Tools', 'Other'].includes(category);
        var categoryId = isCustomCategory ? '4' : (category === 'Food Ingredients' ? '2' : category === 'Kitchen Tools' ? '1' : category === 'Other' ? '3' : '');

        var $itemRow = $('<div>').addClass('item-row').attr('data-index', '0');
        var $itemGroup1 = $('<div>').addClass('item-group');
        var $itemGroup2 = $('<div>').addClass('item-group');

        $itemGroup1.append(
          $('<div>').addClass('modal-form-group').append(
            $('<label>').addClass('modal-form-label').attr('for', 'itemName_0').text('Item Name'),
            $('<input>').addClass('modal-form-input')
                       .attr({ type: 'text', id: 'itemName_0', name: 'itemName_0', placeholder: "e.g., Tso's Chicken", required: true })
                       .val(itemName)
          ),
          $('<div>').addClass('modal-form-group').append(
            $('<label>').addClass('modal-form-label').attr('for', 'unitPrice_0').text('Unit Price ($)'),
            $('<input>').addClass('modal-form-input')
                       .attr({ type: 'number', id: 'unitPrice_0', name: 'unitPrice_0', step: '0.01', placeholder: 'e.g., 12.00', required: true })
                       .val(unitPrice.toFixed(2))
          ),
          $('<div>').addClass('modal-form-group').append(
            $('<label>').addClass('modal-form-label').attr('for', 'vendor_0').text('Vendor/Supplier'),
            $('<input>').addClass('modal-form-input')
                       .attr({ type: 'text', id: 'vendor_0', name: 'vendor_0', placeholder: 'e.g., General Food', required: true })
                       .val(vendor)
          )
        );

        var $categorySelect = $('<select>').addClass('modal-form-input category-select')
                                          .attr({ id: 'categorySelect_0', name: 'categorySelect_0', required: true })
                                          .append(
                                            $('<option>').val('').text('Select Category'),
                                            
                                            $('<option>').val('1').text('Kitchen Tools').prop('selected', categoryId === '1'),
                                            $('<option>').val('2').text('Food Ingredients').prop('selected', categoryId === '2'),
                                            $('<option>').val('3').text('Other').prop('selected', categoryId === '3'),
                                            $('<option>').val('4').text('Custom').prop('selected', categoryId === '4')

                                          );
        var $categoryCustom = $('<input>').addClass('modal-form-input category-custom')
                                         .attr({ type: 'text', id: 'categoryCustom_0', name: 'categoryCustom_0', placeholder: 'Enter custom category' })
                                         .val(isCustomCategory ? category : '')
                                         .css('display', isCustomCategory ? 'block' : 'none');

        var $statusSelect = $('<select>').addClass('modal-form-input status-select')
                                        .attr({ id: 'status_0', name: 'status_0', required: true })
                                        .append(
                                          $('<option>').val('pending').text('Pending').prop('selected', status.toLowerCase() === 'pending'),
                                          $('<option>').val('shipped').text('Shipped').prop('selected', status.toLowerCase() === 'shipped'),
                                          $('<option>').val('delivered').text('Delivered').prop('selected', status.toLowerCase() === 'delivered')
                                        );

        $itemGroup2.append(
          $('<div>').addClass('modal-form-group').append(
            $('<label>').addClass('modal-form-label').attr('for', 'categorySelect_0').text('Category'),
            $categorySelect,
            $categoryCustom
          ),
          $('<div>').addClass('modal-form-group').append(
            $('<label>').addClass('modal-form-label').attr('for', 'quantity_0').text('Quantity'),
            $('<input>').addClass('modal-form-input')
                       .attr({ type: 'number', id: 'quantity_0', name: 'quantity_0', min: '1', placeholder: 'e.g., 1', required: true })
                       .val(quantity)
          ),
          $('<div>').addClass('modal-form-group').append(
            $('<label>').addClass('modal-form-label').attr('for', 'status_0').text('Status'),
            $statusSelect
          )
        );

        var $removeButton = $('<button>').addClass('remove-item').attr('type', 'button').text('Remove');

        $itemRow.append($itemGroup1, $itemGroup2, $removeButton);
        $('.items-container').empty().append($itemRow);

        itemIndex = 0;
        setupCategoryToggle(0);
        updateSummary();

        $('#addOrderForm').off('submit').on('submit', function (e) {
          e.preventDefault();
          var items = [];
          var index = 0;
          var itemName = $('#itemName_' + index).val();
          var unitPrice = parseFloat($('#unitPrice_' + index).val()) || 0;
          var vendor = $('#vendor_' + index).val();
          var category = $('#categorySelect_' + index).val();
          if (category === '4') {
            category = $('#categoryCustom_' + index).val() || '';
          }
          var quantity = parseInt($('#quantity_' + index).val()) || 0;
          var status = $('#status_' + index).val();

          if (itemName && unitPrice > 0 && vendor && category && quantity > 0 && status) {
            items.push({
              item_name: itemName,
              unit_price: unitPrice,
              vendor: vendor,
              category: category,
              quantity: quantity,
              status: status
            });
          } else {
            alert('Please fill all fields with valid values.');
            return;
          }

          console.log('Sending update:', { order_id: orderId, items: items });
          $.ajax({
            url: './update_order_item.php',
            method: 'POST',
            dataType: 'json',
            data: JSON.stringify({ order_id: orderId, items: items }),
            contentType: 'application/json',
            success: function (response) {
              console.log('Update response:', response);
              if (response.success) {
                var rowIdx = -1;
                table.rows().every(function (idx) {
                  if (this.data().order_id === orderId) {
                    rowIdx = idx;
                  }
                });
                if (rowIdx !== -1) {
                  var newData = {
                    order_id: orderId,
                    item: items[0].item_name,
                    category: isCustomCategory ? items[0].category : (items[0].category === '1' ? 'Food Ingredients' : items[0].category === '2' ? 'Kitchen Tools' : 'Other'),
                    vendor: items[0].vendor,
                    status: items[0].status,
                    progress: items[0].status === 'delivered' ? 100 : items[0].status === 'shipped' ? 50 : 0,
                    schedule_date: rowData.schedule_date || formatWATDate(new Date()),
                    unit_price: items[0].unit_price,
                    quantity: items[0].quantity,
                    total_price: items[0].unit_price * items[0].quantity
                  };
                  table.row(':eq(' + rowIdx + ')').data(newData).draw();
                  $('#addOrderModal').removeClass('show');
                  $('#modal-backdrop').removeClass('show');
                  $('#addOrderModal .modal-title').text('Add Purchase Order Items');
                  alert('Order updated successfully!');
                } else {
                  console.error('Row not found for orderId:', orderId);
                  alert('Error: Could not find order to update.');
                }
              } else {
                console.error('Update failed:', response.message);
                alert('Error updating order item: ' + response.message);
              }
            },
            error: function (xhr, status, error) {
              console.error('Update AJAX Error:', status, error, xhr.responseText);
              alert('Error updating order: ' + error);
            }
          });
        });
      } else if (action === 'delete') {
        if (confirm('Are you sure you want to delete this order?')) {
          $.ajax({
            url: './delete_order_item.php',
            method: 'POST',
            dataType: 'json',
            data: JSON.stringify({ order_id: orderId }),
            contentType: 'application/json',
            success: function (response) {
              console.log('Delete response:', response);
              if (response.success) {
                var rowIdx = -1;
                table.rows().every(function (idx) {
                  if (this.data().order_id === orderId) {
                    rowIdx = idx;
                  }
                });
                if (rowIdx !== -1) {
                  table.row(':eq(' + rowIdx + ')').remove().draw();
                  alert('Order deleted successfully!');
                } else {
                  console.error('Row not found for orderId:', orderId);
                  alert('Error: Could not find order to delete.');
                }
              } else {
                console.error('Delete failed:', response.message);
                alert('Error deleting order: ' + response.message);
              }
            },
            error: function (xhr, status, error) {
              console.error('Delete AJAX Error:', status, error, xhr.responseText);
              alert('Error deleting order: ' + error);
            }
          });
        }
      }
    });


// Debug jQuery and DOM
console.log('Document ready, jQuery version:', $.fn.jquery);
console.log('Binding status tab click handler');

// Use event delegation
$(document).on('click', '.inventory-actions .tabs .tab', function (e) {
    e.preventDefault();
    console.log('Status tab clicked:', $(this).text(), 'Event:', e);
    $('.inventory-actions .tabs .tab').removeClass('active');
    $(this).addClass('active');
    var status = $(this).text().toLowerCase().trim();
    console.log('Filtering status:', `"${status}"`);
    if (status === 'all') {
        table.column(4).search('').draw();
    } else {
        table.column(4).search(status, false, true).draw();
    }
    console.log('Filtered rows:', table.rows({ search: 'applied' }).data().toArray());
    console.log('All statuses in table:', table.column(4).data().toArray());
});
console.log('Tabs found:', $('.inventory-actions .tabs .tab').length);

// Test selector
console.log('Tabs found:', $('.inventory-actions .tabs .tab').length); // Debug

    // Tab and dropdown filters
    // $('.inventory-actions .tabs .tab').on('click', function () {
    //   $('.inventory-actions .tabs .tab').removeClass('active');
    //   $(this).addClass('active');
    //   var status = $(this).text().toLowerCase();
    //   if (status === 'all') {
    //     table.column(4).search('').draw();
    //   } else {
    //     table.column(4).search('^' + status + '$', true, false).draw();
    //   }
    // });

    $('.dropdown-menu a').on('click', function (e) {
      e.preventDefault();
      var category = $(this).text();
      var dropdownToggle = $(this).closest('.dropdown').find('.inventory-dropdown-toggle');
      dropdownToggle.contents().first().replaceWith(category);
      if (category === 'All Category') {
        table.column(2).search('').draw();
      } else {
        table.column(2).search(category).draw();
      }
    });

// $('.inventory-actions .tabs .tab').on('click', function () {
//     console.log('Status tab clicked:', $(this).text());
//     $('.inventory-actions .tabs .tab').removeClass('active');
//     $(this).addClass('active');
//     var status = $(this).text().toLowerCase().trim();
//     console.log('Filtering status:', `"${status}"`);
//     if (status === 'all') {
//         table.column(4).search('').draw();
//     } else {
//         table.column(4).search('^' + status + '$', true, false).draw();
//     }
//     console.log('Filtered rows:', table.rows({ search: 'applied' }).data().toArray());
//     console.log('All statuses in table:', table.column(4).data().toArray());
// });







    // Add Order Modal Functionality
    var addOrderModal = $('#addOrderModal');
    var modalBackdrop = $('#modal-backdrop');
    var itemIndex = 0;

    $('.add-product-button').on('click', function () {
      console.log('Add Product button clicked');
      if (addOrderModal.length) {
        addOrderModal.add(modalBackdrop).addClass('show');
        $('#addOrderModal .modal-title').text('Add Purchase Order Items');
        $('#addOrderForm')[0].reset();
        $('.items-container').html($('.items-container .item-row').first().clone());
        itemIndex = 0;
        setupCategoryToggle(0);
        updateSummary();
        console.log('Modal should be visible');
      }
    });

    modalBackdrop.on('click', function () {
      addOrderModal.add(modalBackdrop).removeClass('show');
      $('#addOrderModal .modal-title').text('Add Purchase Order Items');
      resetForm();
    });

    $('.modal-close-button').on('click', function () {
      addOrderModal.add(modalBackdrop).removeClass('show');
      $('#addOrderModal .modal-title').text('Add Purchase Order Items');
      resetForm();
    });

    $('#addItemButton').on('click', function (e) {
      e.preventDefault();
      console.log('Add Item button clicked');
      var newIndex = itemIndex + 1;
      var $newRow = $('<div>').addClass('item-row').attr('data-index', newIndex);
      var $itemGroup1 = $('<div>').addClass('item-group');
      var $itemGroup2 = $('<div>').addClass('item-group');

      $itemGroup1.append(
        $('<div>').addClass('modal-form-group').append(
          $('<label>').addClass('modal-form-label').attr('for', 'itemName_' + newIndex).text('Item Name'),
          $('<input>').addClass('modal-form-input')
                     .attr({ type: 'text', id: 'itemName_' + newIndex, name: 'itemName_' + newIndex, placeholder: "e.g., Tso's Chicken", required: true })
        ),
        $('<div>').addClass('modal-form-group').append(
          $('<label>').addClass('modal-form-label').attr('for', 'unitPrice_' + newIndex).text('Unit Price ($)'),
          $('<input>').addClass('modal-form-input')
                     .attr({ type: 'number', id: 'unitPrice_' + newIndex, name: 'unitPrice_' + newIndex, step: '0.01', placeholder: 'e.g., 12.00', required: true })
        ),
        $('<div>').addClass('modal-form-group').append(
          $('<label>').addClass('modal-form-label').attr('for', 'vendor_' + newIndex).text('Vendor/Supplier'),
          $('<input>').addClass('modal-form-input')
                     .attr({ type: 'text', id: 'vendor_' + newIndex, name: 'vendor_' + newIndex, placeholder: 'e.g., General Food', required: true })
        )
      );

      $itemGroup2.append(
        $('<div>').addClass('modal-form-group').append(
          $('<label>').addClass('modal-form-label').attr('for', 'categorySelect_' + newIndex).text('Category'),
          $('<select>').addClass('modal-form-input category-select')
                      .attr({ id: 'categorySelect_' + newIndex, name: 'categorySelect_' + newIndex, required: true })
                      .append(
                        $('<option>').val('').text('Select Category'),
                        $('<option>').val('2').text('Food Ingredients'),
                        $('<option>').val('1').text('Kitchen Tools'),
                        $('<option>').val('3').text('Other'),
                        $('<option>').val('4').text('Custom')
                      ),
          $('<input>').addClass('modal-form-input category-custom')
                     .attr({ type: 'text', id: 'categoryCustom_' + newIndex, name: 'categoryCustom_' + newIndex, placeholder: 'Enter custom category' })
                     .css('display', 'none')
        ),
        $('<div>').addClass('modal-form-group').append(
          $('<label>').addClass('modal-form-label').attr('for', 'quantity_' + newIndex).text('Quantity'),
          $('<input>').addClass('modal-form-input')
                     .attr({ type: 'number', id: 'quantity_' + newIndex, name: 'quantity_' + newIndex, min: '1', placeholder: 'e.g., 1', required: true })
        ),
        $('<div>').addClass('modal-form-group').append(
          $('<label>').addClass('modal-form-label').attr('for', 'status_' + newIndex).text('Status'),
          $('<select>').addClass('modal-form-input status-select')
                      .attr({ id: 'status_' + newIndex, name: 'status_' + newIndex, required: true })
                      .append(
                        $('<option>').val('pending').text('Pending'),
                        $('<option>').val('shipped').text('Shipped'),
                        $('<option>').val('delivered').text('Delivered')
                      )
        )
      );

      var $removeButton = $('<button>').addClass('remove-item').attr('type', 'button').text('Remove');

      $newRow.append($itemGroup1, $itemGroup2, $removeButton);
      $('.items-container').append($newRow);
      itemIndex = newIndex;
      setupCategoryToggle(newIndex);
      updateSummary();
      console.log('Added row with index ' + newIndex);
    });

    $(document).on('change', '.category-select', function () {
      var index = $(this).attr('id').split('_')[1];
      var customInput = $('#categoryCustom_' + index);
      if ($(this).val() === '4') {
        customInput.show().prop('required', true);
        $(this).prop('required', false);
      } else {
        customInput.hide().prop('required', false).val('');
        $(this).prop('required', true);
      }
      updateSummary();
    });

    $(document).on('click', '.remove-item', function () {
      $(this).closest('.item-row').remove();
      updateSummary();
    });

    $('#addOrderForm').on('submit', function (e) {
      e.preventDefault();
      var items = [];
      $('.item-row').each(function () {
        var index = $(this).data('index');
        var itemName = $('#itemName_' + index).val();
        var unitPrice = parseFloat($('#unitPrice_' + index).val()) || 0;
        var vendor = $('#vendor_' + index).val();
        var category = $('#categorySelect_' + index).val();
        if (category === '4') {
          category = $('#categoryCustom_' + index).val() || '';
        }
        var quantity = parseInt($('#quantity_' + index).val()) || 0;
        var status = $('#status_' + index).val();

        if (itemName && unitPrice > 0 && vendor && category && quantity > 0 && status) {
          items.push({
            item_name: itemName,
            unit_price: unitPrice,
            vendor: vendor,
            category: category,
            quantity: quantity,
            status: status
          });
        }
      });

      if (items.length === 0) {
        alert('Please add at least one item and fill all fields.');
        return;
      }

      console.log('Sending add:', { items });
      $.ajax({
        url: './add_order_items.php',
        method: 'POST',
        dataType: 'json',
        data: JSON.stringify({ items: items }),
        contentType: 'application/json',
        success: function (response) {
          console.log('Add response:', response);
          if (response.success && Array.isArray(response.data)) {
            response.data.forEach(function (item) {
              var orderItem = {
                order_id: item.order_id,
                item: item.item_name,
                category: item.category === '1' ? 'Food Ingredients' : item.category === '2' ? 'Kitchen Tools' : item.category === '3' ? 'Other' : item.category,
                vendor: item.vendor,
                status: item.status || 'pending',
                schedule_date: formatWATDate(new Date()),
                progress: item.status === 'delivered' ? 100 : item.status === 'shipped' ? 50 : 0,
                unit_price: item.unit_price,
                quantity: item.quantity,
                total_price: item.unit_price * item.quantity
              };
              table.row.add(orderItem).draw();
            });
            $('#addOrderModal').add(modalBackdrop).removeClass('show');
            $('#addOrderModal .modal-title').text('Add Purchase Order Items');
            resetForm();
            alert('Order items added successfully!');
          } else {
            console.error('Add order failed:', response.message || 'Unknown error');
            alert('Error adding items: ' + (response.message || 'Unknown error'));
          }
        },
        error: function (xhr, status, error) {
          console.error('Add AJAX Error:', status, error, xhr.responseText);
          alert('Error adding items. Check console for details.');
        }
      });
    });

    function updateSummary() {
      var summary = $('#itemsSummary').empty();
      $('.item-row').each(function () {
        var index = $(this).data('index');
        var itemName = $('#itemName_' + index).val();
        var vendor = $('#vendor_' + index).val();
        var category = $('#categorySelect_' + index).val() === '4' ? $('#categoryCustom_' + index).val() : $('#categorySelect_' + index).find('option:selected').text();
        var quantity = $('#quantity_' + index).val();
        var status = $('#status_' + index).val();
        if (itemName && vendor && category && quantity && status) {
          summary.append('<li>Item: ' + escapeHtml(itemName) + ', Vendor: ' + escapeHtml(vendor) + ', Category: ' + escapeHtml(category) + ', Qty: ' + escapeHtml(quantity) + ', Status: ' + escapeHtml(status) + '</li>');
        }
      });
      if (summary.children().length) {
        summary.parent().show();
      } else {
        summary.parent().hide();
      }
    }

    function resetForm() {
      $('.items-container').html($('.items-container .item-row').first().clone());
      itemIndex = 0;
      $('#itemsSummary').empty().parent().hide();
      $('#addOrderForm')[0].reset();
      setupCategoryToggle(0);
    }

    function setupCategoryToggle(index) {
      $('#categorySelect_' + index).on('change', function () {
        var customInput = $('#categoryCustom_' + index);
        if ($(this).val() === '4') {
          customInput.show().prop('required', true);
          $(this).prop('required', false);
        } else {
          customInput.hide().prop('required', false).val('');
          $(this).prop('required', true);
        }
        updateSummary();
      });
    }

    // Initial setup
    setupCategoryToggle(0);
    updateSummary();
  });
</script>
    <script src="../../scripts/components.js"></script>
  </body>
</html>