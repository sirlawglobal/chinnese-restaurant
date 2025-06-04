// Page to section mapping with relative paths
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
  // Add more pages as needed
};

function getCurrentPath() {
  const pathname = window.location.pathname;
  const basePaths = ["/admin-portal", "/admin"]; // Add potential base paths here

  for (const basePath of basePaths) {
    if (pathname.startsWith(basePath)) {
      return pathname.replace(basePath, "");
    }
  }

  return pathname;
}
// Get current path (relative to admin-portal)
const currentPath = getCurrentPath();
console.log(currentPath);
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
console.log(currentPageInfo);

// Function to generate proper relative paths
function getRelativePath(targetPath) {
  const currentDepth = currentPath.split("/").filter(Boolean).length - 1;
  let relativePath = "";

  if (currentDepth > 0) {
    relativePath = "../".repeat(currentDepth);
  }

  return relativePath + targetPath;
}
// Notification System
const notifications = {
  count: 3,
  items: [
    { id: 1, text: "New user registered", time: "2 mins ago", read: false },
    { id: 2, text: "System update available", time: "1 hour ago", read: false },
    { id: 3, text: "Payment processed", time: "3 hours ago", read: true },
  ],
  container: null,

  init: function () {
    // Only initialize once
    if (this.container) return;

    // Create container
    this.container = document.createElement("div");
    this.container.className = "notifications-container";
    document.body.appendChild(this.container);

    // Render initial content
    this.update();

    // Close when clicking outside
    document.addEventListener("click", (e) => {
      const isNotificationButton = e.target.closest(".notification-btn");
      const isInsideContainer = e.target.closest(".notifications-container");

      if (!isInsideContainer && !isNotificationButton) {
        this.hide();
      }
    });
  },

  render: function () {
    return `
        <div class="notifications-dropdown">
          <div class="notifications-header">
            <h4>Notifications (${this.count})</h4>
            <button class="mark-all-read">Mark all read</button>
          </div>
          <div class="notifications-list">
            ${this.items
              .map(
                (item) => `
              <div class="notification-item ${
                item.read ? "read" : "unread"
              }" data-id="${item.id}">
                <p>${item.text}</p>
                <small>${item.time}</small>
              </div>
            `
              )
              .join("")}
          </div>
        </div>
      `;
  },

  toggle: function () {
    this.init(); // Ensure container exists
    this.container.classList.toggle("visible");

    // Update badge count in header
    // this.updateBadge();
  },

  hide: function () {
    if (this.container) {
      this.container.classList.remove("visible");
    }
  },

  update: function () {
    if (!this.container) return;
    this.container.innerHTML = this.render();

    // Add event listeners after render
    this.container.querySelectorAll(".notification-item").forEach((item) => {
      item.addEventListener("click", (e) => {
        const id = parseInt(item.dataset.id);
        this.markRead(id);
      });
    });

    this.container
      .querySelector(".mark-all-read")
      .addEventListener("click", (e) => {
        e.stopPropagation();
        this.markAllRead();
      });
  },

  // updateBadge: function () {
  //   const badge = document.querySelector(".notification-btn .badge");
  //   if (badge) {
  //     badge.textContent = this.count > 0 ? this.count : "";
  //     badge.style.display = this.count > 0 ? "flex" : "none";
  //   }
  // },

  markRead: function (id) {
    const item = this.items.find((n) => n.id === id);
    if (item && !item.read) {
      item.read = true;
      this.count--;
      this.update();
      this.updateBadge();
    }
  },

  markAllRead: function () {
    let changed = false;
    this.items.forEach((item) => {
      if (!item.read) {
        item.read = true;
        this.count--;
        changed = true;
      }
    });
    if (changed) {
      this.update();
      this.updateBadge();
    }
  },
};

// Initialize notification system when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  notifications.init();

  // Event delegation for notification button
  document.addEventListener("click", (e) => {
    if (e.target.closest(".notification-btn")) {
      notifications.toggle();
    }

    // Handle dropdown toggles
    const dropdownToggle = e.target.closest(".dropdown-toggle");
    if (dropdownToggle) {
      e.preventDefault();
      const dropdownItem = dropdownToggle.closest(".dropdown-item");
      dropdownItem.classList.toggle("active");

      const dropdownContent = dropdownItem.querySelector(".dropdown-content");
      dropdownContent.classList.toggle("show");

      // Close other dropdowns
      document.querySelectorAll(".dropdown-item").forEach((item) => {
        if (item !== dropdownItem) {
          item.classList.remove("active");
          item.querySelector(".dropdown-content").classList.remove("show");
        }
      });
    }
  });
});

