const notifications = {
  count: 0,
  items: [],
  container: null,
  pusher: null, // Only Pusher remains

  init: function () {
    if (this.container) {
      console.log('Notifications container already initialized');
      return;
    }

    console.log('Initializing notifications...');
    this.loadFromStorage();

    // The container should ideally be appended to a specific element,
    // like the header's icons-container, to ensure proper positioning.
    // For now, keeping it on body as per your original code, but
    // be mindful of its CSS positioning.
    this.container = document.createElement("div");
    this.container.className = "notifications-container";
    document.body.appendChild(this.container);
    console.log('Notifications container appended to body:', this.container);

    this.update();
    this.connectPusher(); // Only connect to Pusher

    document.addEventListener("click", (e) => {
      const isNotificationButton = e.target.closest(".notification-btn");
      const isInsideContainer = e.target.closest(".notifications-container");

      if (!isInsideContainer && !isNotificationButton) {
        console.log('Hiding notifications dropdown');
        this.hide();
      }
    });
  },

  loadFromStorage: function () {
    try {
      const stored = localStorage.getItem('notifications');
      if (stored) {
        this.items = JSON.parse(stored);
        this.count = this.items.filter(item => !item.read).length;
        console.log('Loaded notifications from storage:', this.items);
      }
    } catch (error) {
      console.error('Error loading notifications from storage:', error);
    }
  },

  saveToStorage: function () {
    try {
      localStorage.setItem('notifications', JSON.stringify(this.items));
      console.log('Saved notifications to storage:', this.items);
    } catch (error) {
      console.error('Error saving notifications to storage:', error);
    }
  },

  // This method now solely handles Pusher connection
  connectPusher: function () {
    if (this.pusher) return; // Prevent re-initializing if already connected

    console.log('Initializing Pusher connection...');
    try {
      // Ensure Pusher is loaded in your HTML before this script runs
      this.pusher = new Pusher('c0ccafac1819f2d1f85c', {
        cluster: 'eu',
        forceTLS: true
      });

      const channel = this.pusher.subscribe('orders-channel');
      channel.bind('new-order-event', (data) => {
        console.log('Pusher event received:', data);
        if (data.type === 'new_order') {
          this.addNotification({
            id: Date.now(), // Use Date.now() for a unique ID
            text: `New ${data.order_type} order #${data.order_id} for £${data.total_amount}`,
            time: this.formatTime(data.timestamp),
            read: false
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
      // If Pusher library isn't loaded, this catch block will activate.
      // Make sure <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
      // is in your HTML.
    }
  },

  addNotification: function (notification) {
    this.items.unshift(notification); // Add to the beginning of the array
    this.count = this.items.filter(item => !item.read).length; // Recalculate unread count
    this.saveToStorage(); // Save to local storage
    this.update(); // Update the UI of the dropdown
    updateNotificationBadge(); // Update the bell badge
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
      // Use toLocaleString with locale and timezone for more accurate time display
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
    // Calling init here ensures the container exists, though it's primarily
    // initialized on DOMContentLoaded. Redundant but harmless.
    this.init();
    if (!this.container) {
      console.error('Notifications container not found, reinitializing');
      this.init();
    }
    this.container.classList.toggle("visible");
    console.log('Notifications container visibility:', this.container.classList.contains("visible"));
    this.update(); // Always update content when toggling visibility
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
    this.container.innerHTML = this.render(); // Re-render the entire content

    // Attach event listeners to newly rendered items
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
        e.stopPropagation(); // Prevent dropdown from closing if clicked
        console.log('Marking all notifications as read');
        this.markAllRead();
      });
    }
  },

  markRead: function (id) {
    const item = this.items.find((n) => n.id === id);
    if (item && !item.read) {
      item.read = true;
      this.count = this.items.filter(n => !n.read).length;
      this.saveToStorage();
      this.update(); // Update dropdown UI
      updateNotificationBadge(); // Update bell badge
      console.log('Notification marked as read:', id);
    }
  },

  markAllRead: function () {
    let changed = false;
    this.items.forEach((item) => {
      if (!item.read) {
        item.read = true;
        changed = true;
      }
    });
    if (changed) {
      this.count = 0; // All are read
      this.saveToStorage();
      this.update(); // Update dropdown UI
      updateNotificationBadge(); // Update bell badge
      console.log('All notifications marked as read');
    }
  }
};

// --- MODIFIED CODE START ---

// Dynamically generate the notification bell HTML
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

// Badge Update Function
function updateNotificationBadge() {
  const badge = document.querySelector(".notification-btn .badge");
  if (badge) {
    badge.textContent = notifications.count > 0 ? notifications.count : "";
    badge.style.display = notifications.count > 0 ? "flex" : "none"; // Control visibility

    if (notifications.count > 0) {
      badge.classList.add("pulse");
      setTimeout(() => badge.classList.remove("pulse"), 1000);
    }
  } else if (notifications.count > 0) {
    // If badge doesn't exist but count is > 0, re-render the bell
    // This handles cases where the initial header render might not have included a badge
    const iconsContainer = document.querySelector(".header-actions .icons-container");
    if (iconsContainer) {
      // It's safer to re-render the whole icons-container content if you want to ensure the badge is there
      // This assumes the order of buttons in icons-container
      iconsContainer.innerHTML = getNotificationBellHTML() + `
        <button><svg class="icon"><use href="#settings"></use></svg></button>
      `;
      // Re-attach the event listener for the settings button if it gets re-rendered
      // (This is a broader consideration for dynamic HTML, not directly related to notifications)
    }
  }
}

// --- MODIFIED CODE END ---

// Unmodified DOMContentLoaded Event Listener
document.addEventListener("DOMContentLoaded", () => {
  notifications.init();

  document.addEventListener("click", (e) => {
    // The notification button click is handled by inline onclick on the button itself now,
    // which calls notifications.toggle(). The previous logic here was for a generic listener.
    // If you prefer, you can remove the inline onclick and uncomment a listener here,
    // making sure it doesn't conflict.
    // if (e.target.closest(".notification-btn")) {
    //   notifications.toggle();
    // }

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

// Unmodified Page Mapping and Navigation Logic
const pageMap = {
  "/dashboard/": {
    page: "Dashboard",
    icon: "overview",
  },
  "/orders/": {
    page: "Orders",
    icon: "receipt",
  },
  "/messages/": {
    page: "Messages",
    icon: "text",
  },
  "/calendar/": {
    page: "Calendar",
    icon: "calendar",
  },
  "/menu/": {
    page: "Menu",
    icon: "menu",
  },
  "/menu2/": {
    page: "Menu02",
    icon: "menu",
  },
  "/inventory/": {
    page: "Inventory",
    icon: "inventory",
    subItems: {
      "/inventory/items/": {
        page: "Inventory Items",
        icon: "inventory",
      },
      "/inventory/purchase-orders/": {
        page: "Purchase Orders",
        icon: "purchase-order",
      },
    },
  },
  "/reviews/": {
    page: "Reviews",
    icon: "review",
  },
};

function getCurrentPath() {
  const pathname = window.location.pathname;
  const basePaths = ["/admin-portal", "/admin"];

  for (const basePath of basePaths) {
    if (pathname.startsWith(basePath)) {
      return pathname.replace(basePath, "");
    }
  }

  return pathname;
}

const currentPath = getCurrentPath();
const currentPageInfo = Object.entries(pageMap).reduce(
  (acc, [rootPath, rootInfo]) => {
    if (currentPath.endsWith(rootPath)) {
      return { ...rootInfo, isSubItem: false };
    }

    if (rootInfo.subItems) {
      const subItemMatch = Object.entries(rootInfo.subItems).find(
        ([subPath, subInfo]) => currentPath.endsWith(subPath)
      );

      if (subItemMatch) {
        const [subPath, subInfo] = subItemMatch;
        return { ...subInfo, isSubItem: true, parentPage: rootInfo.page };
      }
    }

    return acc;
  },
  { page: "", icon: "", isSubItem: false }
);

function getRelativePath(targetPath) {
  const currentDepth = currentPath.split("/").filter(Boolean).length - 1;
  let relativePath = "";

  if (currentDepth > 0) {
    relativePath = "../".repeat(currentDepth);
  }

  return relativePath + targetPath;
}

// MODIFIED Header Template to use the new getNotificationBellHTML()
const header = `
  <header class="flex justify-between align-center">
    <div class="title">
      <h3>${currentPageInfo.page}</h3>
      ${
        currentPageInfo.page === "Dashboard"
          ? `<p>Hello ${username}, welcome back!</p>`
          : ""
      }
      ${
        currentPageInfo.page != "Dashboard"
          ? `<div class="breadcrumb">
              <a>Dashboard</a> /
              <a href="/admin${currentPath}">${
                currentPageInfo.page != "Orders"
                  ? currentPageInfo.page
                  : "Customer Orders"
              }</a>
            </div>`
          : ""
      }
    </div>
    <div class="header-actions">
      <div class="search">
        <button>
          <svg class="icon"><use href="#search"></use></svg>
        </button>
        <input type="text" placeholder="Search anything">
      </div>
      <div class="icons-container">
        ${getNotificationBellHTML()} <button>
          <svg class="icon"><use href="#settings"></use></svg>
        </button>
      </div>
      <div class="user-profile flex align-center">
        <div class="user-profile-details">
          <p class="username">${username}</p>
          <small class="role">${userRole}</small>
        </div>
        <div class="user-profile-picture flex align-center justify-center">
          <img src="${profilePicture}" alt="Profile Picture">
        </div>
      </div>
    </div>
  </header>
`;

const sidebar = `
  <aside>
    <div class="user flex align-center justify-center">
      <div class="logo">
        <img src="${getRelativePath("./assets/images/logo.webp")}" alt="Golden Dish">
      </div>
    </div>
    <nav>
      <div class="sidebar-section">
        ${Object.entries(pageMap)
          .map(([path, pageInfo]) => {
            const isActive = currentPath.startsWith(`admin${path}`);
            const hasSubItems = pageInfo.subItems;
            const isSubItemActive =
              hasSubItems &&
              Object.keys(pageInfo.subItems).some((subPath) =>
                currentPath.startsWith(subPath)
              );

            if (!hasSubItems) {
              return `
                <a href="${getRelativePath(`admin${path}`)}"
                   class="${isActive ? "active" : ""}">
                  <svg class="icon"><use href="#${pageInfo.icon}"></use></svg>
                  ${pageInfo.page}
                </a>
              `;
            } else {
              return `
                <div class="dropdown-item ${isActive || isSubItemActive ? "active" : ""}">
                  <a href="#" class="dropdown-toggle" data-path="${path}">
                    <svg class="icon"><use href="#${pageInfo.icon}"></use></svg>
                    ${pageInfo.page}
                    <svg class="dropdown-icon"><use href="#caret-down"></use></svg>
                  </a>
                  <div class="dropdown-content ${isActive || isSubItemActive ? "show" : ""}">
                    ${Object.entries(pageInfo.subItems)
                      .map(([subPath, subPageInfo]) => `
                        <a href="${getRelativePath(`admin${subPath}`)}"
                           class="${currentPath.startsWith(subPath) ? "active" : ""}">
                          ${subPageInfo.page}
                        </a>
                      `).join("")}
                  </div>
                </div>
              `;
            }
          }).join("")}
        <a href="${getRelativePath("/BackEnd/controller/auth/logout.php")}"
           class="${currentPath === "/" ? "active" : ""}">
          <svg class="icon"><use href="#logout"></use></svg>
          Logout
        </a>
      </div>
    </nav>
  </aside>
`;

// Unmodified Render Components Function
function renderComponents() {
  fetch(getRelativePath(`./admin/assets/img/icons-sprite.svg`))
    .then((response) => response.text())
    .then((svg) => document.body.insertAdjacentHTML("afterbegin", svg))
    .catch(error => console.error('Error loading SVG:', error));

  document.body.insertAdjacentHTML("afterbegin", sidebar);
  document.querySelector("main").insertAdjacentHTML("afterbegin", header);

  const activeSection = document.querySelector(
    `.sidebar-section[data-section="${currentPageInfo.section}"]`
  );
  if (activeSection) {
    activeSection.classList.add("active-section");
  }

  if (currentPageInfo.page) {
    if (currentPageInfo.isSubItem && currentPageInfo.parentPage) {
      document.title = `${currentPageInfo.page} | ${currentPageInfo.parentPage} | Golden Dish`;
    } else {
      document.title = `${currentPageInfo.page} | Golden Dish`;
    }
  }

  // Ensure the badge is updated after the header is rendered
  updateNotificationBadge();
}

// Unmodified Render Components Call
document.addEventListener("DOMContentLoaded", renderComponents);

// Removed the original (unmodified) WebSocket-based notification object