<?php
session_start();

if (!isset($_SESSION['user']['id']) || !isset($_SESSION['user']['role'])) {
    header("Location: /chinnese-restaurant/login/");
    exit();
}

$username = $_SESSION['user']['email'] ?? '';
$userRole = $_SESSION['user']['role'] ?? '';
$profilePicture = $_SESSION['user']['profile_picture'] ?? 'https://picsum.photos/40';

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
          pageLength: 5,
        });
      });

      // Replace your current DataTable initialization with:
// $(document).ready(function () {
//     $('#orders-table').DataTable({
//         pageLength: 5,
//         initComplete: function(settings, json) {
//             console.log('DataTable initialized');
//         }
//     });
// });
    </script>
    <title>Overview</title>
    <link rel="stylesheet" href="../assets/styles/general.css" />
    <link rel="stylesheet" href="../assets/styles/panels.css" />
    <link rel="stylesheet" href="../assets/styles/overview.css" />

    <style>
      .orderDetailsPopup {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
  justify-content: center;
  align-items: center;
}

.popup-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  max-width: 600px;
  width: 90%;
  max-height: 80vh;
  overflow-y: auto;
  position: relative;
}

.popup-content .close {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 24px;
  cursor: pointer;
  color: #333;
}

.popup-content .close:hover {
  color: #f00;
}

.items-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.items-table th,
.items-table td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}

.items-table th {
  background-color: #f2f2f2;
  font-weight: bold;
}

.items-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}
.view-items-btn {
  color: #28a745;
  font-weight: 900;
  border: none;
}
.view-items-btn:hover {
  color: #218838;
  background-color: white;
  /* border-color: #218838; */
}
.view-items-btn:active {
  background-color: #1e7e34;
  /* border-color: #1e7e34; */
}


.slider-wrapper {
  position: relative;
  overflow: hidden;
  width: 100%;
}
.slider {
  display: flex;
  flex-wrap: nowrap;
  transition: transform 0.5s ease;
}
.slide {
  flex: 0 0 300px;
  margin-right: 20px;
  box-sizing: border-box;
}
.slider-controls {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}
.slider-controls button {
  background: #fff;
  border: 1px solid #ddd;
  padding: 5px;
  cursor: pointer;
}
.slider-controls button svg {
  width: 24px;
  height: 24px;
}
.star.filled {
  fill: #f39c12;
}
.star {
  fill: #ddd;
  width: 16px;
  height: 16px;
}


.slide {
  flex: 0 0 300px;
  margin-right: 20px;
  box-sizing: border-box;
  min-width: 300px; /* Ensure consistent width */
}

.slider {
  display: flex;
  transition: transform 0.3s ease;
  width: max-content; /* Allow the slider to expand beyond container */
}

.slider-controls {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 15px;
}

.slider-controls button {
  background: #ddd;
  border: none;
  padding: 10px;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.3s ease;
}

.slider-controls button:hover {
  background: #ccc;
}

