<?php
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="css/style1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
  <div class="container">
    <div class="form-box box">

      <?php
      include "connection.php";

      if (isset($_POST['login'])) {

        $email = trim($_POST['email']);
        $pass = $_POST['password'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          echo "<div class='message'>
                    <p>Invalid email address</p>
                    </div><br>";
          echo "<a href='login.php'><button class='btn'>Go Back</button></a>";
        } else {
          $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $res = $stmt->get_result();

          if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $password = $row['password'];

            $decrypt = password_verify($pass, $password);

            if ($decrypt) {
              session_regenerate_id(true);
              $_SESSION['id'] = $row['id'];
              $_SESSION['username'] = $row['username'];
              header("Location: home.php");
              exit;

            } else {
              echo "<div class='message'>
                    <p>Wrong Password</p>
                    </div><br>";

              echo "<a href='login.php'><button class='btn'>Go Back</button></a>";
            }
          } else {
            echo "<div class='message'>
                    <p>Wrong Email or Password</p>
                    </div><br>";

            echo "<a href='login.php'><button class='btn'>Go Back</button></a>";
          }
          $stmt->close();
        }


      } else {


        ?>

        <header>Login</header>
        <hr>
        <form action="#" method="POST">

          <div class="form-box">


            <div class="input-container">
              <i class="fa fa-envelope icon"></i>
              <input class="input-field" type="email" placeholder="Email Address" name="email" required>
            </div>

            <div class="input-container">
              <i class="fa fa-lock icon"></i>
              <input class="input-field password" type="password" placeholder="Password" name="password" required>
              <i class="fa fa-eye toggle icon"></i>
            </div>

            <div class="remember">
              <input type="checkbox" class="check" name="remember_me">
              <label for="remember">Remember me</label>
            </div>

          </div>



          <input type="submit" name="login" id="submit" value="Login" class="button">

          <div class="links">
            Don't have an account? <a href="signup.php">Signup Now</a>
          </div>

        </form>
      </div>
      <?php
      }
      ?>
  </div>
  <script>
    const toggle = document.querySelector(".toggle"),
      input = document.querySelector(".password");
    toggle.addEventListener("click", () => {
      if (input.type === "password") {
        input.type = "text";
        toggle.classList.replace("fa-eye-slash", "fa-eye");
      } else {
        input.type = "password";
      }
    })
  </script>
</body>

</html>