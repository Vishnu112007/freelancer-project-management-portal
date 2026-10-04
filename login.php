<?php

session_start();
require_once "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] == "client") {

                header("Location: client/dashboard.php");

            } else {

                header("Location: freelancer/dashboard.php");
            }

            exit();

        } else {

            $message = "Invalid password.";

        }

    } else {

        $message = "Account not found.";

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - Freelancer Portal</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header>

    <div class="logo">
        Freelancer Portal
    </div>

    <nav>

        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>

    </nav>

</header>


<main>

    <section class="form-section">

        <div class="form-container">

            <h2>Login</h2>

            <p>Login to your account</p>

            <?php if ($message != ""): ?>

                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <form method="POST"
                  action="login.php"
                  id="loginForm">

                <label>Email</label>

                <input type="email"
                       name="email"
                       placeholder="Enter your email"
                       required>


                <label>Password</label>

                <input type="password"
                       name="password"
                       placeholder="Enter your password"
                       required>


                <button type="submit">
                    Login
                </button>

            </form>


            <p class="form-link">

                Don't have an account?

                <a href="register.php">
                    Register here
                </a>

            </p>

        </div>

    </section>

</main>


<footer>

    <p>
        © 2026 Freelancer Project Management Portal
    </p>

</footer>


<script src="js/validation.js"></script>

</body>

</html>