.slider-controls button svg {
  width: 20px;
  height: 20px;
  stroke: #333;
}

    </style>
  </head>
  <body class="flex">
    <main>
      <div class="container flex">
        <section class="content">
          <div class="grid g-af3">
         <div class="grid-item stat">
    <div class="image">
      <svg class="icon"><use href="#receipt"></use></svg>
    </div>
    <div class="details">
      <small>Total Orders</small>
      <div class="flex justify-between align-center full-width">
        <h3 id="total-orders">0</h3>
        <span><small id="orders-percentage">0%</small></span>
      </div>
    </div>
  </div>
  <div class="grid-item stat">
    <div class="image">
      <svg class="icon"><use href="#users"></use></svg>
    </div>
    <div class="details">
      <small>Total Customers</small>
      <div class="flex justify-between align-center full-width">
        <h3 id="total-customers">0</h3>
        <span><small id="customers-percentage">0%</small></span>
      </div>
    </div>
  </div>
  <div class="grid-item stat">
    <div class="image">
      <svg class="icon"><use href="#currency"></use></svg>
    </div>
    <div class="details">
      <small>Total Revenue</small>
      <div class="flex justify-between align-center full-width">
        <h3 id="total-revenue">$0</h3>
        <span><small id="revenue-percentage">0%</small></span>
      </div>
    </div>
  </div>
            <div class="grid-item chart-container half-width">
              <div class="title flex justify-between align-center">
                <div>
                  <p>Total Revenue</p>
                  <h4>$114,852</h4>
                </div>
                <div class="filter">
                  <strong class="flex align-center justify-content-xxl-between">
                    This Week
                    <svg class="icon caret"><use href="#caret-down"></use></svg>
                  </strong>
                  <div class="dropdown"></div>
                </div>
              </div>
              <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
            <div class="grid-item chart-container half-width">
              <div class="title flex justify-between align-center">
                <h6>Top Categories</h6>
                <div class="filter">
                  <strong class="flex align-center justify-content-xxl-between">
                    This Week
                    <svg class="icon caret"><use href="#caret-down"></use></svg>
                  </strong>
                  <div class="dropdown"></div>
                </div>
              </div>
              <div>
                <canvas id="categoryChart" width="300" height="300"></canvas>
              </div>
            </div>
            <div class="grid-item chart-container half-width">
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
              <canvas id="ordersChart" width="400" height="200"></canvas>
            </div>
            <div class="grid-item">
              <div class="flex justify-between align-center">
                <h6>Orders Types</h6>
                <div class="filter">
                  <strong class="flex align-center justify-content-xxl-between">
                    This Week
                    <svg class="icon caret"><use href="#caret-down"></use></svg>
                  </strong>
                  <div class="dropdown"></div>
                </div>
              </div>
              <div class="orders flex column">
                <div class="order-type">
                  <div class="image">
                    <svg class="icon"><use href="#dine"></use></svg>
                  </div>
                  <div class="details">
                    <div class="label">
                      <span>Dine-In</span>
                      <span>45%</span>
                      <span>900</span>
                    </div>
                    <div class="bar">
                      <div class="fill" style="width: 45%"></div>
                    </div>
                  </div>
                </div>
                <div class="order-type">
                  <div class="image">
                    <svg class="icon"><use href="#fork"></use></svg>
                  </div>
                  <div class="details">
                    <div class="label">
                      <span>Takeaway</span>
                      <span>30%</span>
                      <span>600</span>
                    </div>
                    <div class="bar">
                      <div class="fill" style="width: 30%"></div>
                    </div>
                  </div>
                </div>
                <div class="order-type">
                  <div class="image">
                    <svg class="icon"><use href="#moped"></use></svg>
                  </div>
                  <div class="details">
                    <div class="label">
                      <span>Order</span>
                      <span>25%</span>
                      <span>500</span>
                    </div>
                    <div class="bar">
                      <div class="fill" style="width: 25%"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="table card">
  <div class="flex justify-between align-center">
    <h6>Recent Orders</h6>
    <div class="flex align-center justify-end">
      <div class="search">
        <button>
          <svg class="icon"><use href="#search"></use></svg>
        </button>
        <input
          type="text"
          id="filterInput"
          onkeyup="filterTable()"
          placeholder="Search anything"
        />
      </div>
      <div class="filter">
        <strong class="flex align-center justify-content-xxl-between">
          This Week
          <svg class="icon caret"><use href="#caret-down"></use></svg>
        </strong>
        <div class="dropdown"></div>
      </div>
      <button class="button">See All Orders</button>
    </div>
  </div>
  <table id="orders-table">
    <thead class="header">
      <tr>
        <th onclick="sortTable(0)">Order ID</th>
        <th onclick="sortTable(1)">Menu</th>
        <th onclick="sortTable(2)">Qty</th>
        <th onclick="sortTable(3)">Amount</th>
        <th onclick="sortTable(4)">Customer</th>
        <th onclick="sortTable(5)">Status</th>
      </tr>
    </thead>
    <tbody id="orders-table-body">
      <!-- Orders will be dynamically inserted here -->
    </tbody>
  </table>
</div>
        
