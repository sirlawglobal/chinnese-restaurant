<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../assets/styles/main.css" />
    <link rel="stylesheet" href="../assets/styles/forms.css" />
    <link rel="stylesheet" href="../assets/styles/signin.css" />
    <title>Admin Sign In</title>
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
          <img src="../../assets/images/admin-logo.png" alt="Golden Dish" />
        </div>
        <div class="head">
          <?php if(isset($_SESSION["fail"])):?>
          <div class="custom-alert error"><?=$_SESSION["fail"]?>
           <?php unset($_SESSION["fail"]);
          ?></div>
      <?php endif?>
          <h3>Sign In</h3>
          <p>Sign in to stay connected.</p>
        </div>
        <form>
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
        <img src="../../assets/images/admin-img.png" alt="Golden Dish" />
      </section>
    </main>
    <script>
      // on submit redirect to /dashboard
      document
        .querySelector("form")
        .addEventListener("submit", function (event) {
          event.preventDefault();
          window.location.href = "../dashboard";
        });
    </script>
  </body>
</html>
