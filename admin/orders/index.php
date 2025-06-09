<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script
      type="text/javascript"
      src="https://code.jquery.com/jquery-3.3.1.min.js"
    ></script>
    <link
      rel="stylesheet"
      href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css"
    />
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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
</style>
  </head>
  <body class="flex">
    <main>
      <div class="content">
      

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
        case 'processing': $processing++; break;
        case 'completed': $completed++; break;
        case 'cancelled': $cancelled++; break;
    }
    
    // Order type counts
    switch (strtolower($order['order_type'])) {
        case 'delivery': $delivery++; break;
        case 'pickup': $pickup++; break;
        case 'dinein': $dinein++; break;
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
                <svg class="icon"><use href="#receipt"></use></svg>
                <svg class="icon"><use href="#dots"></use></svg>
              </div>
              <h5><?php echo $totalOrders; ?></h5>
              <p>Total Orders</p>
            </div>
            <div class="grid-item card">
              <div class="flex justify-between align-center">
                <svg class="icon"><use href="#dc"></use></svg>
                <svg class="icon"><use href="#dots"></use></svg>
              </div>
              <h5><?php echo $processing; ?></h5>
              <p>On Process</p>
            </div>
            <div class="grid-item card">
              <div class="flex justify-between align-center">
                <svg class="icon"><use href="#check"></use></svg>
                <svg class="icon"><use href="#dots"></use></svg>
              </div>
              <h5><?php echo $completed; ?></h5>
              <p>Completed</p>
            </div>
            <div class="grid-item card">
              <div class="flex justify-between align-center">
                <svg class="icon"><use href="#receiptx"></use></svg>
                <svg class="icon"><use href="#dots"></use></svg>
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
                  <svg class="icon caret"><use href="#caret-down"></use></svg>
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
                  <svg class="icon caret"><use href="#caret-down"></use></svg>
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

        <script>
    // Verify PHP values are reaching JavaScript
    console.log("PHP Values:", {
        totalOrders: <?php echo $totalOrders; ?>,
        processing: <?php echo $processing; ?>,
        completed: <?php echo $completed; ?>,
        cancelled: <?php echo $cancelled; ?>
    });
</script>
        <div class="card">
          <table id="my-table">
            <thead class="header">
              <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Order Type</th>
                <th>Address</th>
                <th>Qty</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($orders as $order) {
                $orderId = $order['id'];
                
                // Fetch order items
                $items = db_query("SELECT * FROM order_items WHERE order_id = :order_id", ['order_id' => $orderId], 'assoc');
                
                $totalItems = 0;
                foreach ($items as $item) {
                  $totalItems += $item['quantity'];
                }
                
                // Format status class
                $statusClass = strtolower(str_replace(' ', '', $order['status']));
                $orderType = ucfirst($order['order_type']);
                $address = !empty($order['delivery_address']) ? htmlspecialchars($order['delivery_address']) : '-';
                $customer = !empty($order['customer_name']) ? htmlspecialchars($order['customer_name']) : htmlspecialchars($order['guest_email']);
                $totalAmount = '$' . number_format($order['total_amount'], 2);
                
                // Format date/time
                $date = !empty($order['schedule_date']) ? $order['schedule_date'] : date('Y-m-d', strtotime($order['created_at']));
                $time = !empty($order['schedule_time']) ? date('h:i A', strtotime($order['schedule_time'])) : date('h:i A', strtotime($order['created_at']));
              ?>
              <tr>
                <td>ORD<?php echo $orderId; ?></td>
                <td><?php echo $date; ?><br><small><?php echo $time; ?></small></td>
                <td><?php echo $customer; ?></td>
                <td><span class="type <?php echo strtolower($order['order_type']); ?>"><?php echo $orderType; ?></span></td>
                <td><span class="address"><?php echo $address; ?></span></td>
                <td><?php echo $totalItems; ?></td>
                <td><?php echo $totalAmount; ?></td>
                <td><span class="status <?php echo $statusClass; ?>"><?php echo ucfirst($order['status']); ?></span></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
    <script src="../scripts/components.js"></script>
    <!-- <script src="../scripts/orders.js"></script> -->
    <!-- <script>
      // Initialize charts with dynamic data
      document.addEventListener('DOMContentLoaded', function() {
        // Overview Chart (Line/Bar chart)
        const overviewCtx = document.getElementById('overviewChart').getContext('2d');
        new Chart(overviewCtx, {
          type: 'line',
          data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
              label: 'Orders',
              data: [12, 19, 15, 17, 25, 22, 30],
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              borderColor: 'rgba(75, 192, 192, 1)',
              borderWidth: 2,
              tension: 0.4
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                display: false
              }
            },
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
        
        // Order Type Chart (Doughnut chart)
        const orderTypeCtx = document.getElementById('order_typeChart').getContext('2d');
        new Chart(orderTypeCtx, {
          type: 'doughnut',
          data: {
            labels: ['Delivery', 'Pickup', 'Dine-in'],
            datasets: [{
              data: [<?php echo $delivery; ?>, <?php echo $pickup; ?>, <?php echo $dinein; ?>],
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
            cutout: '70%',
            plugins: {
              legend: {
                display: false
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
    </script> -->

    <script>
  // Initialize charts with dynamic data
  document.addEventListener('DOMContentLoaded', function() {
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
              label: function(context) {
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
              label: function(context) {
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
</script>
  </body>
</html>