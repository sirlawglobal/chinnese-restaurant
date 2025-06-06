<?php 
require_once __DIR__ . '/../../../BackEnd/config/init.php';
requireAdmin();
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
      /* inventory.css */
#inventory-table_wrapper {
  font-family: inherit; /* Match your app's font */
}

#inventory-table th, #inventory-table td {
  padding: 10px; /* Match padding from static table */
  text-align: left;
  vertical-align: middle;
}

/* Style the STATUS column */
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
  background-color: #f5a623; /* Orange */
}

.status.delivered::before {
  background-color: #f5a623; /* Orange */
}

/* Style the DELIVERY progress bar */
.delivery-progress {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.progress-bar-container {
  width: 100px; /* Match width from static table */
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
  background-color: #f5a623; /* Orange */
}

.progress-bar.delivered {
  background-color: #f5a623; /* Orange */
}

/* Style the ACTION buttons */
.receive-button {
  padding: 5px 10px;
  border: 1px solid #e0e0e0;
  background-color: white;
  border-radius: 4px;
  cursor: pointer;
}

.receive-button.received {
  background-color: #f5a623; /* Orange */
  color: white;
  border: none;
}
    </style>
    <main>
    <div class="content">
      <div class="top tabs">
        <a href="../items/" class="tab">Inventory</a>
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
              <div class="dropdown">
                <button class="inventory-dropdown-toggle">
                  All Status
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
                <div class="dropdown-menu">
                  <a href="#">All</a>
                  <a href="#">Pending</a>
                  <a href="#">Shipped</a>
                  <a href="#">Delivered</a>
                </div>
              </div>
            </div>
            <button class="add-product-button">+ Add Product</button>
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

<script>
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
        {
          data: null,
          render: function () {
            return '<input type="checkbox" />';
          },
          orderable: false,
        },
        {
          data: "order_id",
          render: function (data) {
            return data;
          },
        },
        {
          data: null,
          render: function (data) {
            return `${data.item}<br /><small>${data.category || 'N/A'}</small>`;
          },
        },
        { data: "vendor" },
        {
          data: "status",
          render: function (data) {
            return `<span class="status ${data.toLowerCase()}">${data}</span>`;
          },
        },
        {
          data: null,
          render: function (data) {
            try {
              return `
                <div class="delivery-progress">
                  <div class="flex justify-between align-center">
                    <div class="progress-bar-container">
                      <div class="progress-bar ${data.status.toLowerCase()}" style="width: ${data.delivery_progress || 0}%"></div>
                    </div>
                    ${data.delivery_progress || 0}%
                  </div>
                  <small>${data.status === "delivered" ? "Arrived" : data.status === "pending" ? "Arrive" : data.status === "shipping" ? "In Transit" : "N/A"} ${new Date(data.schedule_date || data.order_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) || 'N/A'}</small>
                </div>`;
            } catch (e) {
              console.error("Delivery render error for order_id", data.order_id, e);
              return "Error rendering delivery";
            }
          },
        },
        {
          data: "unit_price",
          render: function (data) {
            try {
              return `$${parseFloat(data).toFixed(2)}`;
            } catch (e) {
              console.error("Unit price error for order_id", data.order_id, e);
              return "$0.00";
            }
          },
        },
        { data: "quantity" },
        {
          data: "total_price",
          render: function (data) {
            try {
              return `$${parseFloat(data).toFixed(2)}`;
            } catch (e) {
              console.error("Total price error for order_id", data.order_id, e);
              return "$0.00";
            }
          },
        },
        {
          data: null,
          render: function (data) {
            return `
              <button class="receive-button ${data.status === "delivered" ? "received" : ""}" data-order-id="${data.order_id.split('\n')[0]}">
                ${data.status === "delivered" ? "Received" : "Receive"}
              </button>`;
          },
          orderable: false,
        },
      ],
    });

    $.ajax({
      url: "./api.php",
      method: "GET",
      dataType: "json",
      cache: false,
      success: function (response) {
        console.log("Full Response:", response);
        console.log("Data Array:", response.data);
        if (response.success && Array.isArray(response.data)) {
          table.clear().rows.add(response.data).draw();
          console.log("Table rows added:", table.data().length);
        } else {
          console.error("API Error or invalid data:", response.message, response.error);
          table.clear().draw();
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error, xhr.responseText);
        table.clear().draw();
      },
    });

  $("#inventory-table").on("click", ".receive-button", function () {
  const button = $(this);
  const orderId = button.data("order-id"); // e.g., "PO102"
  const row = button.closest("tr");
  const data = table.row(row).data();

  if (data.status !== "delivered") {
    // Extract the original id by removing "PO" and subtracting 100
    const originalId = parseInt(orderId.replace('PO', '')) - 100;
    $.ajax({
      url: "./update.php",
      method: "POST",
      dataType: "json",
      data: {
        order_id: originalId, // Send the original id (e.g., 2)
        status: "delivered"
      },
      success: function (response) {
        if (response.success) {
          // Reload data from api.php to ensure consistency
          $.ajax({
            url: "./api.php",
            method: "GET",
            dataType: "json",
            cache: false,
            success: function (response) {
              if (response.success && Array.isArray(response.data)) {
                table.clear().rows.add(response.data).draw();
                alert("Order " + orderId.replace('PO', '') + " marked as received!");
              }
            },
            error: function (xhr, status, error) {
              console.error("AJAX Error reloading data:", error, xhr.responseText);
              alert("Error reloading table data.");
            }
          });
        } else {
          console.error("Update failed:", response.message, response.error);
          alert("Failed to update order status: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error updating status:", error, xhr.responseText);
        alert("Error updating order status.");
      }
    });
  }
});

    $(".inventory-actions .tabs .tab").on("click", function () {
      $(".inventory-actions .tabs .tab").removeClass("active");
      $(this).addClass("active");
      const status = $(this).text().toLowerCase();
      if (status === "all") {
        table.column(4).search("").draw();
      } else {
        table.column(4).search(`^${status}$`, true, false).draw();
      }
    });

    $(".dropdown-menu a").on("click", function (e) {
      e.preventDefault();
      const category = $(this).text();
      const dropdownToggle = $(this).closest(".dropdown").find(".inventory-dropdown-toggle");
      dropdownToggle.contents().first().replaceWith(category);
      if (category === "All Category") {
        table.column(2).search("").draw();
      } else {
        table.column(2).search(category).draw();
      }
    });
  });
</script>
    <script src="../../scripts/components.js"></script>
  </body>
</html>
