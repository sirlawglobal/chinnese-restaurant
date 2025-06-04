document.addEventListener("DOMContentLoaded", function () {
  // Get all breadcrumb elements
  const breadcrumbs = {
    general: document.getElementById("general-tab"),
    notifications: document.getElementById("notifications-tab"),
    advertisement: document.getElementById("advertisement-tab"),
    security: document.getElementById("security-tab"),
  };

  // Get all view containers
  const views = {
    general: document.getElementById("general-view"),
    notifications: document.getElementById("notifications-view"),
    advertisement: document.getElementById("advertisement-view"),
    security: document.getElementById("security-view"),
  };

  // Initialize by showing general view
  let currentView = "general";
  activateView(currentView);

  // Add click event listeners to each breadcrumb
  Object.keys(breadcrumbs).forEach((view) => {
    breadcrumbs[view].addEventListener("click", function (e) {
      e.preventDefault();
      activateView(view);
    });
  });

  // Function to activate a specific view
  function activateView(view) {
    // Deactivate all views and breadcrumbs
    Object.keys(views).forEach((v) => {
      views[v].style.display = "none";
      breadcrumbs[v].classList.remove("active");
    });

    // Activate the selected view and breadcrumb
    views[view].style.display = "block";
    breadcrumbs[view].classList.add("active");
    currentView = view;

    // Optional: Save the current view to localStorage
    localStorage.setItem("lastSettingsView", view);
  }

  // Optional: Restore last viewed tab if needed
  const lastView = localStorage.getItem("lastSettingsView");
  if (lastView && views[lastView]) {
    activateView(lastView);
  }
});