// {
//   <div class="breadcrumb">
//     $
//     {currentPageInfo.section === null
//       ? ""
//       : `<a>${currentPageInfo.section}</a> / `}
//     <a href="${currentPath}">${currentPageInfo.page}</a>
//   </div>;
// }

// Components
const header = `
      <header class="flex justify-between align-center">
          <div class="title">
            <h3>${currentPageInfo.page}</h3>
            ${
              currentPageInfo.page === "Dashboard"
                ? "<p>Hello Joshua, welcome back!</p>"
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
                  <button onclick="toggleNotification()" class="notification-btn">
                    <svg class="icon"><use href="#notification"></use></svg>
                    ${
                      notifications.count > 0
                        ? `<span class="badge"></span>`
                        : ""
                    }
                  </button>
                  <button>
                    <svg class="icon"><use href="#settings"></use></svg>
                  </button>
              </div>
              <div class="user-profile flex align-center">
                <div class="user-profile-details">
                  <p class="username">Joshua Beck</p>
                  <small class="role">Admin</small>
                </div>
                <div class="user-profile-picture flex align-center justify-center">
                  <img src="https://picsum.photos/40" alt="Profile Picture">
                </div>
              </div>
          </div>
      </header>
  `;

const sidebar = `
      <aside>
          <div class="user flex align-center justify-center">
              <div class="logo">
                <img src="${getRelativePath(
                  "./assets/images/logo.webp"
                )}" alt="Golden Dish">
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
                              <svg class="icon"><use href="#${
                                pageInfo.icon
                              }"></use></svg>
                              ${pageInfo.page}
                          </a>
                        `;
                      } else {
                        // This is a dropdown item
                        return `
                          <div class="dropdown-item ${
                            isActive || isSubItemActive ? "active" : ""
                          }">
                            <a href="#" class="dropdown-toggle" data-path="${path}">
                              <svg class="icon"><use href="#${
                                pageInfo.icon
                              }"></use></svg>
                              ${pageInfo.page}
                              <svg class="dropdown-icon"><use href="#caret-down"></use></svg>
                            </a>
                            <div class="dropdown-content ${
                              isActive || isSubItemActive ? "show" : ""
                            }">
                              ${Object.entries(pageInfo.subItems)
                                .map(
                                  ([subPath, subPageInfo]) => `
                                  <a href="${getRelativePath(`admin${subPath}`)}" 
                                     class="${
                                       currentPath.startsWith(subPath)
                                         ? "active"
                                         : ""
                                     }">
                                     ${subPageInfo.page}
                                  </a>
                                `
                                )
                                .join("")}
                            </div>
                          </div>
                        `;
                      }
                    })
                    .join("")}
                  <a href="${getRelativePath("../")}" 
                     class="${currentPath === "/" ? "active" : ""}">
                      <svg class="icon"><use href="#logout"></use></svg>
                      Logout
                  </a>
              </div>
          </nav>
      </aside>
  `;
const notification = ``;

function renderComponents() {
  fetch(getRelativePath(`./admin/assets/img/icons-sprite.svg`))
    .then((response) => response.text())
    .then((svg) => document.body.insertAdjacentHTML("afterbegin", svg));
  document.body.insertAdjacentHTML("afterbegin", sidebar);

  document.querySelector("main").insertAdjacentHTML("afterbegin", header);

  // Highlight active section in sidebar
  const activeSection = document.querySelector(
    `.sidebar-section[data-section="${currentPageInfo.section}"]`
  );
  if (activeSection) {
    activeSection.classList.add("active-section");
  }

  // Set document title
  // document.title = `${currentPageInfo.page} | Golden Dish`;
  if (currentPageInfo.page) {
    if (currentPageInfo.isSubItem && currentPageInfo.parentPage) {
      document.title = `${currentPageInfo.page} | ${currentPageInfo.parentPage} | Golden Dish`;
    } else {
      document.title = `${currentPageInfo.page} | Golden Dish`;
      console.log("====================================");
      console.log(currentPageInfo);
      console.log("====================================");
    }
  }
}

// Call this when the DOM is loaded
document.addEventListener("DOMContentLoaded", renderComponents);
