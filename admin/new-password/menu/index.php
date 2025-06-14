
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
    <title>Billing Alerts</title>
    <link rel="stylesheet" href="../assets/styles/general.css" />
    <link rel="stylesheet" href="../assets/styles/panels.css" />
    <link rel="stylesheet" href="../assets/styles/menu.css" />
  </head>
  <body class="flex">
    <main>
      <div class="content flex">
        <div class="inner-content card">
          <div class="featured">
            <h6>Featured Menu</h6>
            <div class="cream-card card flex">
              <div class="image">
                <img src="https://picsum.photos/500" alt="Featured Item" />
              </div>
              <div class="details flex column justify-between">
                <h4>
                  Grilled Turkey Breast with Steamed Asparagus and Brown Rice
                </h4>
                <div class="flex align-center justify-between">
                  <h4 class="price">$3.00</h4>
                  <div class="tags">
                    <span class="pill">Turkey</span
                    ><span class="pill">Customizable</span>
                  </div>
                  <div class="review-rating">
                    <span class="stars"> ★ </span>
                    <span class="rating-text">4.8/5 (125 reviews)</span>
                  </div>
                </div>
                <div class="grid g-af2">
                  <div class="grid-item flex align-center">
                    <div class="image">
                      <svg class="icon"><use href="#bar"></use></svg>
                    </div>
                    <div class="text">
                      <p>Difficulty</p>
                      <h6>Medium</h6>
                    </div>
                  </div>
                  <div class="grid-item flex align-center">
                    <div class="image">
                      <svg class="icon"><use href="#health"></use></svg>
                    </div>
                    <div class="text">
                      <p>Health Score</p>
                      <h6>85/100</h6>
                    </div>
                  </div>
                  <div class="grid-item flex align-center">
                    <div class="image">
                      <svg class="icon"><use href="#cook"></use></svg>
                    </div>
                    <div class="text">
                      <p>Cook Duration</p>
                      <h6>10 minutes</h6>
                    </div>
                  </div>
                  <div class="grid-item flex align-center">
                    <div class="image">
                      <svg class="icon"><use href="#step"></use></svg>
                    </div>
                    <div class="text">
                      <p>Total Steps</p>
                      <h6>4 steps</h6>
                    </div>
                  </div>
                </div>
                <button>
                  <svg
                    width="17"
                    height="16"
                    viewBox="0 0 17 16"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M14.7075 4.58547L11.9144 1.79297C11.8215 1.70009 11.7113 1.62641 11.5899 1.57614C11.4686 1.52587 11.3385 1.5 11.2072 1.5C11.0759 1.5 10.9458 1.52587 10.8245 1.57614C10.7031 1.62641 10.5929 1.70009 10.5 1.79297L2.79313 9.49985C2.69987 9.59237 2.62593 9.70251 2.5756 9.82386C2.52528 9.94521 2.49959 10.0754 2.50001 10.2067V12.9998C2.50001 13.2651 2.60536 13.5194 2.7929 13.707C2.98043 13.8945 3.23479 13.9998 3.50001 13.9998H14C14.1326 13.9998 14.2598 13.9472 14.3536 13.8534C14.4473 13.7596 14.5 13.6325 14.5 13.4998C14.5 13.3672 14.4473 13.2401 14.3536 13.1463C14.2598 13.0525 14.1326 12.9998 14 12.9998H7.70751L14.7075 5.99985C14.8004 5.90699 14.8741 5.79674 14.9243 5.6754C14.9746 5.55406 15.0005 5.424 15.0005 5.29266C15.0005 5.16132 14.9746 5.03127 14.9243 4.90992C14.8741 4.78858 14.8004 4.67834 14.7075 4.58547ZM6.29313 12.9998H3.50001V10.2067L9.00001 4.70672L11.7931 7.49985L6.29313 12.9998ZM12.5 6.79297L9.70751 3.99985L11.2075 2.49985L14 5.29297L12.5 6.79297Z"
                      fill="white"
                    />
                  </svg>

                  Edit Menu
                </button>
              </div>
            </div>
          </div>
          <div class="grid g-af2">
            <div class="popular-menu">
              <div class="menu-header">
                <h2 class="menu-title">Top Rated Menu</h2>
                <button class="more-options">...</button>
              </div>

              <div class="popular-item">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=92"
                    alt="Greek Salad with Feta and Olives"
                  />
                </div>
                <div class="item-details">
                  <div class="flex justify-between align-center">
                    <h3 class="item-name">Greek Salad with Feta and Olives</h3>
                    <button class="add-button">+</button>
                  </div>
                  <div class="flex align-center justify-between">
                    <div class="item-rating">
                      <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-star"
                      >
                        <polygon
                          points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                        ></polygon>
                      </svg>
                      <span>4.9 / 5</span>
                    </div>
                    <span class="item-category salad">Salad</span>
                  </div>
                </div>
              </div>

              <div class="popular-item">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=90"
                    alt="Blueberry Protein Smoothie"
                  />
                </div>
                <div class="item-details">
                  <div class="flex align-center justify-between">
                    <h3 class="item-name">Blueberry Protein Smoothie</h3>
                    <button class="add-button">+</button>
                  </div>
                  <div class="flex align-center justify-between">
                    <div class="item-rating">
                      <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-star"
                      >
                        <polygon
                          points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                        ></polygon>
                      </svg>
                      <span>4.8 / 5</span>
                    </div>
                    <span class="item-category smoothie">Smoothie</span>
                  </div>
                </div>
              </div>

              <div class="popular-item">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=94"
                    alt="Grilled Salmon with Lemon and Asparagus"
                  />
                </div>
                <div class="item-details">
                  <div class="flex align-center justify-between">
                    <h3 class="item-name">
                      Grilled Salmon with Lemon and Asparagus
                    </h3>
                    <button class="add-button">+</button>
                  </div>
                  <div class="flex align-center justify-between">
                    <div class="item-rating">
                      <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-star"
                      >
                        <polygon
                          points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                        ></polygon>
                      </svg>
                      <span>4.9 / 5</span>
                    </div>
                    <span class="item-category salmon">Salmon</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="popular-menu">
              <div class="menu-header">
                <h2 class="menu-title">Top Rated Menu</h2>
                <button class="more-options">...</button>
              </div>

              <div class="popular-item">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=92"
                    alt="Greek Salad with Feta and Olives"
                  />
                </div>
                <div class="item-details">
                  <div class="flex justify-between align-center">
                    <h3 class="item-name">Greek Salad with Feta and Olives</h3>
                    <button class="add-button">+</button>
                  </div>
                  <div class="flex align-center justify-between">
                    <div class="item-rating">
                      <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-star"
                      >
                        <polygon
                          points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                        ></polygon>
                      </svg>
                      <span>4.9 / 5</span>
                    </div>
                    <span class="item-category salad">Salad</span>
                  </div>
                </div>
              </div>

              <div class="popular-item">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=90"
                    alt="Blueberry Protein Smoothie"
                  />
                </div>
                <div class="item-details">
                  <div class="flex align-center justify-between">
                    <h3 class="item-name">Blueberry Protein Smoothie</h3>
                    <button class="add-button">+</button>
                  </div>
                  <div class="flex align-center justify-between">
                    <div class="item-rating">
                      <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-star"
                      >
                        <polygon
                          points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                        ></polygon>
                      </svg>
                      <span>4.8 / 5</span>
                    </div>
                    <span class="item-category smoothie">Smoothie</span>
                  </div>
                </div>
              </div>

              <div class="popular-item">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=94"
                    alt="Grilled Salmon with Lemon and Asparagus"
                  />
                </div>
                <div class="item-details">
                  <div class="flex align-center justify-between">
                    <h3 class="item-name">
                      Grilled Salmon with Lemon and Asparagus
                    </h3>
                    <button class="add-button">+</button>
                  </div>
                  <div class="flex align-center justify-between">
                    <div class="item-rating">
                      <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-star"
                      >
                        <polygon
                          points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                        ></polygon>
                      </svg>
                      <span>4.9 / 5</span>
                    </div>
                    <span class="item-category salmon">Salmon</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="all-menu">
            <div class="menu-controls">
              <div class="flex align-center justify-between">
                <h6>All Menu</h6>
                <div class="menu-actions">
                  <div class="search-bar">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="feather feather-search"
                    >
                      <circle cx="11" cy="11" r="8"></circle>
                      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input
                      type="text"
                      placeholder="Search for menu"
                      id="search-input"
                    />
                  </div>
                  <button class="search-button" id="search-button">
                    Search
                  </button>
                  <button class="filter-button">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="feather feather-filter"
                    >
                      <polygon
                        points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"
                      ></polygon>
                    </svg>
                    Filter
                  </button>
                  <button class="grid-view">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="feather feather-grid"
                    >
                      <rect x="3" y="3" width="7" height="7"></rect>
                      <rect x="14" y="3" width="7" height="7"></rect>
                      <rect x="3" y="14" width="7" height="7"></rect>
                      <rect x="14" y="14" width="7" height="7"></rect>
                    </svg>
                  </button>
                </div>
              </div>
              <div class="flex justify-between align-center">
                <div class="category-tabs">
                  <button class="tab active" data-category="all">All</button>
                  <button class="tab" data-category="chicken">
                    Chicken Dishes
                  </button>
                  <button class="tab" data-category="soups">Soups</button>
                  <button class="tab" data-category="noodles">
                    Noodles & Chow Mein
                  </button>
                  <button class="tab" data-category="rice">Rice Dishes</button>
                  <!-- <button class="tab expand-more">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="feather feather-chevron-down"
                    >
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </button> -->
                </div>

                <div class="sort-options">
                  <span>Sort by:</span>
                  <button class="sort-dropdown" id="sort-dropdown">
                    Popular
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="feather feather-chevron-down"
                    >
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </button>
                  <div class="sort-dropdown-menu" style="display: none">
                    <a href="#" data-sort="popular">Popular</a>
                    <a href="#" data-sort="name">Name</a>
                    <a href="#" data-sort="health">Health Score</a>
                  </div>
                </div>
              </div>
            </div>

            <div class="menu-items-container"></div>
          </div>
        </div>
        <div class="aside flex column">
          <div class="recommended-menu card">
            <div class="menu-header">
              <h2 class="menu-title">Recommended Menu</h2>
              <button class="more-options">...</button>
            </div>

            <div class="menu-item">
              <div class="menu-item-details">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=4"
                    alt="Oatmeal with Almond Butter and Berries"
                  />
                </div>
                <div class="item-details">
                  <h3 class="item-name">
                    Oatmeal with Almond Butter and Berries
                  </h3>
                  <span class="item-category dessert">Dessert</span>
                </div>
                <button class="add-button">+</button>
              </div>
              <div class="nutrition-info">
                <span class="nutrition-item">
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-sun"
                  >
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="17" y1="5" x2="19" y2="7"></line>
                    <line x1="7" y1="5" x2="5" y2="7"></line>
                    <line x1="17" y1="17" x2="19" y2="19"></line>
                    <line x1="7" y1="17" x2="5" y2="19"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                  </svg>
                  350 kcal
                </span>
                <span class="nutrition-item">
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-leaf"
                  >
                    <path
                      d="M18.6 13.4c-.9 1.5-2.4 2.6-4.1 3.2l-6.2 2.5a2 2 0 0 1-2.3-2.3l2.5-6.2c.6-1.7 1.7-3.2 3.2-4.1 2.1-1.3 4.7-1.3 6.8 0 1.5.9 2.6 2.4 3.2 4.1z"
                    ></path>
                    <path d="M12 12l.5-3.2"></path>
                    <path d="M15 15l-3.2-.5"></path>
                  </svg>
                  45g
                </span>
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-zap"
                  >
                    <polygon
                      points="13 2 3 14 12 15 11 22 21 10 12 9 13 2"
                    ></polygon>
                  </svg>
                  12g</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-egg"
                  >
                    <path
                      d="M13 6c0-3.5-5-3.5-5 0 0 2.1.8 4 2 5.3 1.2 1.5 3 2.2 5 2.2s3.8-.7 5-2.2c1.2-1.3 2-3.2 2-5.3 0-3.5-5-3.5-5 0"
                    ></path>
                  </svg>
                  14g</span
                >
              </div>
            </div>

            <div class="menu-item">
              <div class="menu-item-details">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=5"
                    alt="Grilled Chicken Wrap with Avocado and Spinach"
                  />
                </div>
                <div class="item-details">
                  <h3 class="item-name">
                    Grilled Chicken Wrap with Avocado and Spinach
                  </h3>
                  <span class="item-category chicken">Chicken</span>
                </div>
                <button class="add-button">+</button>
              </div>
              <div class="nutrition-info">
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-sun"
                  >
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="17" y1="5" x2="19" y2="7"></line>
                    <line x1="7" y1="5" x2="5" y2="7"></line>
                    <line x1="17" y1="17" x2="19" y2="19"></line>
                    <line x1="7" y1="17" x2="5" y2="19"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                  </svg>
                  450 kcal</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-leaf"
                  >
                    <path
                      d="M18.6 13.4c-.9 1.5-2.4 2.6-4.1 3.2l-6.2 2.5a2 2 0 0 1-2.3-2.3l2.5-6.2c.6-1.7 1.7-3.2 3.2-4.1 2.1-1.3 4.7-1.3 6.8 0 1.5.9 2.6 2.4 3.2 4.1z"
                    ></path>
                    <path d="M12 12l.5-3.2"></path>
                    <path d="M15 15l-3.2-.5"></path>
                  </svg>
                  40g</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-zap"
                  >
                    <polygon
                      points="13 2 3 14 12 15 11 22 21 10 12 9 13 2"
                    ></polygon>
                  </svg>
                  30g</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-egg"
                  >
                    <path
                      d="M13 6c0-3.5-5-3.5-5 0 0 2.1.8 4 2 5.3 1.2 1.5 3 2.2 5 2.2s3.8-.7 5-2.2c1.2-1.3 2-3.2 2-5.3 0-3.5-5-3.5-5 0"
                    ></path>
                  </svg>
                  18g</span
                >
              </div>
            </div>

            <div class="menu-item">
              <div class="menu-item-details">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=2"
                    alt="Quinoa Salad with Roasted Vegetables and Feta"
                  />
                </div>
                <div class="item-details">
                  <h3 class="item-name">
                    Quinoa Salad with Roasted Vegetables and Feta
                  </h3>
                  <span class="item-category salad">Salad</span>
                </div>
                <button class="add-button">+</button>
              </div>
              <div class="nutrition-info">
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-sun"
                  >
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="17" y1="5" x2="19" y2="7"></line>
                    <line x1="7" y1="5" x2="5" y2="7"></line>
                    <line x1="17" y1="17" x2="19" y2="19"></line>
                    <line x1="7" y1="17" x2="5" y2="19"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                  </svg>
                  400 kcal</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-leaf"
                  >
                    <path
                      d="M18.6 13.4c-.9 1.5-2.4 2.6-4.1 3.2l-6.2 2.5a2 2 0 0 1-2.3-2.3l2.5-6.2c.6-1.7 1.7-3.2 3.2-4.1 2.1-1.3 4.7-1.3 6.8 0 1.5.9 2.6 2.4 3.2 4.1z"
                    ></path>
                    <path d="M12 12l.5-3.2"></path>
                    <path d="M15 15l-3.2-.5"></path>
                  </svg>
                  50g</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-zap"
                  >
                    <polygon
                      points="13 2 3 14 12 15 11 22 21 10 12 9 13 2"
                    ></polygon>
                  </svg>
                  15g</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-egg"
                  >
                    <path
                      d="M13 6c0-3.5-5-3.5-5 0 0 2.1.8 4 2 5.3 1.2 1.5 3 2.2 5 2.2s3.8-.7 5-2.2c1.2-1.3 2-3.2 2-5.3 0-3.5-5-3.5-5 0"
                    ></path>
                  </svg>
                  12g</span
                >
              </div>
            </div>
          </div>
          <div class="new-menu card">
            <div class="menu-header">
              <h2 class="menu-title">New Menu</h2>
              <button class="more-options">...</button>
            </div>

            <div class="menu-item">
              <div class="menu-item-details">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=4"
                    alt="Oatmeal with Almond Butter and Berries"
                  />
                </div>
                <div class="item-details">
                  <h3 class="item-name">
                    Oatmeal with Almond Butter and Berries
                  </h3>
                  <span class="item-category dessert">Dessert</span>
                </div>
                <button class="add-button">+</button>
              </div>
              <div class="nutrition-info">
                <span class="nutrition-item">
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-sun"
                  >
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="17" y1="5" x2="19" y2="7"></line>
                    <line x1="7" y1="5" x2="5" y2="7"></line>
                    <line x1="17" y1="17" x2="19" y2="19"></line>
                    <line x1="7" y1="17" x2="5" y2="19"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                  </svg>
                  350 kcal
                </span>
                <span class="nutrition-item">
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-leaf"
                  >
                    <path
                      d="M18.6 13.4c-.9 1.5-2.4 2.6-4.1 3.2l-6.2 2.5a2 2 0 0 1-2.3-2.3l2.5-6.2c.6-1.7 1.7-3.2 3.2-4.1 2.1-1.3 4.7-1.3 6.8 0 1.5.9 2.6 2.4 3.2 4.1z"
                    ></path>
                    <path d="M12 12l.5-3.2"></path>
                    <path d="M15 15l-3.2-.5"></path>
                  </svg>
                  45g
                </span>
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-zap"
                  >
                    <polygon
                      points="13 2 3 14 12 15 11 22 21 10 12 9 13 2"
                    ></polygon>
                  </svg>
                  12g</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-egg"
                  >
                    <path
                      d="M13 6c0-3.5-5-3.5-5 0 0 2.1.8 4 2 5.3 1.2 1.5 3 2.2 5 2.2s3.8-.7 5-2.2c1.2-1.3 2-3.2 2-5.3 0-3.5-5-3.5-5 0"
                    ></path>
                  </svg>
                  14g</span
                >
              </div>
            </div>

            <div class="menu-item">
              <div class="menu-item-details">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=5"
                    alt="Grilled Chicken Wrap with Avocado and Spinach"
                  />
                </div>
                <div class="item-details">
                  <h3 class="item-name">
                    Grilled Chicken Wrap with Avocado and Spinach
                  </h3>
                  <span class="item-category chicken">Chicken</span>
                </div>
                <button class="add-button">+</button>
              </div>
              <div class="nutrition-info">
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-sun"
                  >
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="17" y1="5" x2="19" y2="7"></line>
                    <line x1="7" y1="5" x2="5" y2="7"></line>
                    <line x1="17" y1="17" x2="19" y2="19"></line>
                    <line x1="7" y1="17" x2="5" y2="19"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                  </svg>
                  450 kcal</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-leaf"
                  >
                    <path
                      d="M18.6 13.4c-.9 1.5-2.4 2.6-4.1 3.2l-6.2 2.5a2 2 0 0 1-2.3-2.3l2.5-6.2c.6-1.7 1.7-3.2 3.2-4.1 2.1-1.3 4.7-1.3 6.8 0 1.5.9 2.6 2.4 3.2 4.1z"
                    ></path>
                    <path d="M12 12l.5-3.2"></path>
                    <path d="M15 15l-3.2-.5"></path>
                  </svg>
                  40g</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-zap"
                  >
                    <polygon
                      points="13 2 3 14 12 15 11 22 21 10 12 9 13 2"
                    ></polygon>
                  </svg>
                  30g</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-egg"
                  >
                    <path
                      d="M13 6c0-3.5-5-3.5-5 0 0 2.1.8 4 2 5.3 1.2 1.5 3 2.2 5 2.2s3.8-.7 5-2.2c1.2-1.3 2-3.2 2-5.3 0-3.5-5-3.5-5 0"
                    ></path>
                  </svg>
                  18g</span
                >
              </div>
            </div>

            <div class="menu-item">
              <div class="menu-item-details">
                <div class="item-image">
                  <img
                    src="https://picsum.photos/300?random=2"
                    alt="Quinoa Salad with Roasted Vegetables and Feta"
                  />
                </div>
                <div class="item-details">
                  <h3 class="item-name">
                    Quinoa Salad with Roasted Vegetables and Feta
                  </h3>
                  <span class="item-category salad">Salad</span>
                </div>
                <button class="add-button">+</button>
              </div>
              <div class="nutrition-info">
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-sun"
                  >
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="17" y1="5" x2="19" y2="7"></line>
                    <line x1="7" y1="5" x2="5" y2="7"></line>
                    <line x1="17" y1="17" x2="19" y2="19"></line>
                    <line x1="7" y1="17" x2="5" y2="19"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                  </svg>
                  400 kcal</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-leaf"
                  >
                    <path
                      d="M18.6 13.4c-.9 1.5-2.4 2.6-4.1 3.2l-6.2 2.5a2 2 0 0 1-2.3-2.3l2.5-6.2c.6-1.7 1.7-3.2 3.2-4.1 2.1-1.3 4.7-1.3 6.8 0 1.5.9 2.6 2.4 3.2 4.1z"
                    ></path>
                    <path d="M12 12l.5-3.2"></path>
                    <path d="M15 15l-3.2-.5"></path>
                  </svg>
                  50g</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-zap"
                  >
                    <polygon
                      points="13 2 3 14 12 15 11 22 21 10 12 9 13 2"
                    ></polygon>
                  </svg>
                  15g</span
                >
                <span class="nutrition-item"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="feather feather-egg"
                  >
                    <path
                      d="M13 6c0-3.5-5-3.5-5 0 0 2.1.8 4 2 5.3 1.2 1.5 3 2.2 5 2.2s3.8-.7 5-2.2c1.2-1.3 2-3.2 2-5.3 0-3.5-5-3.5-5 0"
                    ></path>
                  </svg>
                  12g</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    
   <script>
// Pass PHP variables to JavaScript
const username = '<?php echo addslashes($username); ?>';
const userRole = '<?php echo addslashes($userRole); ?>';
const profilePicture = '<?php echo addslashes($profilePicture); ?>';
</script>
    <script src="../scripts/components.js"></script>
    <script src="../scripts/menu.js"></script>
  </body>
</html>