<div class="reviews">
  <div class="title flex justify-between align-center">
    <h4>Customer Reviews</h4>
    <a href="#">See More Reviews</a>
  </div>
  <!-- <div class="slider-wrapper">
    <div class="slider flex nowrap" id="reviews-slider">
    
    </div>
  </div> -->

  <div class="slider-wrapper">
  <div class="slider flex nowrap" id="reviews-slider">
    <!-- Reviews will be dynamically inserted here -->
  </div>
  <!-- <div class="slider-controls"  >
  <button class="prev" style="display: block !important; background: #ddd; padding: 10px;">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="15 18 9 12 15 6"></polyline>
    </svg>
  </button>
  <button class="next" style="display: block !important; background: #ddd; padding: 10px;">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="9 18 15 12 9 6"></polyline>
    </svg>
  </button>
</div> -->

<div class="slider-controls">
  <button class="prev">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="15 18 9 12 15 6"></polyline>
    </svg>
  </button>
  <button class="next">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="9 18 15 12 9 6"></polyline>
    </svg>
  </button>
</div>
</div>
</div>
       
        </section>
        <section class="aside flex column">
          <div class="title flex justify-between align-center">
            <h4>Trending Menus</h4>
            <div class="card filter">
              <strong class="flex align-center justify-content-xxl-between">
                This Week
                <svg class="icon caret"><use href="#caret-down"></use></svg>
              </strong>
              <div class="dropdown"></div>
            </div>
          </div>
          <div class="trending flex column">
            <div class="card">
              <div class="image">
                <img src="../assets/img/food.png" alt="" />
              </div>
              <div class="details flex column">
                <div>
                  <h4>Spicy Noodles Delight</h4>
                  <small>Noodles</small>
                </div>
                <div class="flex justify-between align-center details">
                  <div class="flex align-center justify-between ratings">
                    <p class="flex align-center">
                      <svg class="icon"><use href="#review"></use></svg>4.9
                    </p>
                    <p class="flex align-center">
                      <svg class="icon"><use href="#cart"></use></svg>350
                    </p>
                  </div>
                  <h4 class="price">$18.00</h4>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="image">
                <img src="../assets/img/food.png" alt="" />
              </div>
              <div class="details flex column">
                <div>
                  <h4>Spicy Noodles Delight</h4>
                  <small>Noodles</small>
                </div>
                <div class="flex justify-between align-center details">
                  <div class="flex align-center justify-between ratings">
                    <p class="flex align-center">
                      <svg class="icon"><use href="#review"></use></svg>4.9
                    </p>
                    <p class="flex align-center">
                      <svg class="icon"><use href="#cart"></use></svg>350
                    </p>
                  </div>
                  <h4 class="price">$18.00</h4>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="image">
                <img src="../assets/img/food.png" alt="" />
              </div>
              <div class="details flex column">
                <div>
                  <h4>Spicy Noodles Delight</h4>
                  <small>Noodles</small>
                </div>
                <div class="flex justify-between align-center details">
                  <div class="flex align-center justify-between ratings">
                    <p class="flex align-center">
                      <svg class="icon"><use href="#review"></use></svg>4.9
                    </p>
                    <p class="flex align-center">
                      <svg class="icon"><use href="#cart"></use></svg>350
                    </p>
                  </div>
                  <h4 class="price">$18.00</h4>
                </div>
              </div>
            </div>
          </div>
          <div class="activity-container card flex column">
            <div class="activity-header">
              <h2>Recent Activity</h2>
              <div class="dots">...</div>
            </div>

            <div class="activity-item">
              <div class="icon-container box">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  class="feather feather-package"
                >
                  <path d="M12 3L20 7.5V16.5L12 21L4 16.5V7.5L12 3z"></path>
                </svg>
              </div>
              <div class="activity-details">
                <h5>Sylvester Quilt <span>Inventory Manager</span></h5>
                <p>updated inventory - 10 units of "Organic Chicken Breast"</p>
                <p class="activity-time">11:20 AM</p>
              </div>
              <div class="vertical-line"></div>
            </div>

            <div class="activity-item">
              <div class="icon-container check">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  class="feather feather-check-square"
                >
                  <polyline points="9 11 12 14 22 4"></polyline>
                  <path
                    d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"
                  ></path>
                </svg>
              </div>
              <div class="activity-details">
                <h5>Maria Kings <span>Kitchen Admin</span></h5>
                <p>marked order #ORD1028 as completed</p>
                <p class="activity-time">11:00 AM</p>
              </div>
              <div class="vertical-line"></div>
            </div>

            <div class="activity-item">
              <div class="icon-container calendar">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  class="feather feather-calendar"
                >
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </div>
              <div class="activity-details">
                <h5>William Smith <span>Receptionist</span></h5>
                <p>added new reservation for 4 guests at 7:00 PM</p>
                <p class="activity-time">10:20 AM</p>
              </div>
              <div class="vertical-line"></div>
            </div>
          </div>
        </section>
      </div>
    </main>
    <script>
