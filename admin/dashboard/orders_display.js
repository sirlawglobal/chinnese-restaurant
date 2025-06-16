async function fetchAndDisplayOrders() {
  try {
    const response = await fetch('get_orders.php');
    const orders = await response.json();
    const tableBody = document.getElementById('orders-table-body');
    tableBody.innerHTML = '';

    for (const order of orders) {
      const orderItemsResponse = await fetch(`get_order_items.php?order_id=${order.id}`);
      const orderItems = await orderItemsResponse.json();

      console.log('Order:', order);

      const row = document.createElement('tr');
      let statusClass = '';
      if (order.status.toLowerCase().includes('process')) statusClass = 'process';
      else if (order.status.toLowerCase().includes('cancel')) statusClass = 'cancelled';
      else if (order.status.toLowerCase().includes('complete')) statusClass = 'completed';

      const total = orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2);

      row.innerHTML = `
        <td>ORD${order.id}</td>
        <td><button class="view-items-btn" data-order-id="${order.id}" data-items='${JSON.stringify(orderItems)}'>View</button></td>
        <td>${orderItems.length}</td>
        <td>$${total}</td>
        <td>${order.user_email || order.guest_email || 'Guest'}</td>
        <td><span class="status ${statusClass}">${order.status}</span></td>
      `;

      tableBody.appendChild(row);
    }

    // Destroy any existing DataTable instance to prevent conflicts
    if ($.fn.DataTable.isDataTable('#orders-table')) {
      $('#orders-table').DataTable().destroy();
    }

    // Initialize DataTable
    $('#orders-table').DataTable({ pageLength: 5 });

    // Add event listeners for view buttons
    document.querySelectorAll('.view-items-btn').forEach(button => {
      button.addEventListener('click', (e) => {
        const orderId = e.target.dataset.orderId;
        const items = JSON.parse(e.target.dataset.items);
        showOrderItemsPopup(orderId, items);
      });
    });

  } catch (error) {
    console.error('Error fetching orders:', error);
  }
}

function showOrderItemsPopup(orderId, items) {
  let popup = document.getElementById('order-items-popup');
  if (!popup) {
    popup = document.createElement('div');
    popup.id = 'order-items-popup';
    popup.className = 'orderDetailsPopup';
    document.body.appendChild(popup);
  }

  let itemsHtml = items.map(item => `
    <tr>
      <td>${item.item_name}</td>
      <td>${item.quantity}</td>
      <td>$${item.price.toFixed(2)}</td>
      <td>$${(item.price * item.quantity).toFixed(2)}</td>
    </tr>
  `).join('');

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

  popup.style.display = 'block';

  popup.querySelector('.close').addEventListener('click', () => {
    popup.style.display = 'none';
  });

  popup.addEventListener('click', (e) => {
    if (e.target === popup) {
      popup.style.display = 'none';
    }
  });
}

async function fetchAndDisplayStats() {
  try {
    const response = await fetch('get_stats.php');
    const stats = await response.json();

    if (stats.error) {
      console.error('Error fetching stats:', stats.error);
      return;
    }

    document.getElementById('total-orders').textContent = stats.total_orders.toLocaleString();
    document.getElementById('orders-percentage').textContent = `${stats.orders_percentage}%`;
    document.getElementById('total-customers').textContent = stats.total_customers.toLocaleString();
    document.getElementById('customers-percentage').textContent = `${stats.customers_percentage}%`;
    document.getElementById('total-revenue').textContent = `$${parseFloat(stats.total_revenue).toLocaleString()}`;
    document.getElementById('revenue-percentage').textContent = `${stats.revenue_percentage}%`;
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
}

async function fetchAndDisplayReviews() {
  try {
    const response = await fetch('get_reviews.php');
    const reviews = await response.json();
    const slider = document.getElementById('reviews-slider');
    slider.innerHTML = '';

    reviews.forEach(review => {
      const reviewCard = document.createElement('div');
      reviewCard.className = 'card';

      let starsHtml = '';
      for (let i = 0; i < 5; i++) {
        starsHtml += i < Math.floor(review.rating)
          ? '<svg class="icon star filled"><use href="#star"></use></svg>'
          : '<svg class="icon star"><use href="#star"></use></svg>';
      }

      reviewCard.innerHTML = `
        <h4>${review.dish_name}</h4>
        <p>${review.review_text}</p>
        <div class="flex align-baseline">
          <p>${review.reviewer_name}</p>
          <small>- ${new Date(review.review_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</small>
        </div>
        <div class="flex align-center">
          <div class="stars">${starsHtml}</div>
          <small>(${review.rating.toFixed(1)})</small>
        </div>
      `;

      slider.appendChild(reviewCard);
    });
  } catch (error) {
    console.error('Error fetching reviews:', error);
  }
}

function filterTable() {
  let input = document.getElementById('filterInput');
  let filter = input.value.toUpperCase();
  let table = document.getElementById('orders-table');
  let rows = table.getElementsByTagName('tr');

  for (let i = 1; i < rows.length; i++) {
    let cells = rows[i].getElementsByTagName('td');
    let match = false;

    for (let j = 0; j < cells.length; j++) {
      if (cells[j].innerText.toUpperCase().includes(filter)) {
        match = true;
        break;
      }
    }

    rows[i].style.display = match ? '' : 'none';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  fetchAndDisplayStats();
  fetchAndDisplayOrders();
  fetchAndDisplayReviews();
});