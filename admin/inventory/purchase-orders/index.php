

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
// Pass PHP variables to JavaScript
const username = '<?php echo addslashes($first_name); ?>';
const userRole = '<?php echo addslashes($userRole); ?>';
const profilePicture = '<?php echo addslashes($profilePicture); ?>';
</script>

  <script>
    $(document).ready(function () {
      // Initialize DataTable
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
            render: () => '<input type="checkbox" />',
            orderable: false,
          },
          {
            data: null,
            render: (data) => `${data.orderId}<br /><small>${data.orderDate}</small>`,
          },
          {
            data: null,
            render: (data) => `${data.item}<br /><small>${data.category}</small>`,
          },
          { data: "vendor" },
          {
            data: "status",
            render: (data) => `<span class="status ${data.toLowerCase()}">${data}</span>`,
          },
          {
            data: null,
            render: (data) => `
              <div class="delivery-progress">
                <div class="flex justify-between align-center">
                  <div class="progress-bar-container">
                    <div class="progress-bar ${data.status.toLowerCase()}" style="width: ${data.deliveryProgress}%"></div>
                  </div>
                  ${data.deliveryProgress}%
                </div>
                <small>${data.status === "Delivered" ? "Arrived" : "Arrive"} ${data.deliveryDate}</small>
              </div>`,
          },
          { data: "unitPrice", render: (data) => `$${data.toFixed(2)}` },
          { data: "quantity" },
          { data: "totalPrice", render: (data) => `$${data.toFixed(2)}` },
          {
            data: null,
            render: (data) => `
              <button class="receive-button ${data.status === "Delivered" ? "received" : ""}">
                ${data.status === "Delivered" ? "Received" : "Receive"}
              </button>`,
            orderable: false,
          },
        ],
      });

      // Load mockup data
      if (typeof purchaseOrders !== 'undefined') {
        table.rows.add(purchaseOrders).draw();
      } else {
        console.error("Mockup data (purchaseOrders) not found. Ensure mockup.js is loaded correctly.");
        table.rows.add([]).draw(); // Draw empty table if data is missing
      }

      // Handle "Receive" button clicks (mocked behavior)
      $("#inventory-table").on("click", ".receive-button", function () {
        const row = $(this).closest("tr");
        const data = table.row(row).data();
        if (data.status !== "Delivered") {
          // Mock status update
          data.status = "Delivered";
          data.deliveryProgress = 100;
          table.row(row).data(data).draw(); // Update row
          alert("Order marked as received! (Mocked)");
        }
      });

      // Handle filter tabs (All, Pending, Shipped, Delivered)
      $(".inventory-actions .tabs .tab").on("click", function () {
        $(".inventory-actions .tabs .tab").removeClass("active");
        $(this).addClass("active");
        const status = $(this).text();
        if (status === "All") {
          table.column(4).search("").draw();
        } else {
          table.column(4).search(status).draw();
        }
      });

      // Handle category dropdown filter
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
