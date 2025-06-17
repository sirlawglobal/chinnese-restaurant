<?php 
require_once __DIR__ . '/../BackEnd/config/init.php';

if(isLoggedIn())
{

  if(isAdmin()){
$role = true;
//var_dump( $role);die;
  } else{
 $role = false;
    redirect($url ."menu");
    exit();
  }  
 
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/styles/main.css" />
    <link rel="stylesheet" href="../assets/styles/forms.css" />
    <link rel="stylesheet" href="../assets/styles/signin.css" />
    <title>Admin Sign In</title>
  </head>
  <body>
    <main class="flex align-center justify-center wrap">
      <section class="content">
        <div class="logo">
          <img src="../assets/images/admin-logo.png" alt="Golden Dish" />
        </div>
        <div class="head">
           <?php  if(isset($role) && $role == true): ?> 
          <h3>Let Add Our Staffs</h3>
          <p>Add Staff by their rank.</p>
<?php else: ?>
    <h3>Sign Up</h3>
          <p>Get started to start feeding like a king.</p>
          <?php endif;?>
        </div>
     <!-- <form id="registrationForm" method="POST" action="register.php"> -->
     <form id="register-form" method="POST" >
  <fieldset>
    <legend>Fullname</legend>
    <input type="text" name="name" id="name" required />
  </fieldset>
  <fieldset>
    <legend>Phone</legend>
    <input type="tel" name="phone" id="phone" required />
  </fieldset>
  <?php  if(isset($role) && $role == true): ?> 
 <fieldset>
  <legend>Role</legend>
  <select name="role" id="role">
    <option value="">-- Select Role --</option>
    <option value="admin">Admin</option>
    <option value="staff">Staff</option>
    <option value="chef">Chef</option>
    <option value="waiter">Waiter</option>
    <option value="cashier">Cashier</option>
    <option value="manager">Manager</option>
    <option value="dishwasher">Dishwasher</option>
    <option value="delivery">Delivery Personnel</option>
    <option value="bartender">Bartender</option>
  </select>
</fieldset>
<?php endif;?>
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
  </div>
  <div class="flex align-center justify-center">
    <button type="submit" id="register-btn">Sign Up</button>
  </div>
    
  <div class="flex align-center justify-center">
              <p>No acccount yet?</p>

               <a  href="../login/">Sign in</a>
          </div>
</form>

      </section>
      <section class="image">
        <img src="../assets/images/admin-img.png" alt="Golden Dish" />
      </section>
    </main>
   <script>

  const registerForm = document.getElementById("register-form");

  registerForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(registerForm);
    // const payload = {
    //   data_type: "register",
    //   name: formData.get("name"),
    //   role: formData.get("role"),
    //   phone: formData.get("phone"),
    //   email: formData.get("email"),
    //   password: formData.get("password")
    // };
const payload = {
  data_type: "register",
  name: formData.get("name"),
  phone: formData.get("phone"),
  email: formData.get("email"),
  password: formData.get("password")
};

const role = formData.get("role");
if (role !== null) {
  payload.role = role;
}
 send_data("register", payload);
  });
function send_data(data_type = "register", payload = {}) {
  const submitBtn = document.getElementById("register-btn");
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.textContent = "Registering...";
  }

  const ajax = new XMLHttpRequest();
  ajax.onreadystatechange = function () {
    if (ajax.readyState === 4) {
      if (ajax.status === 200 && ajax.responseText.trim() !== "") {
        handle_result(ajax.responseText, data_type);
      }

      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = "Register";
      }
    }
  }; 

  let ROOTS = "<?= ROOT ?>"; // Works inside PHP file
  ajax.open("POST", ROOTS + "/BackEnd/controller/auth/register.php", true);
  ajax.setRequestHeader("Content-Type", "application/json");
  ajax.send(JSON.stringify(payload));
}

function handle_result(result, data_type) {
  const obj = JSON.parse(result);
  if (obj.data_type !== data_type) return;

  alert(obj.message);

  if (data_type === "register" && obj.success) {
    const form = document.getElementById("register-form");
    if (form) form.reset();
  }

  if (obj.success && obj.redirect) {
    window.location.href = obj.redirect;
  }
}


</script>

  </body>
</html>