// Pass PHP variables to JavaScript
const username = '<?php echo addslashes($username); ?>';
const userRole = '<?php echo addslashes($userRole); ?>';
const profilePicture = '<?php echo addslashes($profilePicture); ?>';
</script>
<script src="your-script.js"></script>
    <script src="../scripts/charts.js"></script>
    <script src="../scripts/components.js"></script>

    <script>
// Function to fetch and display orders


async function fetchAndDisplayOrders() {
  try {
    const response = await fetch('get_orders.php');
//     const response = await fetch('get_orders.php', {
  
// });
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const orders = await response.json();
    const tableBody = document.getElementById('orders-table-body');

    
    tableBody.innerHTML = '';

    orders.forEach(order => {
      // Calculate total quantity

      console.log('Order data:', order); // Debug: Log the order data
      // console.log('Processing order:', order); // Debug: Log the order being processed
      const totalQty = order.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
      
      // Format customer email
      const customerEmail = order.user_name || order.guest_name || 'Guest';
      
      // Determine status class
      let statusClass = '';
      if (order.status.toLowerCase().includes('process')) statusClass = 'process';
      else if (order.status.toLowerCase().includes('cancel')) statusClass = 'cancelled';
      else if (order.status.toLowerCase().includes('complete')) statusClass = 'completed';

      const row = document.createElement('tr');
      row.innerHTML = `
        <td>ORD${order.id}</td>
        <td>
          <button class="view-items-btn" data-order-id="${order.id}" 
                  data-items='${JSON.stringify(order.items)}'>
            View details
          </button>
        </td>
        <td>${totalQty}</td>
        <td>$${parseFloat(order.total_amount).toFixed(2)}</td>
        <td>${customerEmail}</td>
        <td><span class="status ${statusClass}">${order.status}</span></td>
      `;

      tableBody.appendChild(row);
    });

    // Initialize DataTable
    if ($.fn.DataTable.isDataTable('#orders-table')) {
      $('#orders-table').DataTable().destroy();
    }
    $('#orders-table').DataTable({ pageLength: 5 });

    // Add event listeners to view buttons
    document.querySelectorAll('.view-items-btn').forEach(button => {
      button.addEventListener('click', (e) => {
        const orderId = e.target.dataset.orderId;
        const items = JSON.parse(e.target.dataset.items);
        showOrderItemsPopup(orderId, items);
      });
    });

  } catch (error) {
    console.error('Error in fetchAndDisplayOrders:', error);
  }
}

