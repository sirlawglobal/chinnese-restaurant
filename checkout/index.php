<?php
require_once __DIR__ . '/../BackEnd/config/init.php';



// Fetch user email if logged in
$userEmail = isLoggedIn() && isset($_SESSION['user']['email']) && !empty($_SESSION['user']['email']) ? $_SESSION['user']['email'] : '';
$role = isLoggedIn();

// Debugging session data
error_log("Checkout - isLoggedIn: " . ($role ? 'true' : 'false') . ", userEmail: " . $userEmail);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golden Dish | Checkout</title>
    <link rel="stylesheet" href="../assets/styles/main.css">
    <link rel="stylesheet" href="../assets/styles/checkout.css">
    <!-- Include DOMPurify for sanitization -->
    <script src="//cdn.jsdelivr.net/npm/dompurify@2.1.0/dist/purify.min.js"></script>
    <!-- Include Flutterwave SDK -->
    <script src="https://checkout.flutterwave.com/v3.js"></script>
</head>
<body>
    <div class="sprite" hidden></div>
    <div class="wrapper">
        <header class="header">
            <div class="header__top">
                <div class="flex column">
                    <a href="../" class="header__logo">
                        <img src="../assets/images/logo2.webp" alt="Golden Dish Logo" />
                    </a>
                </div>
                <div class="flex justify-end align-center wrap">
                    <div class="header__search">
                        <input
                            type="text"
                            placeholder="Enter item you are looking for"
                            class="search__input"
                            aria-label="Search for menu items"
                        />
                        <button class="search__button" aria-label="Search">
                            <svg class="icon"><use href="#search"></use></svg>
                        </button>
                    </div>
                    <button
                        class="button--cart flex align-center justify-center"
                        aria-label="View cart"
                        id="cartButton"
                    >
                        <svg class="icon"><use href="#bag"></use></svg>
                        <span class="cart-badge">0</span>
                    </button>
                    <div class="buttons">
                        <?php if ($role): ?>
                            <a class="button button-primary" href="../BackEnd/controller/auth/logout.php">Logout</a>
                        <?php else: ?>
                            <a class="button button-primary" href="../login/">Log in</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <main class="main">
            <h3 class="main__title">Secure Checkout</h3>
            <section class="content flex justify-between">
                <div class="delivery__details">
                    <div class="inner">
                        <h3 class="flex align-center delivery__address">
                            <svg class="icon"><use href="#location"></use></svg>
                            Delivery Address
                        </h3>
                        <textarea
                            class="textarea"
                            id="deliveryAddress"
                            name="delivery_address"
                            placeholder="Type your address here"
                            aria-label="Delivery address"
                            required
                        ></textarea>

                        <?php if (!$role): ?>
                            <div class="guest-email" id="guestEmailContainer">
                                <label for="guestEmail" class="form-label">Email</label>
                                <input
                                    type="email"
                                    id="guestEmail"
                                    class="form-input"
                                    placeholder="Enter your email"
                                    aria-label="Guest email"
                                    style="border-radius: 10px; padding: 8px; width: 100%; border: 1px solid rgba(2, 2, 2, 0.589);"
                                    required
                                />
                            </div>
                        <?php endif; ?>

                        <h3 class="flex align-center order__type">
                            <svg class="icon"><use href="#location"></use></svg>
                            Type of Order
                        </h3>
                        <div class="flex justify-center align-center order__actions">
                            <button class="button--action active" id="orderNow" data-type="now">
                                <svg class="icon"><use href="#schedule"></use></svg>
                                Order Now
                            </button>
                            <button class="button--action" id="scheduleOrder" data-type="schedule">
                                <svg class="icon"><use href="#schedule"></use></svg>
                                Schedule Order
                            </button>
                        </div>
                        <div class="schedule-details" id="scheduleDetails" style="display: none;">
                            <label for="scheduleDate" class="form-label">Select Date</label>
                            <input type="date" id="scheduleDate" class="form-input" />
                            <label for="scheduleTime" class="form-label">Select Time</label>
                            <input type="time" id="scheduleTime" class="form-input" />
                        </div>
                        <div class="order__note">
                            <h4>Any Note for us?</h4>
                            <textarea
                                class="textarea"
                                id="orderNotes"
                                name="order_notes"
                                placeholder="Type your note here"
                                aria-label="Order notes"
                            ></textarea>
                        </div>
                    </div>
                </div>
                <div class="receipt">
                    <div class="cart">
                        <div class="flex justify-between align-center cart__header">
                            <h3 class="cart__title">Cart</h3>
                            <p class="cart__count">0 Items</p>
                        </div>
                        <div class="cart__content"></div>
                    </div>
                    <div class="bill__details">
                        <p>Bill details</p>
                        <div class="flex justify-between align-center">
                            <p>Item Total</p>
                            <p class="item-total">£0.00</p>
                        </div>
                        <div class="flex justify-between align-center">
                            <p>Delivery Fee | 12.9 kms</p>
                            <p class="delivery-fee">£0.00</p>
                        </div>
                        <div class="flex justify-between align-center">
                            <p>Taxes and Charges</p>
                            <p class="taxes">£0.00</p>
                        </div>
                    </div>
                    <p class="deets">
                        Monthly + 3 Days/Week plan + 16:30 Delivery time
                    </p>
                    <div class="cart__subtotal">
                        <div class="flex justify-between align-center">
                            <p>Subtotal</p>
                            <p class="subtotal">£0.00</p>
                        </div>
                        <div class="flex justify-between align-center">
                            <p>Discount</p>
                            <p class="discount">£0.00</p>
                        </div>
                    </div>
                    <div class="cart__total">
                        <div>
                            <p class="cart__total__text">Total:</p>
                        </div>
                        <p class="cart__total__price">£0.00</p>
                    </div>
                    <button class="button--checkout" id="proceedToPayment">Proceed To Payment</button>
                </div>
            </section>
        </main>
    </div>
    <footer class="footer flex align-center justify-between">
        <div class="footer__content">
            <div class="footer__socials">
                <a href="#" class="footer__icon">
                    <svg class="icon"><use href="#facebook"></use></svg>
                </a>
                <a href="#" class="footer__icon">
                    <svg class="icon"><use href="#insta"></use></svg>
                </a>
                <a href="#" class="footer__icon">
                    <svg class="icon"><use href="#twitter"></use></svg>
                </a>
            </div>
        </div>
        <nav class="footer__nav">
            <a href="#" class="footer__link">About us</a>
            <a href="#" class="footer__link">Delivery</a>
            <a href="#" class="footer__link">Help & Support</a>
            <a href="#" class="footer__link">T&C</a>
        </nav>
    </footer>

    <script>
    // Load SVG sprite
    (async () => {
        try {
            const response = await fetch("../assets/icons-sprite.svg");
            const data = await response.text();
            document.querySelector(".sprite").innerHTML = data;
        } catch (error) {
            console.error("Error loading SVG sprite:", error);
        }
    })();

    document.addEventListener("DOMContentLoaded", function () {
        const cartContainer = document.querySelector(".cart__content");
        const cartCount = document.querySelector(".cart__count");
        const cartBadge = document.querySelector(".cart-badge");
        const itemTotal = document.querySelector(".item-total");
        const deliveryFee = document.querySelector(".delivery-fee");
        const taxes = document.querySelector(".taxes");
        const subtotal = document.querySelector(".subtotal");
        const discount = document.querySelector(".discount");
        const totalPrice = document.querySelector(".cart__total__price");
        const orderNowBtn = document.getElementById("orderNow");
        const scheduleOrderBtn = document.getElementById("scheduleOrder");
        const scheduleDetails = document.getElementById("scheduleDetails");
        const proceedToPaymentBtn = document.getElementById("proceedToPayment");
        let cartItems = JSON.parse(localStorage.getItem("cart")) || [];
        let orderType = "now";

        // Debugging: Log initial state
        const isLoggedIn = <?php echo json_encode($role); ?>;
        const userEmail = <?php echo json_encode($userEmail); ?>;
        console.log("Checkout - isLoggedIn:", isLoggedIn, "userEmail:", userEmail);

        // Toggle order type buttons
        orderNowBtn.addEventListener("click", () => {
            orderType = "now";
            orderNowBtn.classList.add("active");
            scheduleOrderBtn.classList.remove("active");
            scheduleDetails.style.display = "none";
        });

        scheduleOrderBtn.addEventListener("click", () => {
            orderType = "schedule";
            scheduleOrderBtn.classList.add("active");
            orderNowBtn.classList.remove("active");
            scheduleDetails.style.display = "block";
        });

        // Function to render cart items
        function renderCartItems() {
            cartContainer.innerHTML = "";
            if (cartItems.length === 0) {
                cartContainer.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
                cartCount.textContent = "0 Items";
                cartBadge.textContent = "0";
                cartBadge.style.display = "none";
                updateBillDetails(0);
                proceedToPaymentBtn.disabled = true;
                return;
            }

            const categories = JSON.parse(localStorage.getItem("menu_categories")) || [];

            function getCategoryNameById(id) {
                const category = categories.find(cat => cat.id === id);
                return category ? category.name : "Unknown";
            }

            cartItems.forEach((item, index) => {
                const categoryName = getCategoryNameById(item.category);
                const cartItem = document.createElement("div");
                cartItem.className = "cart__item";
                cartItem.innerHTML = `
                    <p class="dish__origin">from <span>${DOMPurify.sanitize(categoryName)}</span></p>
                    <div class="flex justify-between align-center">
                        <div class="item__details">
                            <h4 class="item__title">${DOMPurify.sanitize(item.name)} (${DOMPurify.sanitize(item.portion)})</h4>
                            <p class="item__price">£${(parseFloat(item.price) || 0).toFixed(2)}</p>
                        </div>
                        <div class="cart__actions">
                            <button class="action__button" aria-label="Decrease quantity of ${DOMPurify.sanitize(item.name)}">-</button>
                            <input class="item__count" type="number" min="1" value="${item.quantity}" aria-label="Quantity of ${DOMPurify.sanitize(item.name)}" />
                            <button class="action__button" aria-label="Increase quantity of ${DOMPurify.sanitize(item.name)}">+</button>
                        </div>
                    </div>
                `;
                cartContainer.appendChild(cartItem);
            });

            updateCartBadge();
            updateBillDetails();
            initCartItems();
            proceedToPaymentBtn.disabled = false;
        }

        // Function to update bill details
        function updateBillDetails() {
            let itemTotalValue = 0;
            cartItems.forEach((item) => {
                const price = parseFloat(item.price) || 0;
                const quantity = item.quantity || 1;
                itemTotalValue += price * quantity;
            });

            const deliveryFeeValue = cartItems.length > 0 ? 131.00 : 0;
            const taxesValue = cartItems.length > 0 ? 2.00 : 0;
            const discountValue = cartItems.length > 0 ? 4.00 : 0;
            const subtotalValue = itemTotalValue + deliveryFeeValue + taxesValue;
            const totalValue = Math.max(subtotalValue - discountValue, 0);

            itemTotal.textContent = `£${itemTotalValue.toFixed(2)}`;
            deliveryFee.textContent = `£${deliveryFeeValue.toFixed(2)}`;
            taxes.textContent = `£${taxesValue.toFixed(2)}`;
            subtotal.textContent = `£${subtotalValue.toFixed(2)}`;
            discount.textContent = `£${discountValue.toFixed(2)}`;
            totalPrice.textContent = `£${totalValue.toFixed(2)}`;
        }

        // Function to update cart badge and count
        function updateCartBadge() {
            const totalItems = cartItems.reduce((sum, item) => sum + (item.quantity || 1), 0);
            cartBadge.textContent = totalItems;
            cartBadge.style.display = totalItems > 0 ? "flex" : "none";
            cartCount.textContent = `${totalItems} Item${totalItems !== 1 ? "s" : ""}`;
            localStorage.setItem("cart", JSON.stringify(cartItems));
        }

        // Initialize quantity controls for each cart item
        function initCartItems() {
            const currentItems = document.querySelectorAll(".cart__item");
            currentItems.forEach((item, index) => {
                const minusBtn = item.querySelector(".action__button:first-child");
                const plusBtn = item.querySelector(".action__button:last-child");
                const quantityInput = item.querySelector(".item__count");

                const newMinusBtn = minusBtn.cloneNode(true);
                const newPlusBtn = plusBtn.cloneNode(true);
                minusBtn.replaceWith(newMinusBtn);
                plusBtn.replaceWith(newPlusBtn);

                newMinusBtn.addEventListener("click", () => {
                    let currentValue = parseInt(quantityInput.value);
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                        cartItems[index].quantity = currentValue - 1;
                    } else {
                        cartItems.splice(index, 1);
                        renderCartItems();
                    }
                    updateCartBadge();
                    updateBillDetails();
                });

                newPlusBtn.addEventListener("click", () => {
                    let currentValue = parseInt(quantityInput.value);
                    quantityInput.value = currentValue + 1;
                    cartItems[index].quantity = currentValue + 1;
                    updateCartBadge();
                    updateBillDetails();
                });

                quantityInput.addEventListener("change", () => {
                    let value = parseInt(quantityInput.value);
                    if (isNaN(value) || value < 1) {
                        quantityInput.value = 1;
                        cartItems[index].quantity = 1;
                    } else {
                        cartItems[index].quantity = value;
                    }
                    updateCartBadge();
                    updateBillDetails();
                });
            });
        }

        // Generate unique transaction reference
        function generateTxRef() {
            return "GD_" + Math.floor(Math.random() * 1000000000) + "_" + Date.now();
        }

        proceedToPaymentBtn.addEventListener("click", async () => {
            const deliveryAddress = DOMPurify.sanitize(document.getElementById("deliveryAddress").value);
            const orderNotes = DOMPurify.sanitize(document.getElementById("orderNotes").value);
            const scheduleDate = document.getElementById("scheduleDate").value;
            const scheduleTime = document.getElementById("scheduleTime").value;
            const guestEmail = DOMPurify.sanitize(document.getElementById("guestEmail")?.value || "");

            // Debugging: Log inputs
            console.log("Payment Inputs - isLoggedIn:", isLoggedIn, "userEmail:", userEmail, "guestEmail:", guestEmail);

            // Validation
            if (!deliveryAddress.trim()) {
                alert("Please provide a delivery address.");
                return;
            }
            if (orderType === "schedule" && (!scheduleDate || !scheduleTime)) {
                alert("Please select a date and time for scheduled order.");
                return;
            }
            if (!cartItems || cartItems.length === 0) {
                alert("Your cart is empty.");
                return;
            }
            if (!isLoggedIn && (!guestEmail.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(guestEmail))) {
                alert("Please provide a valid email address for guest checkout.");
                return;
            }
            if (isLoggedIn && !userEmail) {
                alert("User email not found. Please log in again.");
                return;
            }

            // Calculate total
            let itemTotalValue = 0;
            cartItems.forEach((item) => {
                let price = typeof item.price === 'string' ? parseFloat(item.price.replace('£', '').trim()) : parseFloat(item.price);
                if (isNaN(price) || price < 0) {
                    console.error(`Invalid price for item ${item.name}:`, item.price);
                    price = 0;
                }
                const quantity = parseInt(item.quantity) || 1;
                itemTotalValue += price * quantity;
            });

            const deliveryFeeValue = cartItems.length > 0 ? 131.00 : 0;
            const taxesValue = cartItems.length > 0 ? 2.00 : 0;
            const discountValue = 0;
            const totalValue = Math.max(itemTotalValue + deliveryFeeValue + taxesValue - discountValue, 0);

            // Validate total
            if (totalValue <= 0) {
                alert("Order total must be greater than £0. Please add more items.");
                return;
            }

            // Prepare order data
            const orderData = {
                cart: cartItems.map(item => ({
                    name: item.name,
                    price: typeof item.price === 'string' ? parseFloat(item.price.replace('£', '').trim()) : parseFloat(item.price),
                    quantity: parseInt(item.quantity) || 1,
                    portion: item.portion,
                    category: item.category
                })),
                delivery_address: deliveryAddress,
                order_notes: orderNotes,
                order_type: orderType,
                schedule_date: orderType === "schedule" ? scheduleDate : null,
                schedule_time: orderType === "schedule" ? scheduleTime : null,
                total_amount: totalValue,
                tx_ref: generateTxRef(),
                guest_email: isLoggedIn ? null : guestEmail,
                user_email: isLoggedIn ? userEmail : null
            };

            // Debugging: Log order data
            console.log("Order Data:", orderData);

            try {
                const response = await fetch("./initiate_payment.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(orderData),
                });

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message || "Unknown server error");
                }

                // Initiate Flutterwave payment
                FlutterwaveCheckout({
                    public_key: "FLWPUBK_TEST-d5a8da37df0a15b8a096c077ff8f3e5e-X",
                    tx_ref: result.tx_ref,
                    amount: parseFloat(totalValue.toFixed(2)),
                    currency: "GBP",
                    payment_options: "card,banktransfer,applepay,googlepay,mobilemoney,qr",
                    redirect_url: "../confirmation/index.php",
                    meta: { order_id: result.order_id },
                    customer: {
                        email: isLoggedIn ? userEmail : guestEmail,
                        name: "Golden Dish Customer",
                        phone_number: "1234567890"
                    },
                    customizations: {
                        title: "Golden Dish Payment",
                        description: "Payment for your order at Golden Dish",
                        logo: "../assets/images/logo2.webp",
                    },
                    callback: function (data) {
                        if (data.status === "successful") {
                            localStorage.removeItem("cart");
                            window.location.href = `../confirmation/index.php?tx_ref=${encodeURIComponent(data.tx_ref)}&transaction_id=${data.transaction_id}`;
                        }
                    },
                    onclose: function () {
                        console.log("Payment modal closed");
                    },
                });
            } catch (error) {
                console.error("Error initiating payment:", error);
                alert("Error initiating payment: " + error.message);
            }
        });

        // Initial render
        renderCartItems();

        // Observe cart container for dynamic updates
        const observer = new MutationObserver(() => {
            initCartItems();
            updateBillDetails();
        });

        if (cartContainer) {
            observer.observe(cartContainer, {
                childList: true,
                subtree: true,
            });
        }
    });

    document.getElementById("cartButton").addEventListener("click", function () {
        window.location.href = "../cart/";
    });
    </script>
</body>
</html>