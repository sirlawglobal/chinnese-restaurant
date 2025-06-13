const notifications = {
  count: 0,
  items: [],
  container: null,
  pusher: null,

  init: async function () {
    if (this.container) {
      console.log('Notifications container already initialized');
      return;
    }

    console.log('Initializing notifications...');
    await this.loadFromServer(); // Fetch from server instead of localStorage

    this.container = document.createElement("div");
    this.container.className = "notifications-container";
    document.body.appendChild(this.container);
    console.log('Notifications container appended to body:', this.container);

    this.update();
    this.connectPusher();

    document.addEventListener("click", (e) => {
      const isNotificationButton = e.target.closest(".notification-btn");
      const isInsideContainer = e.target.closest(".notifications-container");
      if (!isInsideContainer && !isNotificationButton) {
        console.log('Hiding notifications dropdown');
        this.hide();
      }
    });
  },

  loadFromServer: async function () {
    try {
      const response = await fetch('/api/notifications.php?action=fetch', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
      });
      const data = await response.json();
      if (data.success) {
        this.items = data.notifications;
        this.count = this.items.filter(item => !item.read).length;
        console.log('Loaded notifications from server:', this.items);
      } else {
        console.error('Error fetching notifications:', data.message);
      }
    } catch (error) {
      console.error('Error loading notifications from server:', error);
    }
  },

  saveToServer: async function (notificationId) {
    try {
      const response = await fetch('/api/notifications.php?action=mark_read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ notification_id: notificationId })
      });
      const data = await response.json();
      if (data.success) {
        console.log('Notification marked as read on server:', notificationId);
      } else {
        console.error('Error marking notification as read:', data.message);
      }
    } catch (error) {
      console.error('Error saving to server:', error);
    }
  },

  markAllReadOnServer: async function () {
    try {
      const response = await fetch('/api/notifications.php?action=mark_all_read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
      });
      const data = await response.json();
      if (data.success) {
        console.log('All notifications marked as read on server');
      } else {
        console.error('Error marking all notifications as read:', data.message);
      }
    } catch (error) {
      console.error('Error marking all notifications as read:', error);
    }
  },

  connectPusher: function () {
    if (this.pusher) return;
    console.log('Initializing Pusher connection...');
    try {
      this.pusher = new Pusher('c0ccafac1819f2d1f85c', {
        cluster: 'eu',
        forceTLS: true
      });

      const channel = this.pusher.subscribe('orders-channel');
      channel.bind('new-order-event', (data) => {
        console.log('Pusher event received:', data);
        if (data.type === 'new_order') {
          this.addNotification({
            id: data.notification_id, // Use server-assigned ID
            text: data.text,
            time: this.formatTime(data.timestamp),
            read: data.read,
            order_id: data.order_id
          });
        }
      });

      this.pusher.connection.bind('connected', () => {
        console.log('Pusher connected');
      });

      this.pusher.connection.bind('error', (err) => {
        console.error('Pusher connection error:', err);
      });
    } catch (error) {
      console.error('Pusher initialization error:', error);
    }
  },

  addNotification: function (notification) {
    this.items.unshift(notification);
    this.count = this.items.filter(item => !item.read).length;
    this.update();
    updateNotificationBadge();
    console.log('Added notification:', notification);
  },

  formatTime: function (timestamp) {
    try {
      const now = new Date();
      const time = new Date(timestamp);
      if (isNaN(time.getTime())) {
        console.warn('Invalid timestamp:', timestamp);
        return 'Just now';
      }
      const diffMs = now - time;
      const diffMins = Math.round(diffMs / 60000);
      if (diffMins < 1) return 'Just now';
      if (diffMins < 60) return `${diffMins} min${diffMins > 1 ? 's' : ''} ago`;
      const diffHours = Math.round(diffMins / 60);
      if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
      return time.toLocaleString('en-NG', { timeZone: 'Africa/Lagos', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    } catch (error) {
      console.error('Error formatting time:', error);
      return 'Just now';
    }
  },

  render: function () {
    console.log('Rendering notifications:', this.items);
    return `
      <div class="notifications-dropdown">
        <div class="notifications-header">
          <h4>Notifications (${this.count})</h4>
          <button class="mark-all-read btn btn-sm btn-outline-secondary">Mark all as read</button>
        </div>
        <div class="notifications-list">
          ${this.items.length > 0 ? this.items.map(item => `
            <div class="notification-item ${item.read ? 'read' : 'unread'}" data-id="${item.id}">
              <p>${item.text}</p>
              <small>${item.time}</small>
            </div>
          `).join('') : '<p class="text-muted">No notifications</p>'}
        </div>
      </div>
    `;
  },

  toggle: function () {
    console.log('Toggling notifications dropdown');
    this.init();
    if (!this.container) {
      console.error('Notifications container not found, reinitializing');
      this.init();
    }
    this.container.classList.toggle("visible");
    console.log('Notifications container visibility:', this.container.classList.contains("visible"));
    this.update();
  },

  hide: function () {
    if (this.container) {
      this.container.classList.remove("visible");
      console.log('Notifications dropdown hidden');
    }
  },

  update: function () {
    if (!this.container) {
      console.error('Notifications container not found');
      return;
    }
    console.log('Updating notifications UI');
    this.container.innerHTML = this.render();

    const items = this.container.querySelectorAll(".notification-item");
    console.log('Found notification items:', items.length);
    items.forEach((item) => {
      item.addEventListener("click", (e) => {
        const id = parseInt(item.dataset.id);
        console.log('Marking notification as read:', id);
        this.markRead(id);
      });
    });

    const markAllButton = this.container.querySelector(".mark-all-read");
    if (markAllButton) {
      markAllButton.addEventListener("click", (e) => {
        e.stopPropagation();
        console.log('Marking all notifications as read');
        this.markAllRead();
      });
    }
  },

  markRead: async function (id) {
    const item = this.items.find((n) => n.id === id);
    if (item && !item.read) {
      item.read = true;
      this.count = this.items.filter(n => !n.read).length;
      await this.saveToServer(id); // Update server
      this.update();
      updateNotificationBadge();
      console.log('Notification marked as read:', id);
    }
  },

  markAllRead: async function () {
    let changed = false;
    this.items.forEach((item) => {
      if (!item.read) {
        item.read = true;
        changed = true;
      }
    });
    if (changed) {
      this.count = 0;
      await this.markAllReadOnServer(); // Update server
      this.update();
      updateNotificationBadge();
      console.log('All notifications marked as read');
    }
  }
};

// Unchanged functions: getNotificationBellHTML, updateNotificationBadge, DOMContentLoaded
function getNotificationBellHTML() {
  return `
    <div class="notification-bell">
      <button onclick="notifications.toggle()" class="notification-btn">
        <svg class="icon"><use href="#notification"></use></svg>
        <span class="badge" style="display: ${notifications.count > 0 ? 'flex' : 'none'};">${notifications.count > 0 ? notifications.count : ''}</span>
      </button>
    </div>
  `;
}

function updateNotificationBadge() {
  const badge = document.querySelector(".notification-btn .badge");
  if (badge) {
    badge.textContent = notifications.count > 0 ? notifications.count : "";
    badge.style.display = notifications.count > 0 ? "flex" : "none";
    if (notifications.count > 0) {
      badge.classList.add("pulse");
      setTimeout(() => badge.classList.remove("pulse"), 1000);
    }
  } else if (notifications.count > 0) {
    const iconsContainer = document.querySelector(".header-actions .icons-container");
    if (iconsContainer) {
      iconsContainer.innerHTML = getNotificationBellHTML() + `
        <button><svg class="icon"><use href="#settings"></use></svg></button>
      `;
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  notifications.init();

  document.addEventListener("click", (e) => {
    const dropdownToggle = e.target.closest(".dropdown-toggle");
    if (dropdownToggle) {
      e.preventDefault();
      const dropdownItem = dropdownToggle.closest(".dropdown-item");
      dropdownItem.classList.toggle("active");
      const dropdownContent = dropdownItem.querySelector(".dropdown-content");
      dropdownContent.classList.toggle("show");
      document.querySelectorAll(".dropdown-item").forEach((item) => {
        if (item !== dropdownItem) {
          item.classList.remove("active");
          item.querySelector(".dropdown-content").classList.remove("show");
        }
      });
    }
  });
});