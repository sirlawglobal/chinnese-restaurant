<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golden Dish | Order Confirmation</title>
    <link rel="stylesheet" href="../assets/styles/main.css">
    <link rel="stylesheet" href="../assets/styles/checkout.css">
    <script src="https://cdn.jsdelivr.net/npm/dompurify@2.3.6/dist/purify.min.js"></script>
</head>
<body>
    <div id="sprite" hidden></div>
    <div class="wrapper">
        <header class="header">
            <div class="header__top">
                <div class="flex column">
                    <a href="../" class="header__logo">
                        <img src="../assets/images/logo2.webp" alt="Golden Dish Logo">
                    </a>
                </div>
                <div class="flex justify-end align-center wrap">
                    <button class="button button--signin">Sign in</button>
                </div>
            </div>
        </header>
        <main class="main">
            <h3 class="main__title">Order Confirmation</h3>
            <section class="content flex justify-center">
                <div class="confirmation">
                    <div id="confirmationMessage" class="inner">
                        <h4>Verifying your payment...</h4>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <footer class="footer flex align-center justify-between">
        <div class="footer__content">
            <div class="footer__socials">
                <a href="#" class="footer__icon"><svg class="icon"><use href="#facebook"></use></svg></a>
                <a href="#" class="footer__icon"><svg class="icon"><use href="#insta"></use></svg></a>
                <a href="#" class="footer__icon"><svg class="icon"><use href="#twitter"></use></svg></a>
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
        const sprite = document.getElementById("sprite");
        (async () => {
            try {
                const data = await fetch("../assets/icons-sprite.svg").then((response) => response.text());
                sprite.innerHTML = data;
            } catch (error) {
                console.error("Error loading SVG sprite:", error);
            }
        })();

        document.addEventListener("DOMContentLoaded", async () => {
            const confirmationMessage = document.getElementById("confirmationMessage");
            const urlParams = new URLSearchParams(window.location.search);
            const tx_ref = urlParams.get("tx_ref");
            const transaction_id = urlParams.get("transaction_id");

            if (!tx_ref || !transaction_id) {
                confirmationMessage.innerHTML = "<h4>Error: Invalid transaction details.</h4>";
                return;
            }

            try {
                const response = await fetch(`../api/verify_payment.php?tx_ref=${encodeURIComponent(tx_ref)}&transaction_id=${encodeURIComponent(transaction_id)}`);
                const result = await response.json();

                if (result.success) {
                    confirmationMessage.innerHTML = `
                        <h4>Order Confirmed!</h4>
                        <p>Your payment was successful. Order ID: ${DOMPurify.sanitize(result.order_id)}</p>
                        <p>Thank you for ordering with Golden Dish. You'll receive a confirmation soon.</p>
                        <a href="../" class="button">Back to Home</a>
                    `;
                } else {
                    confirmationMessage.innerHTML = `
                        <h4>Payment Failed</h4>
                        <p>${DOMPurify.sanitize(result.message)}</p>
                        <a href="../checkout/" class="button">Try Again</a>
                    `;
                }
            } catch (error) {
                confirmationMessage.innerHTML = `
                    <h4>Error</h4>
                    <p>Error verifying payment: ${DOMPurify.sanitize(error.message)}</p>
                    <a href="../checkout/" class="button">Try Again</a>
                `;
            }
        });
    </script>
</body>
</html>