<?php
require_once '../../BackEnd/config/db.php';
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


// Fetch categories for the add order form

$categories = db_query("SELECT id, name FROM categories ORDER BY name", [], 'assoc');

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">.
  <script type="text/javascript">
    $(document).ready(function () {
      $("#my-table").DataTable({
        paging: true,
        pageLength: 10,
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
  <title>Orders</title>
  <link rel="stylesheet" href="../assets/styles/general.css" />
  <link rel="stylesheet" href="../assets/styles/panels.css" />
  <link rel="stylesheet" href="../assets/styles/orders.css" />

  <style>
    .chart-container {
      position: relative;
      height: 300px;
      width: 100%;
    }

    .order-chart-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 20px;
    }

    .chart-wrapper {
      position: relative;
      width: 150px;
      height: 150px;
    }

    #centerText {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
    }

    #centerText .title {
      font-size: 12px;
      color: #666;
    }

    #centerText .value {
      font-size: 18px;
      font-weight: bold;
    }

    #chart-legend {
      flex: 1;
      padding-left: 20px;
    }

    .legend-item {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .legend-color {
      display: inline-block;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      margin-right: 8px;
    }

    .legend-label {
      flex: 1;
      font-size: 12px;
      color: #666;
    }

    .legend-value {
      font-weight: bold;
      font-size: 12px;
    }

    .action-dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-toggle {
      padding: 5px 10px;
      cursor: pointer;
    }

    .dropdown-menu1 {
      display: none;
      position: absolute;
      right: 0;
      background: white;
      border: 1px solid #ccc;
      z-index: 999;
      list-style: none;
      padding: 5px 0;
      margin: 0;
      width: 100px;
    }

    .dropdown-menu1 li {
      padding: 5px 10px;
    }

    .dropdown-menu1 li:hover {
      background: #f0f0f0;
    }

    .action-dropdown1 {
      position: relative;
      display: inline-block;
    }

    .dropdown-toggle1 {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 6px 12px;
      font-size: 14px;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .dropdown-toggle1:hover {
      background-color: #2980b9;
    }

    .dropdown-menu1 {
      display: none;
      position: absolute;
      right: 0;
      top: 35px;
      min-width: 120px;
      background-color: white;
      box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
      border-radius: 4px;
      z-index: 1000;
      padding: 0;
      overflow: hidden;
    }

    .dropdown-menu1 li {
      border-bottom: 1px solid #eee;
    }

    .dropdown-menu1 li:last-child {
      border-bottom: none;
    }

    .dropdown-menu1 li button {
      width: 100%;
      background: none;
      border: none;
      padding: 10px 16px;
      text-align: left;
      font-size: 14px;
      color: #333;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .dropdown-menu1 li button:hover {
      background-color: #f2f2f2;
      color: #000;
    }

    
    .order-item-row {
      position: relative;
      padding-right: 40px;
    }

    .remove-item-btn {
      /*
      height:20%; */
       width:4%;
      position: absolute;
      right: 10px;
      top: 38%; 
      transform: translateY(-50%);
    }

    
  </style>
</head>

<body class="flex">
  <main>
    <div class="content">

<!-- <div class="mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
      <svg width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
      </svg>
      Add Order
    </button>
  </div> -->

  <div class="mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addOrderModal">Add Order</button>
    </div>
      <?php
      require_once '../../BackEnd/config/db.php';

      // Get all orders
      $orders = db_query("SELECT * FROM orders ORDER BY created_at DESC", [], 'assoc');

      // Calculate summary statistics
      $totalOrders = count($orders);
      $processing = 0;
      $completed = 0;
      $cancelled = 0;
      $delivery = 0;
      $pickup = 0;
      $dinein = 0;

      // For weekly data
      $weeklyData = array_fill(0, 7, 0); // Initialize array for 7 days
      $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

      foreach ($orders as $order) {
        // Status counts
        switch (strtolower($order['status'])) {
          case 'processing':
            $processing++;
            break;
          case 'completed':
            $completed++;
            break;
          case 'cancelled':
            $cancelled++;
            break;
        }

        // Order type counts
        switch (strtolower($order['order_type'])) {
          case 'delivery':
            $delivery++;
            break;
          case 'pickup':
            $pickup++;
            break;
          case 'dinein':
            $dinein++;
            break;
        }

        // Weekly data - count orders by day of week
        $orderDay = date('w', strtotime($order['created_at'])); // 0 (Sun) to 6 (Sat)
        $weeklyData[$orderDay]++;
      }

      // Reorder weekly data to start with Monday (optional)
      $weeklyData = array_merge(array_slice($weeklyData, 1), array_slice($weeklyData, 0, 1));
      $dayNames = array_merge(array_slice($dayNames, 1), array_slice($dayNames, 0, 1));
      ?>
      <div class="summary grid">
        <div class="grid g-af2">
          <div class="grid-item card">
            <div class="flex justify-between align-center">
              <svg class="icon">
                <use href="#receipt"></use>
              </svg>
              <svg class="icon">
                <use href="#dots"></use>
              </svg>
            </div>
            <h5><?php echo $totalOrders; ?></h5>
            <p>Total Orders</p>
          </div>
          <div class="grid-item card">
            <div class="flex justify-between align-center">
              <svg class="icon">
                <use href="#dc"></use>
              </svg>
              <svg class="icon">
                <use href="#dots"></use>
              </svg>
            </div>
            <h5><?php echo $processing; ?></h5>
            <p>On Process</p>
          </div>
          <div class="grid-item card">
            <div class="flex justify-between align-center">
              <svg class="icon">
                <use href="#check"></use>
              </svg>
              <svg class="icon">
                <use href="#dots"></use>
              </svg>
            </div>
            <h5><?php echo $completed; ?></h5>
            <p>Completed</p>
          </div>
          <div class="grid-item card">
            <div class="flex justify-between align-center">
              <svg class="icon">
                <use href="#receiptx"></use>
              </svg>
              <svg class="icon">
                <use href="#dots"></use>
              </svg>
            </div>
            <h5><?php echo $cancelled; ?></h5>
            <p>Cancelled</p>
          </div>
        </div>
        <div class="card chart-container overview">
          <div class="title flex justify-between align-center">
            <h6>Orders Overview</h6>
            <div class="filter">
              <strong class="flex align-center justify-content-xxl-between">
                This Week
                <svg class="icon caret">
                  <use href="#caret-down"></use>
                </svg>
              </strong>
              <div class="dropdown"></div>
            </div>
          </div>
          <canvas id="overviewChart" height="100"></canvas>
        </div>
        <div class="order_type card">
          <div class="title flex justify-between align-center">
            <h6>Order Types</h6>
            <div class="filter">
              <strong class="flex align-center justify-content-xxl-between">
                This Week
                <svg class="icon caret">
                  <use href="#caret-down"></use>
                </svg>
              </strong>
              <div class="dropdown"></div>
            </div>
          </div>
          <div class="order-chart-container">
            <div class="chart-wrapper">
              <canvas id="order_typeChart" height="150" width="150"></canvas>
              <div id="centerText">
                <div class="title">Total Order</div>
                <div class="value" id="totalValue"><?php echo $totalOrders; ?></div>
              </div>
            </div>
            <div id="chart-legend"></div>
          </div>
        </div>
      </div>

<div class="card">
  <table id="my-table">
    <thead class="header">
      <tr>
        <th>ORDER DATE</th>
        <th>Order ID</th>
        <th>SURNAME</th>
        <th>FIRSTNAME</th>
        <th>PHONE</th>
        <th>ADDRESS</th>
        <th>TOTAL PRICE</th>
        <th>STATUS</th>
        <th>ACTION</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <?php
        $orderId = $order['id'];
        $fullName = !empty($order['user_name']) ? $order['user_name'] : $order['guest_name'];
        $email = !empty($order['user_email']) ? $order['user_email'] : $order['guest_email'];
        $phone = !empty($order['user_phone']) ? $order['user_phone'] : ($order['guest_phone'] ?? '-');
        $address = htmlspecialchars($order['delivery_address']);
        $status = ucfirst($order['status']);
        $amount = '£' . number_format($order['total_amount'], 2);
        $date = date('Y-m-d', strtotime($order['created_at']));
        $time = date('h:i A', strtotime($order['created_at']));
        ?>
        <tr>
          <td><?= $date ?><br><small><?= $time ?></small></td>
          <td>ORD<?= $orderId ?></td>
          <td><?= explode(' ', $fullName)[1] ?? $fullName?></td>
          <td><?= explode(' ', $fullName)[0] ?? $fullName ?></td>
          <td><?= $phone ?></td>
          <td><?= $address ?></td>
          <td><?= $amount ?></td>
          <td><span class="status <?= strtolower($order['status']) ?>"><?= $status ?></span></td>
          <td>
            <div class="dropdown">
              <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Action
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#viewModal<?= $orderId ?>">View</a></li>
                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal<?= $orderId ?>">Edit</a></li>
                <li><a class="dropdown-item text-danger" href="delete_order.php?id=<?= $orderId ?>" 
                     onclick="return confirm('Are you sure you want to delete this order?')">Delete</a></li>
              </ul>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
    
<!-- Modals Section - Placed after the main table -->
<?php foreach ($orders as $order): ?>
  <?php
  $orderId = $order['id'];
  // $orderItems = db_query("SELECT oi.*, m.name AS item_name 
  //               FROM order_items oi 
  //               JOIN menu_items m ON oi.menu_item_id = m.id 
  //               WHERE oi.order_id = ?", [$orderId], 'assoc');

  $orderItems = db_query("SELECT * FROM order_items WHERE order_id = ?", [$orderId], 'assoc');


  $fullName = !empty($order['user_name']) ? $order['user_name'] : $order['guest_name'];
  $email = !empty($order['user_email']) ? $order['user_email'] : $order['guest_email'];
  $phone = !empty($order['user_phone']) ? $order['user_phone'] : ($order['guest_phone'] ?? '-');
  $address = htmlspecialchars($order['delivery_address']);
  $status = ucfirst($order['status']);
  $amount = '£' . number_format($order['total_amount'], 2);
  ?>
  
  <!-- View Modal -->
  <div class="modal fade" id="viewModal<?= htmlspecialchars($orderId) ?>" tabindex="-1" 
       aria-labelledby="viewModalLabel<?= htmlspecialchars($orderId) ?>" aria-hidden="true">
       
 

    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewModalLabel<?= htmlspecialchars($orderId) ?>">View Order #ORD<?= htmlspecialchars($orderId) ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>

  <div class="row">
    <div class="mb-3 col-md-4">
      <label class="form-label fw-bold">Name:</label>
      <input class="form-control" value="<?= htmlspecialchars($fullName) ?>" readonly>
    </div>
    <div class="mb-3 col-md-4">
      <label class="form-label fw-bold">Email:</label>
      <input class="form-control" value="<?= htmlspecialchars($email) ?>" readonly>
    </div>
    <div class="mb-3 col-md-4">
      <label class="form-label fw-bold">Phone:</label>
      <input class="form-control" value="<?= htmlspecialchars($phone) ?>" readonly>
    </div>
  </div>

  <div class="row">
    <div class="mb-3 col-md-6">
      <label class="form-label fw-bold">Address:</label>
      <input class="form-control" value="<?= htmlspecialchars($address) ?>" readonly>
    </div>

    <div class="mb-3 col-md-6">
      <label class="form-label fw-bold">Status:</label>
      <input class="form-control" value="<?= htmlspecialchars($status) ?>" readonly>
    </div>
    
  </div>

  <div class="row">
    <div class="mb-3 col-md-12">
      <label class="form-label fw-bold">Grand Total (with delivery):</label>
      <input class="form-control" value="<?= htmlspecialchars($amount) ?>" readonly>
    </div>
  </div>

</form>

          <h6 class="mt-4 fw-bold">Order Items</h6>
          <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th scope="col">Item Name</th>
                  <th scope="col">Quantity</th>
                  <th scope="col">Price</th>
                  <th scope="col">Total Price</th>
                </tr>
              </thead>
              <tbody>
  <?php $grandTotal = 0; ?>
  <?php foreach ($orderItems as $item): ?>
    <?php 
      $total = $item['quantity'] * $item['price'];
      $grandTotal += $total;
    ?>
    <tr>
      <td><?= htmlspecialchars($item['item_name']) ?></td>
      <td><?= htmlspecialchars($item['quantity']) ?></td>
      <td>£<?= number_format($item['price'], 2) ?></td>
      <td>£<?= number_format($total, 2) ?></td>
    </tr>
  <?php endforeach; ?>

  <?php if (empty($orderItems)): ?>
    <tr>
      <td colspan="4" class="text-center text-muted">No items found for this order.</td>
    </tr>
  <?php else: ?>
    <tr>
      <td colspan="3" class="text-end fw-bold">Sub Total:</td>
      <td><strong>£<?= number_format($grandTotal, 2) ?></strong></td>
    </tr>
  <?php endif; ?>
</tbody>

            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal<?= $orderId ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="update_order.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title">Edit Order #<?= $orderId ?></h5>
          </div>
          <div class="modal-body">
            <input type="hidden" name="order_id" value="<?= $orderId ?>">
            <!-- <div class="mb-2"><label>Name:</label><input class="form-control" name="name" value="<?= $fullName ?>"></div>
            <div class="mb-2"><label>Email:</label><input class="form-control" name="email" value="<?= $email ?>"></div>
            <div class="mb-2"><label>Phone:</label><input class="form-control" name="phone" value="<?= $phone ?>"></div>
            <div class="mb-2"><label>Address:</label><input class="form-control" name="address" value="<?= $address ?>"></div> -->
            <div class="mb-2"><label>Status:</label>
              <select name="status" class="form-control">
                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Update Order</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>
    </div>
  </main>

<!-- Add Order Modal -->
 <div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <!-- <form action="add_order.php" method="POST"> -->
            <form >
              <div class="modal-header">
                <h5 class="modal-title" id="addOrderModalLabel">Add New Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="mb-3 col-md-6">
                    <label class="form-label fw-bold">Full Name:</label>
                    <input type="text" class="form-control" name="full_name" required>
                  </div>
                  <div class="mb-3 col-md-6">
                    <label class="form-label fw-bold">Email:</label>
                    <input type="email" class="form-control" name="email" required>
                  </div>
                </div>
                <div class="row">
                  <div class="mb-3 col-md-6">
                    <label class="form-label fw-bold">Phone:</label>
                    <input type="text" class="form-control" name="phone">
                  </div>
                  <div class="mb-3 col-md-6">
                    <label class="form-label fw-bold">Address:</label>
                    <input type="text" class="form-control" name="address" required>
                  </div>
                </div>
                <div class="row">
                  <div class="mb-3 col-md-6">
                    <label class="form-label fw-bold">Order Type:</label>
                    <select name="order_type" class="form-control" required>
                      <option value="delivery">Delivery</option>
                      <option value="pickup">Pickup</option>
                      <option value="dinein">Dine-in</option>
                    </select>
                  </div>
                  <div class="mb-3 col-md-6">
                    <label class="form-label fw-bold">Status:</label>
                    <select name="status" class="form-control" required>
                      <option value="pending">Pending</option>
                      <option value="processing">Processing</option>
                      <option value="completed">Completed</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Order Items:</label>
                  <div id="order-items">
                    <div class="row mb-2 order-item-row" data-item-index="0">
                      <div class="col-md-3">
                        <select class="form-control category-select" name="items[0][category_id]" onchange="loadItems(this, 0)" required>
                          <option value="">Select Category</option>
                          <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <select class="form-control item-select" name="items[0][item_id]" onchange="updatePrice(this, 0)" required>
                          <option value="">Select Item</option>
                        </select>
                      </div>
                      <div class="col-md-2">
                        <input type="number" class="form-control unit-price" name="items[0][price]" readonly placeholder='unit price'>
                      </div>
                      <div class="col-md-2">
                        <input type="number" class="form-control quantity" name="items[0][quantity]" min="1" oninput="calculateTotal(this, 0)" required placeholder='quantity'>
                      </div>
                      <div class="col-md-2">
                        <input type="number" class="form-control total-price" name="items[0][total]" step="0.01" readonly>
                      </div>
                      <button type="button" class="btn btn-sm btn-danger remove-item-btn" ><i class="bi bi-trash"></i></button>.
                      <!-- <p onclick="removeOrderItem(this)">X</p> -->
                    </div>
                  </div>
                  <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addOrderItem()">Add Another Item</button>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Create Order</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>



    <script>// Replace form submission with AJAX
document.querySelector('#addOrderModal form').addEventListener('submit', function (e) {
  e.preventDefault(); // Prevent default submission

  const form = this;
  const formData = new FormData(form);
  const itemRows = document.querySelectorAll('.order-item-row');
  let hasErrors = false;

  // Validate item rows
  itemRows.forEach((row, index) => {
    const categorySelect = row.querySelector('.category-select');
    const itemSelect = row.querySelector('.item-select');
    const quantityInput = row.querySelector('.quantity');

    if (!categorySelect.value || !itemSelect.value || !quantityInput.value || quantityInput.value < 1) {
      hasErrors = true;
      row.style.border = '1px solid red';
    } else {
      row.style.border = 'none';
    }
  });

  if (hasErrors) {
    alert('Please complete all item fields correctly.');
    return;
  }

// Print FormData contents to console
  console.log('FormData Contents:');
  const formDataObject = {};
  for (const [key, value] of formData.entries()) {
    // Handle nested items array
    if (key.startsWith('items')) {
      const match = key.match(/items\[(\d+)\]\[(\w+)\]/);
      if (match) {
        const index = match[1];
        const field = match[2];
        if (!formDataObject.items) formDataObject.items = {};
        if (!formDataObject.items[index]) formDataObject.items[index] = {};
        formDataObject.items[index][field] = value;
      }
    } else {
      formDataObject[key] = value;
    }
  }
  console.log(formDataObject);

fetch('add_order.php', {
    method: 'POST',
    body: formData
})
.then(response => {
    if (!response.ok) {
        throw new Error('Network response was not ok: ' + response.statusText);
    }
    return response.text(); // Get raw text first
})
.then(text => {
    console.log('Raw response:', text); // Log raw response
    try {
        const data = JSON.parse(text);
        if (data.success) {
            alert(data.message);
            form.reset();
            bootstrap.Modal.getInstance(document.getElementById('addOrderModal')).hide();
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (e) {
        alert('Invalid JSON response: ' + text);
    }
})
.catch(error => {
    alert('An error occurred: ' + error.message);
});
});</script>

<script>

    let itemCount = 1;
    function addOrderItem() {
      const orderItemsDiv = document.getElementById('order-items');
      const newItem = document.createElement('div');
      newItem.className = 'row mb-2 order-item-row';
      newItem.dataset.itemIndex = itemCount;
      newItem.innerHTML = `
        <div class="col-md-3">
          <select class="form-control category-select" name="items[${itemCount}][category_id]" onchange="loadItems(this, ${itemCount})" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-control item-select" name="items[${itemCount}][item_id]" onchange="updatePrice(this, ${itemCount})" required>
            <option value="">Select Item</option>
          </select>
        </div>
        <div class="col-md-2">
          <input type="number" class="form-control unit-price" name="items[${itemCount}][price]" readonly placeholder="Unit Price">
        </div>
        <div class="col-md-2">
          <input type="number" class="form-control quantity" name="items[${itemCount}][quantity]" min="1" oninput="calculateTotal(this, ${itemCount})" required placeholder="Quantity">
        </div>
        <div class="col-md-2">
          <input type="number" class="form-control total-price" name="items[${itemCount}][total]" step="0.01" readonly >
        </div>
        <button type="button" class="btn btn-sm btn-danger remove-item-btn" onclick="removeOrderItem(this)">Remove</button>
      `;
      orderItemsDiv.appendChild(newItem);
      itemCount++;
    }

    // Function to remove an order item row
    function removeOrderItem(button) {
      if (document.querySelectorAll('.order-item-row').length > 1) {
        button.parentElement.remove();
      } else {
        alert('At least one item is required.');
      }
    }

    // Function to load items based on selected category
    function loadItems(categorySelect, index) {
      const categoryId = categorySelect.value;
      const itemSelect = categorySelect.parentElement.nextElementSibling.querySelector('.item-select');
      const unitPriceInput = categorySelect.parentElement.nextElementSibling.nextElementSibling.querySelector('.unit-price');
      const quantityInput = categorySelect.parentElement.nextElementSibling.nextElementSibling.nextElementSibling.querySelector('.quantity');
      const totalPriceInput = categorySelect.parentElement.nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling.querySelector('.total-price');

      // Clear previous items and reset fields
      itemSelect.innerHTML = '<option value="">Select Item</option>';
      unitPriceInput.value = '';
      quantityInput.value = '';
      totalPriceInput.value = '';

      if (categoryId) {

        // console.log('Fetching items for category ID:', categoryId);
        // Fetch items for the selected category via AJAX
        fetch('get_items.php?category_id=' + categoryId)
          .then(response => response.json())
          .then(data => {
            data.forEach(item => {
              const option = document.createElement('option');
              option.value = item.id;
              option.textContent = item.name;
              option.dataset.price = item.price;
              itemSelect.appendChild(option);
            });
          })
          .catch(error => console.error('Error fetching items:', error));
      }
    }

    // Function to update unit price when an item is selected
    function updatePrice(itemSelect, index) {
      const unitPriceInput = itemSelect.parentElement.nextElementSibling.querySelector('.unit-price');
      const quantityInput = itemSelect.parentElement.nextElementSibling.nextElementSibling.querySelector('.quantity');
      const totalPriceInput = itemSelect.parentElement.nextElementSibling.nextElementSibling.nextElementSibling.querySelector('.total-price');
      const selectedOption = itemSelect.options[itemSelect.selectedIndex];

      if (selectedOption && selectedOption.dataset.price) {
        unitPriceInput.value = parseFloat(selectedOption.dataset.price).toFixed(2);
        calculateTotal(quantityInput, index);
      } else {
        unitPriceInput.value = '';
        quantityInput.value = '';
        totalPriceInput.value = '';
      }
    }

    // Function to calculate total price for an item
    function calculateTotal(quantityInput, index) {
      const unitPriceInput = quantityInput.parentElement.previousElementSibling.querySelector('.unit-price');
      const totalPriceInput = quantityInput.parentElement.nextElementSibling.querySelector('.total-price');
      const quantity = parseInt(quantityInput.value) || 0;
      const unitPrice = parseFloat(unitPriceInput.value) || 0;
      const total = (quantity * unitPrice).toFixed(2);
      totalPriceInput.value = total;
    }
  </script>
</script>
      
  <script>
    const username = '<?php echo addslashes($first_name); ?>';
    const userRole = '<?php echo addslashes($userRole); ?>';
    const profilePicture = '<?php echo addslashes($profilePicture); ?>';
  </script>
  <script src="../scripts/components.js"></script>
  <!-- <script src="../scripts/orders.js"></script> -->

  <script>
    // Initialize charts with dynamic data
    document.addEventListener('DOMContentLoaded', function () {
      // Weekly Overview Chart (Line chart)
      const overviewCtx = document.getElementById('overviewChart').getContext('2d');
      new Chart(overviewCtx, {
        type: 'line',
        data: {
          labels: <?php echo json_encode($dayNames); ?>,
          datasets: [{
            label: 'Orders',
            data: <?php echo json_encode($weeklyData); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return context.parsed.y + ' orders';
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              }
            }
          }
        }
      });

      // Order Type Chart (Doughnut chart)
      const orderTypeCtx = document.getElementById('order_typeChart').getContext('2d');
      const orderTypeChart = new Chart(orderTypeCtx, {
        type: 'doughnut',
        data: {
          labels: ['Delivery', 'Pickup', 'Dine-in'],
          datasets: [{
            data: [
              <?php echo $delivery; ?>,
              <?php echo $pickup; ?>,
              <?php echo $dinein; ?>
            ],
            backgroundColor: [
              '#4BC0C0',
              '#FF9F40',
              '#9966FF'
            ],
            borderWidth: 0
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '70%',
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = Math.round((value / total) * 100);
                  return `${label}: ${value} (${percentage}%)`;
                }
              }
            }
          }
        }
      });

      // Update legend for order type chart
      const legendContainer = document.getElementById('chart-legend');
      const legendItems = [
        { color: '#4BC0C0', label: 'Delivery', value: <?php echo $delivery; ?> },
        { color: '#FF9F40', label: 'Pickup', value: <?php echo $pickup; ?> },
        { color: '#9966FF', label: 'Dine-in', value: <?php echo $dinein; ?> }
      ];

      legendContainer.innerHTML = ''; // Clear existing legend
      legendItems.forEach(item => {
        const legendItem = document.createElement('div');
        legendItem.className = 'legend-item';
        legendItem.innerHTML = `
        <span class="legend-color" style="background-color: ${item.color}"></span>
        <span class="legend-label">${item.label}</span>
        <span class="legend-value">${item.value}</span>
      `;
        legendContainer.appendChild(legendItem);
      });
    });


    function toggleDropdown(button) {
      const menu = button.nextElementSibling;
      const openMenus = document.querySelectorAll('.dropdown-menu');
      openMenus.forEach(m => { if (m !== menu) m.style.display = 'none'; });

      menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }

    // Modal handling
    function openModal(action, data) {
      const modal = document.getElementById("modal");
      const modalContent = document.getElementById("modal-content");

      if (action === 'view') {
        modalContent.innerHTML = `<h3>Order Details</h3><pre>${JSON.stringify(data, null, 2)}</pre>`;
      } else if (action === 'edit') {
        modalContent.innerHTML = `<h3>Edit Order</h3><p>(Implement edit form here)</p>`;
      } else if (action === 'delete') {
        modalContent.innerHTML = `<h3>Delete Confirmation</h3>
      <p>Are you sure you want to delete order #${data}?</p>
      <button onclick="confirmDelete(${data})">Yes</button>
      <button onclick="closeModal()">Cancel</button>`;
      }

      modal.style.display = 'block';
    }
    function closeModal() {
      document.getElementById("modal").style.display = 'none';
    }
    function confirmDelete(orderId) {
      alert('Order deleted: ' + orderId); // Replace with AJAX call
      closeModal();
    }

  </script>
</body>

</html>