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
    <title>App Review</title>
    <link rel="stylesheet" href="../assets/styles/general.css" />
    <link rel="stylesheet" href="../assets/styles/panels.css" />
    <link rel="stylesheet" href="../assets/styles/reviews.css" />
  </head>
  <body class="flex">
    <main>
      <div class="content">
        <div class="grid g-af2">
          <div class="ratings-overview card">
            <div class="overall-rating">
              <span class="rating-value">0.0</span>
              <div class="star-rating">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
              </div>
              <span class="review-count">0 Reviews</span>
            </div>
            <div class="category-ratings">
              <!-- Populated dynamically -->
            </div>
          </div>
          <div class="review-statistics card">
            <div class="chart-header">
              <div>
                <h2 class="chart-title">Review Statistics</h2>
                <div class="legend">
                  <span class="legend-item">
                    <span class="legend-color positive"></span> Positive Review
                  </span>
                  <span class="legend-item">
                    <span class="legend-color negative"></span> Negative Review
                  </span>
                </div>
              </div>
              <div class="filter-dropdown">
                <button class="review-dropdown-toggle">
                  This Year
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
                <div class="dropdown-menu">
                  <a href="#">Last Year</a>
                  <a href="#">Last 6 Months</a>
                  <a href="#">All Time</a>
                </div>
              </div>
            </div>
            <div class="chart-container">
              <canvas id="reviewChart"></canvas>
            </div>
          </div>
          <div class="reviews-container card">
            <div class="reviews-header">
              <div class="filter-tabs flex">
                <div class="filter-dropdown">
                  <button class="review-dropdown-toggle">
                    All Rating
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </button>
                  <div class="dropdown-menu">
                    <a href="#">All Rating</a>
                    <a href="#">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#">4</a>
                    <a href="#">5</a>
                  </div>
                </div>
                <div class="filter-dropdown">
                  <button class="review-dropdown-toggle">
                    All Category
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </button>
                  <div class="dropdown-menu">
                    <a href="#">All Category</a>
                    <!-- Populated dynamically -->
                  </div>
                </div>
                <div class="filter-dropdown">
                  <button class="review-dropdown-toggle">
                    All Menu
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </button>
                  <div class="dropdown-menu">
                    <a href="#">All Menu</a>
                    <!-- Populated dynamically -->
                  </div>
                </div>
              </div>
              <div class="filter-dropdown">
                <button class="review-dropdown-toggle">
                  This Year
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
                <div class="dropdown-menu">
                  <a href="#">Last Year</a>
                  <a href="#">Last 6 Months</a>
                  <a href="#">All Time</a>
                </div>
              </div>
            </div>
            <!-- Reviews populated dynamically -->
            <div class="pagination">
              <!-- Populated dynamically -->
            </div>
          </div>
        </div>
      </div>
    </main>
    <script>