function showOrderItemsPopup(orderId, items) {
  try {
    console.log('Items received:', items); // Debug: Log the items array

    let popup = document.getElementById('order-items-popup');
    if (!popup) {
      popup = document.createElement('div');
      popup.id = 'order-items-popup';
      popup.className = 'orderDetailsPopup';
      document.body.appendChild(popup);
      console.log('Created new popup element');
    }

    // Handle empty or invalid items array
    const itemsHtml = Array.isArray(items) && items.length > 0
      ? items.map(item => {
          // Ensure price and quantity are numbers
          const price = parseFloat(item.price) || 0;
          const quantity = parseInt(item.quantity, 10) || 0;
          const itemName = item.item_name || 'Unknown Item';

          console.log('Processing item:', { itemName, price, quantity }); // Debug: Log each item

          return `
            <tr>
              <td>${itemName}</td>
              <td>${quantity}</td>
              <td>$${price.toFixed(2)}</td>
              <td>$${(price * quantity).toFixed(2)}</td>
            </tr>
          `;
        }).join('')
      : '<tr><td colspan="4">No items found for this order.</td></tr>';

    popup.innerHTML = `
      <div class="popup-content">
        <span class="close">×</span>
        <h2>Order ORD${orderId} Items</h2>
        <table class="items-table">
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            ${itemsHtml}
          </tbody>
        </table>
      </div>
    `;

    popup.style.display = 'flex'; // Use flex to center content
    console.log('Popup displayed for order:', orderId);

    // Close button event listener
    const closeButton = popup.querySelector('.close');
    if (closeButton) {
      closeButton.addEventListener('click', () => {
        popup.style.display = 'none';
        console.log('Popup closed');
      });
    }

    // Close popup when clicking outside content
    popup.addEventListener('click', (e) => {
      if (e.target === popup) {
        popup.style.display = 'none';
        console.log('Popup closed by clicking outside');
      }
    });
  } catch (error) {
    console.error('Error in showOrderItemsPopup:', error);
  }
}


 
async function fetchAndDisplayReviews() {
  try {
    const response = await fetch('get_reviews.php');
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const reviews = await response.json();

    const slider = document.getElementById('reviews-slider');
    if (!slider) {
      console.error('Reviews slider element not found');
      return;
    }

    slider.innerHTML = '';

    if (!Array.isArray(reviews) || reviews.length === 0) {
      slider.innerHTML = '<div class="card slide"><p>No reviews available</p></div>';
      console.warn('No reviews found or invalid response');
      return;
    }

    reviews.forEach(review => {
      const reviewCard = document.createElement('div');
      reviewCard.className = 'card slide';

      let starsHtml = '';
      for (let i = 0; i < 5; i++) {
        if (i < Math.floor(review.rating || 0)) {
          starsHtml += '<svg class="icon star filled"><use href="#star"></use></svg>';
        } else {
          starsHtml += '<svg class="icon star"><use href="#star"></use></svg>';
        }
      }

      reviewCard.innerHTML = `
        <h4>${review.dish_name || 'Dish'}</h4>
        <p>${review.review_text || 'No comment'}</p>
        <div class="flex align-baseline">
          <p>${review.reviewer_name || 'Anonymous'}</p>
          <small>- ${new Date(review.review_date || Date.now()).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</small>
        </div>
        <div class="flex align-center">
          <div class="stars">${starsHtml}</div>
          <small>(${parseFloat(review.rating || 0).toFixed(1)})</small>
        </div>
      `;

      slider.appendChild(reviewCard);
    });

    initializeSlider();
  } catch (error) {
    console.error('Error fetching reviews:', error);
    const slider = document.getElementById('reviews-slider');
    if (slider) {
      slider.innerHTML = '<div class="card slide"><p>Reviews unavailable</p></div>';
    }
  }
}

