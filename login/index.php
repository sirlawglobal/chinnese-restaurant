<?php require_once __DIR__ . '/../BackEnd/config/init.php';  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/styles/main.css" />
    <link rel="stylesheet" href="../assets/styles/forms.css" />
    <link rel="stylesheet" href="../assets/styles/signin.css" />
    <title>Sign In</title>
  </head>
  <body>


  <style>
      .custom-alert {
  padding: 15px 20px;
  margin: 15px auto;
  width: 90%;
  max-width: 600px;
  border-radius: 5px;
  text-align: center;
  font-family: Arial, sans-serif;
  font-size: 16px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.custom-alert.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.custom-alert.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
/* for error check */
.error {
  color: red;
  font-size: 0.85rem;
}

</style>

    <main class="flex align-center justify-center wrap">
      <section class="content">
        <div class="logo">
          <img src="../assets/images/admin-logo.png" alt="Golden Dish" />
        </div>
        <div class="head">
          <h3>Sign In</h3>
          <p>Sign in to stay connected.</p>
                <?php if(isset($_SESSION["fail"])):?>
          <div class="custom-alert error"><?=$_SESSION["fail"]?>
           <?php unset($_SESSION["fail"]);
          ?></div>
      <?php endif?>
        </div>
        <form id="login-form" method="POST">
          <fieldset>
            <legend>Email</legend>
            <input type="email" name="email" id="email" required />
          </fieldset>
          <fieldset>
            <legend>Password</legend>
            <input type="password" name="password" id="password" required />
          </fieldset>
          <div class="flex justify-between align-center">
            <div class="remember flex align-center">
              <input type="checkbox" name="remember" id="remember" />
              <label for="remember">Remember Me?</label>
            </div>
            <a href="../forgot-password/">Forgot Password</a>
          </div>
          <div class="flex align-center justify-center">
            <button type="submit">Sign In</button>
          </div>
        </form>
      </section>
      <section class="image">
        <img src="../assets/images/admin-img.png" alt="Golden Dish" />
      </section>
    </main>
    <script>


document.querySelector('form').addEventListener('submit', function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  const payload = {
    data_type: "login",
    email: formData.get("email"),
    password: formData.get("password")
  };

  send_data("login", payload);
});

function send_data(data_type = "login", payload = {}) {
  const submitBtn = document.querySelector("#submitBtn");
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.textContent = "Logging in...";
  }

  const ajax = new XMLHttpRequest();
  ajax.onreadystatechange = function () {
    if (ajax.readyState === 4) {
      if (ajax.status === 200 && ajax.responseText.trim() !== "") {
        handle_result(ajax.responseText, data_type);
      }

      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = "Login";
      }
    }
  };
   let ROOTS = "<?= ROOT ?>"; // This works ONLY inside a PHP file
  ajax.open("POST", ROOTS + "/backEnd/controller/auth/login.php", true);

  ajax.setRequestHeader("Content-Type", "application/json");
  ajax.send(JSON.stringify(payload));
}

function handle_result(result, data_type) {
  const obj = JSON.parse(result);
  if (obj.data_type !== data_type) return;

  alert(obj.message);

  if (obj.success && obj.redirect) {
    window.location.href = obj.redirect;
  }
}
</script>



  </body>
</html>