// Pass PHP variables to JavaScript
const username = '<?php echo addslashes($first_name); ?>';
const userRole = '<?php echo addslashes($userRole); ?>';
const profilePicture = '<?php echo addslashes($profilePicture); ?>';
</script>
    <script src="../scripts/components.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // Configurable base URL for API requests
        const API_BASE = window.location.hostname.includes('localhost')
          ? 'http://localhost/chinese-restaurant'
          : '/chinese-restaurant';

        // State
        let allReviews = [];
        let filteredReviews = [];
        const reviewsPerPage = 5;
        let currentPage = 1;
        let reviewChart = null;

        // DOM Elements
        const ratingsOverview = document.querySelector(".ratings-overview");
        const reviewsContainer = document.querySelector(".reviews-container");
        const paginationContainer = document.querySelector(".pagination");
        const reviewChartCanvas = document.getElementById("reviewChart");

        // Filters
        let ratingFilter = "All Rating";
        let categoryFilter =

 "All Category";
        let menuFilter = "All Menu";
        let timeFilter = "This Year";

        // Fetch reviews
        async function fetchReviews() {
          try {
            // const response = await fetch(`${API_BASE}/BackEnd/controller/reviews/get_reviews.php`);
            const response = await fetch('../../BackEnd/controller/reviews/get_reviews.php');
            // console.log("Fetch response status:", response.status, response.url);
            if (!response.ok) {
              throw new Error(`HTTP error: ${response.status} for URL ${response.url}`);
            }
            const data = await response.json();
            // console.log("Fetch response data:", data);
            if (data.success) {
              allReviews = data.data.reviews;
              filteredReviews = allReviews;
              updateRatingsOverview(data.data);
              populateDropdowns();
              applyFilters();
              renderChart();
            } else {
              throw new Error(data.message || "Failed to fetch reviews");
            }
          } catch (error) {
            console.error("Error fetching reviews:", error);
            reviewsContainer.innerHTML = '<p class="error-message">Unable to load reviews. Please try again later.</p>';
          }
        }

        // Update ratings overview
        function updateRatingsOverview(data) {
          const overallRating = ratingsOverview.querySelector(".rating-value");
          const reviewCount = ratingsOverview.querySelector(".review-count");
          const starRating = ratingsOverview.querySelector(".star-rating");

          overallRating.textContent = data.average_rating || "0.0";
          reviewCount.textContent = `${data.total_reviews || 0} Reviews`;

          // Update stars
          const stars = starRating.querySelectorAll(".star");
          const rating = Math.round(data.average_rating * 2) / 2;
          stars.forEach((star, index) => {
            star.className = "star";
            if (index < Math.floor(rating)) {
              star.classList.add("filled");
            } else if (index === Math.floor(rating) && rating % 1 >= 0.5) {
              star.classList.add("half-filled");
            }
          });

          // Placeholder category ratings
          const categoryRatings = [
            { name: "Food Quality", value: 4.8, progress: 96 },
            { name: "Service", value: 4.6, progress: 92 },
            { name: "Ambiance", value: 4.5, progress: 90 },
            { name: "Value for Money", value: 4.7, progress: 94 },
            { name: "Cleanliness", value: 4.9, progress: 98 }
          ];
          const categoryContainer = ratingsOverview.querySelector(".category-ratings");
          categoryContainer.innerHTML = categoryRatings.map(item => `
            <div class="category-item">
              <span class="category-name">${item.name}</span>
              <div class="progress-bar-container">
                <div class="progress-bar" style="width: ${item.progress}%"></div>
              </div>
              <span class="category-value">${item.value}</span>
            </div>
          `).join("");
        }

        // Apply filters
        function applyFilters() {
          filteredReviews = allReviews.filter(review => {
            const reviewDate = new Date(review.review_date);
            const currentYear = new Date().getFullYear();

            // Rating filter
            const ratingMatch = ratingFilter === "All Rating" || parseInt(review.rating) === parseInt(ratingFilter);

            // Category filter
            const categoryMatch = categoryFilter === "All Category" || review.category_name.toLowerCase() === categoryFilter.toLowerCase();

            // Menu filter
            const menuMatch = menuFilter === "All Menu" || review.dish_name.toLowerCase() === menuFilter.toLowerCase();

            // Time filter
            let timeMatch = true;
            if (timeFilter === "This Year") {
              timeMatch = reviewDate.getFullYear() === currentYear;
            } else if (timeFilter === "Last Year") {
              timeMatch = reviewDate.getFullYear() === currentYear - 1;
            } else if (timeFilter === "Last 6 Months") {
              const sixMonthsAgo = new Date();
              sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 6);
              timeMatch = reviewDate >= sixMonthsAgo;
            }

            return ratingMatch && categoryMatch && menuMatch && timeMatch;
          });

          currentPage = 1;
          renderReviews();
          renderPagination();
          renderChart();
        }

        // Render reviews
        function renderReviews() {
          const start = (currentPage - 1) * reviewsPerPage;
          const end = start + reviewsPerPage;
          const paginatedReviews = filteredReviews.slice(start, end);

          reviewsContainer.querySelectorAll(".review-item").forEach(item => item.remove());

          if (paginatedReviews.length === 0) {
            reviewsContainer.innerHTML = '<p class="no-reviews">No reviews match the selected filters.</p>';
            return;
          }

          paginatedReviews.forEach(review => {
            const reviewItem = document.createElement("div");
            reviewItem.className = "review-item";
            reviewItem.innerHTML = `
              <div class="review-image">
                <img src="https://picsum.photos/200?random=${review.id}" alt="${review.dish_name}">
              </div>
              <div class="review-details flex">
                <div class="detail flex column justify-between">
                  <div>
                    <div class="flex justify-between align-center">
                      <h3 class="review-title">${review.dish_name}</h3>
                      <div class="review-rating">
                        <span class="stars">${"★".repeat(review.rating)}${"☆".repeat(5 - review.rating)}</span>
                        <span class="rating-text">${review.rating}/5</span>
                      </div>
                    </div>
                    <p class="review-subtitle">${review.category_name}</p>
                  </div>
                  <div class="review-stats">
                    <span class="review-stat">
                      <svg class="icon"><use href="#review"></use></svg>
                      <span style="color: black">N/A</span> Reviews
                    </span>
                    <span class="review-stat">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                      </svg>
                      <span style="color: black">${review.rating}</span> Overall Rate
                    </span>
                  </div>
                </div>
                <div class="detail flex column justify-between">
                  <span class="review-date">${new Date(review.review_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                  <p class="review-text">${review.review_text}</p>
                  <p class="reviewer-name">${review.reviewer_name}</p>
                </div>
              </div>
            `;
            reviewsContainer.insertBefore(reviewItem, paginationContainer);
          });
        }

        // Render pagination
        function renderPagination() {
          const totalPages = Math.ceil(filteredReviews.length / reviewsPerPage);
          paginationContainer.innerHTML = `
            <button class="paginate_button" ${currentPage === 1 ? 'disabled' : ''}>
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9254 4.55806C13.1915 4.80214 13.1915 5.19786 12.9254 5.44194L8.4375 9.55806C8.17138 9.80218 8.17138 10.1979 8.4375 10.4419L12.9254 14.5581C13.1915 14.8021 13.1915 15.1979 12.9254 15.4419C12.6593 15.6860 12.2278 15.6860 11.9617 15.4419L7.47378 11.3258C6.67541 10.5936 6.67541 9.40641 7.47378 8.67418L11.9617 4.55806C12.2278 4.31398 12.6593 4.31398 12.9254 4.55806Z" fill="#1C1C1C"/>
              </svg>
            </button>
          `;
          for (let i = 1; i <= totalPages; i++) {
            paginationContainer.innerHTML += `<a href="#" class="${i === currentPage ? 'active' : ''}">${i}</a>`;
          }
          paginationContainer.innerHTML += `
            <button class="paginate_button" ${currentPage === totalPages ? 'disabled' : ''}>
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.07459 15.4419C6.80847 15.1979 6.80847 14.8021 7.07459 14.5581L11.5625 10.4419C11.8286 10.1979 11.8286 9.80214 11.5625 9.55806L7.07459 5.44194C6.80847 5.19786 6.80847 4.80214 7.07459 4.55806C7.34072 4.31398 7.77219 4.31398 8.03831 4.55806L12.5262 8.67418C13.3246 9.40641 13.3246 10.5936 12.5262 11.3258L8.03831 15.4419C7.77219 15.686 7.34072 15.686 7.07459 15.4419Z" fill="#1C1C1C"/>
              </svg>
            </button>
          `;

          paginationContainer.querySelectorAll("a").forEach((link, index) => {
            link.addEventListener("click", (e) => {
              e.preventDefault();
              currentPage = index + 1;
              renderReviews();
              renderPagination();
            });
          });
          paginationContainer.querySelectorAll(".paginate_button").forEach((button, index) => {
            button.addEventListener("click", () => {
              if (index === 0 && currentPage > 1) {
                currentPage--;
              } else if (index === 1 && currentPage < totalPages) {
                currentPage++;
              }
              renderReviews();
              renderPagination();
            });
          });
        }

        // Render chart
        function renderChart() {
          if (reviewChart) reviewChart.destroy();

          if (filteredReviews.length === 0) {
            reviewChartCanvas.parentElement.innerHTML = '<p class="no-reviews">No reviews available for the selected filters.</p>';
            return;
          }

          const currentYear = new Date().getFullYear();
          let labels = [];
          let positiveReviews = [];
          let negativeReviews = [];
          let startDate;
          let maxY = 200; // Default max Y-axis value
          let highlightMonth = null;
          let highlightData = null;

          // Prepare data based on time filter
          if (timeFilter === "This Year" || timeFilter === "Last Year") {
            const year = timeFilter === "This Year" ? currentYear : currentYear - 1;
            labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            positiveReviews = Array(12).fill(0);
            negativeReviews = Array(12).fill(0);
            startDate = new Date(year, 0, 1);
            highlightMonth = "Sep"; // Preserve static annotation
          } else if (timeFilter === "Last 6 Months") {
            labels = [];
            for (let i = 5; i >= 0; i--) {
              const date = new Date();
              date.setMonth(date.getMonth() - i);
              labels.push(date.toLocaleString('en-US', { month: 'short', year: 'numeric' }));
            }
            positiveReviews = Array(6).fill(0);
            negativeReviews = Array(6).fill(0);
            startDate = new Date();
            startDate.setMonth(startDate.getMonth() - 6);
          } else if (timeFilter === "All Time") {
            const years = [...new Set(filteredReviews.map(r => new Date(r.review_date).getFullYear()))].sort();
            labels = years.map(y => y.toString());
            positiveReviews = Array(years.length).fill(0);
            negativeReviews = Array(years.length).fill(0);
            startDate = new Date(Math.min(...years) || currentYear, 0, 1);
          }

          // Aggregate reviews
          filteredReviews.forEach(review => {
            const date = new Date(review.review_date);
            if (date >= startDate) {
              let index;
              if (timeFilter === "This Year" || timeFilter === "Last Year") {
                index = date.getMonth();
              } else if (timeFilter === "Last 6 Months") {
                const diff = (date.getFullYear() - startDate.getFullYear()) * 12 + date.getMonth() - startDate.getMonth();
                index = diff >= 0 ? diff : -1;
              } else if (timeFilter === "All Time") {
                index = labels.indexOf(date.getFullYear().toString());
              }
              if (index >= 0) {
                if (review.rating >= 3) {
                  positiveReviews[index]++;
                } else {
                  negativeReviews[index]++;
                }
                maxY = Math.max(maxY, positiveReviews[index] + negativeReviews[index] + 50);
              }
            }
          });

          // Set highlight data for September annotation
          if (highlightMonth === "Sep" && (timeFilter === "This Year" || timeFilter === "Last Year")) {
            const sepIndex = labels.indexOf("Sep");
            if (sepIndex >= 0) {
              highlightData = {
                positive: positiveReviews[sepIndex],
                negative: negativeReviews[sepIndex]
              };
            }
          }

          const data = {
            labels,
            datasets: [
              {
                label: "Positive Review",
                data: positiveReviews,
                backgroundColor: "#ff8a65",
                barThickness: 20,
                categoryPercentage: 0.4,
                barPercentage: 0.8
              },
              {
                label: "Negative Review",
                data: negativeReviews,
                backgroundColor: "#212121",
                barThickness: 20,
                categoryPercentage: 0.4,
                barPercentage: 0.8
              }
            ]
          };

          reviewChart = new Chart(reviewChartCanvas, {
            type: "bar",
            data: data,
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                y: {
                  beginAtZero: true,
                  max: maxY,
                  ticks: {
                    stepSize: 50
                  },
                  grid: {
                    borderColor: "#ccc",
                    borderDash: [2, 2],
                    drawBorder: false
                  }
                },
                x: {
                  grid: {
                    display: false
                  }
                }
              },
              plugins: {
                legend: {
                  display: false
                },
                tooltip: {
                  mode: "index",
                  intersect: false,
                  callbacks: {
                    title: (tooltipItems) => tooltipItems[0].label,
                    label: (tooltipItem) => `${tooltipItem.dataset.label}: ${tooltipItem.formattedValue}`
                  },
                  backgroundColor: "#ffffff",
                  titleColor: "#000",
                  bodyColor: "#000"
                },
                annotation: {
                  annotations: highlightMonth && highlightData ? [
                    {
                      type: "box",
                      xScaleID: "x",
                      yScaleID: "y",
                      xMin: highlightMonth,
                      xMax: highlightMonth,
                      yMin: 0,
                      yMax: maxY,
                      backgroundColor: "rgba(255, 138, 101, 0.2)",
                      borderColor: "transparent"
                    },
                    {
                      type: "label",
                      xValue: highlightMonth,
                      yValue: highlightData.positive,
                      xScaleID: "x",
                      yScaleID: "y",
                      content: `September ${timeFilter === "This Year" ? currentYear : currentYear - 1}\nPositive: ${highlightData.positive}\nNegative: ${highlightData.negative}`,
                      backgroundColor: "rgba(0, 0, 0, 0.8)",
                      color: "white",
                      textAlign: "left",
                      borderRadius: 5,
                      padding: 8,
                      font: { size: 10 },
                      position: "top",
                      yAdjust: -25
                    }
                  ] : []
                }
              }
            }
          });
        }

        // Handle filter dropdowns
        function attachDropdownListeners(menu, toggle, filterType) {
          menu.querySelectorAll("a").forEach(option => {
            option.removeEventListener("click", handleDropdownClick); // Prevent duplicates
            option.addEventListener("click", handleDropdownClick);
          });

          function handleDropdownClick(e) {
            e.preventDefault();
            const value = e.target.textContent.trim();
            toggle.textContent = value + ' ▼';

            if (filterType === "rating") {
              ratingFilter = value === "All Rating" ? "All Rating" : parseInt(value);
            } else if (filterType === "category") {
              categoryFilter = value;
            } else if (filterType === "menu") {
              menuFilter = value;
            } else if (filterType === "time") {
              timeFilter = value;
            }

            applyFilters();
            menu.style.display = "none";
          }
        }

        const filterDropdowns = document.querySelectorAll(".filter-dropdown");
        filterDropdowns.forEach(dropdown => {
          const toggle = dropdown.querySelector(".review-dropdown-toggle");
          const menu = dropdown.querySelector(".dropdown-menu");
          if (toggle && menu) {
            toggle.addEventListener("click", () => {
              menu.style.display = menu.style.display === "block" ? "none" : "block";
            });
            window.addEventListener("click", (event) => {
              if (!event.target.matches(".review-dropdown-toggle") && !event.target.closest(".dropdown-menu")) {
                menu.style.display = "none";
              }
            });

            // Determine filter type based on dropdown content
            let filterType = "time";
            if (dropdown.parentElement.classList.contains("filter-tabs")) {
              if (toggle.textContent.includes("Rating")) {
                filterType = "rating";
              } else if (toggle.textContent.includes("Category")) {
                filterType = "category";
              } else if (toggle.textContent.includes("Menu")) {
                filterType = "menu";
              }
            }
            attachDropdownListeners(menu, toggle, filterType);
          }
        });

        // Populate Category and Menu dropdowns
        function populateDropdowns() {
          const categoryDropdown = document.querySelector(".filter-dropdown:nth-child(2) .dropdown-menu");
          const menuDropdown = document.querySelector(".filter-dropdown:nth-child(3) .dropdown-menu");

          if (categoryDropdown && menuDropdown) {
            const categories = [...new Set(allReviews.map(r => r.category_name))].sort();
            const dishes = [...new Set(allReviews.map(r => r.dish_name))].sort();

            categoryDropdown.innerHTML = `<a href="#">All Category</a>` + categories.map(cat => `<a href="#">${cat}</a>`).join("");
            menuDropdown.innerHTML = `<a href="#">All Menu</a>` + dishes.map(dish => `<a href="#">${dish}</a>`).join("");

            // Reattach event listeners
            attachDropdownListeners(categoryDropdown, categoryDropdown.parentElement.querySelector(".review-dropdown-toggle"), "category");
            attachDropdownListeners(menuDropdown, menuDropdown.parentElement.querySelector(".review-dropdown-toggle"), "menu");
          }
        }

        // Initialize
        fetchReviews();
      });
    </script>
  </body>
</html>