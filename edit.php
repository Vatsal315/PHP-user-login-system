<?php
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
session_start();

include("connection.php");

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style1.css">
</head>

<body>

    <div class="container">
        <div class="form-box box">

            <?php

            if (isset($_POST['update'])) {
                $username = $_POST['username'];
                $email = trim($_POST['email']);
                $password = $_POST['password'];

                $id = $_SESSION['id'];

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "<div class='message'>
                <p>Invalid email address</p>
                </div><br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
                } else {
                    if ($password !== null && $password !== '') {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                        $stmt->bind_param("sssi", $username, $email, $hash, $id);
                    } else {
                        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                        $stmt->bind_param("ssi", $username, $email, $id);
                    }

                    $edit_query = $stmt->execute();
                    $stmt->close();
                }

                if ($edit_query) {
                    echo "<div class='message'>
                <p>Profile Updated!</p>
                </div><br>";
                    echo "<a href='home.php'><button class='btn'>Go Home</button></a>";
                }
            } else {

                $id = $_SESSION['id'];
                $stmt = $conn->prepare("SELECT username, email, id FROM users WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $query = $stmt->get_result();

                while ($result = $query->fetch_assoc()) {
                    $res_username = $result['username'];
                    $res_email = $result['email'];
                    $res_id = $result['id'];
                }
                $stmt->close();

                ?>

                <header>Change Profile</header>
                <form action="#" method="POST" enctype="multipart/form-data">

                    <div class="form-box">

                        <div class="input-container">
                            <i class="fa fa-user icon"></i>
                            <input class="input-field" type="text" placeholder="Username" name="username"
                                value="<?php echo $res_username; ?>" required>
                        </div>

                        <div class="input-container">
                            <i class="fa fa-envelope icon"></i>
                            <input class="input-field" type="email" placeholder="Email Address" name="email"
                                value="<?php echo $res_email; ?>" required>
                        </div>

                        <div class="input-container">
                            <i class="fa fa-lock icon"></i>
                            <input class="input-field password" type="password" placeholder="New Password (leave blank to keep same)" name="password">
                            <i class="fa fa-eye toggle icon"></i>
                        </div>

                    </div>


                    <div class="field">
                        <input type="submit" name="update" id="submit" value="Update" class="btn">
                    </div>


                </form>
            </div>
        <?php } ?>
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