function initializeSlider() {
  const slider = document.getElementById('reviews-slider');
  if (!slider) {
    console.error('Slider element not found');
    return;
  }

  const slides = slider.querySelectorAll('.slide');
  const prevButton = document.querySelector('.slider-controls .prev');
  const nextButton = document.querySelector('.slider-controls .next');
  
  if (!slides.length) {
    console.warn('No slides found');
    return;
  }

  let currentIndex = 0;
  const slideWidth = slides[0].offsetWidth + 20; // Include margin
  const slidesToShow = Math.min(3, slides.length); // Show up to 3 slides
  const totalSlides = slides.length;

  console.log('Slider initialized', { 
    slideWidth, 
    totalSlides,
    slidesToShow 
  });

  // Always show controls if there are slides
  if (prevButton) prevButton.style.display = 'flex';
  if (nextButton) nextButton.style.display = 'flex';

  function updateSlider() {
    const maxIndex = Math.max(totalSlides - slidesToShow, 0);
    currentIndex = Math.min(Math.max(currentIndex, 0), maxIndex);
    
    const offset = -currentIndex * slideWidth;
    slider.style.transform = `translateX(${offset}px)`;
    
    console.log('Slider position:', {
      currentIndex,
      offset,
      maxIndex
    });
  }

  function nextSlide() {
    currentIndex = Math.min(currentIndex + 1, totalSlides - slidesToShow);
    updateSlider();
  }

  function prevSlide() {
    currentIndex = Math.max(currentIndex - 1, 0);
    updateSlider();
  }

  // Clear existing listeners
  const newPrev = prevButton.cloneNode(true);
  prevButton.parentNode.replaceChild(newPrev, prevButton);
  const newNext = nextButton.cloneNode(true);
  nextButton.parentNode.replaceChild(newNext, nextButton);

  // Add new listeners
  document.querySelector('.slider-controls .prev').addEventListener('click', prevSlide);
  document.querySelector('.slider-controls .next').addEventListener('click', nextSlide);

  // Initialize
  updateSlider();

  // Auto-slide
  let autoSlideInterval = setInterval(nextSlide, 5000);

  slider.parentElement.addEventListener('mouseenter', () => {
    clearInterval(autoSlideInterval);
  });

  slider.parentElement.addEventListener('mouseleave', () => {
    autoSlideInterval = setInterval(nextSlide, 5000);
  });
}
// Call the functions when the page loads
// document.addEventListener('DOMContentLoaded', () => {
//   fetchAndDisplayOrders();
//   fetchAndDisplayReviews();
// });


// Your existing filterTable function

</script>
    <script>
      function filterTable() {
        // Get the value in the search field.
        let input = document.getElementById("filterInput");
        let filter = input.value.toUpperCase();

        // Get the rows of the table.
        let table = document.getElementById("my-table");
        let rows = table.getElementsByTagName("tr");

        // Iterate over the rows.
        for (let i = 1; i < rows.length; i++) {
          // Get the cell in the iterated row.
          let cells = rows[i].getElementsByTagName("td");
          let match = false;

          // Iterate over the cells. If there is a match in one of the cell, assign true to the dedicated match variable.
          for (let j = 0; j < cells.length; j++) {
            if (cells[j].innerText.toUpperCase().includes(filter)) {
              match = true;
              break;
            }
          }

          // If there is a match, leave the row visible; otherwise, hide the row.
          rows[i].style.display = match ? "" : "none";
        }
      }
    </script>

    <script>
      // Function to fetch and display stats
async function fetchAndDisplayStats() {
  try {
    const response = await fetch('get_stats.php');
    const stats = await response.json();
    
    // Check for error response
    if (stats.error) {
      console.error('Error fetching stats:', stats.error);
      return;
    }
    
    // Update Total Orders
    document.getElementById('total-orders').textContent = stats.total_orders.toLocaleString();
    document.getElementById('orders-percentage').textContent = `${stats.orders_percentage}%`;
    
    // Update Total Customers
    document.getElementById('total-customers').textContent = stats.total_customers.toLocaleString();
    document.getElementById('customers-percentage').textContent = `${stats.customers_percentage}%`;
    
    // Update Total Revenue
    document.getElementById('total-revenue').textContent = `$${parseFloat(stats.total_revenue).toLocaleString()}`;
    document.getElementById('revenue-percentage').textContent = `${stats.revenue_percentage}%`;
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
}

// Update DOMContentLoaded to include stats fetching
// document.addEventListener('DOMContentLoaded', () => {
//   fetchAndDisplayStats();
//   fetchAndDisplayOrders();
//   fetchAndDisplayReviews();
// });


document.addEventListener('DOMContentLoaded', () => {
  fetchAndDisplayOrders();
  fetchAndDisplayReviews().then(initializeSlider);
  fetchAndDisplayStats();
});
    </script>
  </body>
</html>
