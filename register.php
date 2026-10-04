<?php

require_once "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if ($full_name == "" || $email == "" || $password == "" || $role == "") {

        $message = "Please fill all required fields.";

    } else {

        $check = $conn->prepare(
            "SELECT user_id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Email already registered.";

        } else {

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users
                (full_name, email, password, role)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $full_name,
                $email,
                $hashed_password,
                $role
            );

            if ($stmt->execute()) {

                $message = "Registration successful! You can now login.";

            } else {

                $message = "Registration failed.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register - Freelancer Portal</title>

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

            <h2>Create Account</h2>

            <p>Register as a Client or Freelancer</p>

            <?php if ($message != ""): ?>

                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <form method="POST"
                  action="register.php"
                  id="registerForm">

                <label>Full Name</label>

                <input type="text"
                       name="full_name"
                       placeholder="Enter your full name"
                       required>


                <label>Email</label>

                <input type="email"
                       name="email"
                       placeholder="Enter your email"
                       required>


                <label>Password</label>

                <input type="password"
                       name="password"
                       placeholder="Enter password"
                       required>


                <label>Register As</label>

                <select name="role" required>

                    <option value="">
                        Select Role
                    </option>

                    <option value="client">
                        Client
                    </option>

                    <option value="freelancer">
                        Freelancer
                    </option>

                </select>


                <button type="submit">
                    Register
                </button>

            </form>


            <p class="form-link">
                Already have an account?
                <a href="login.php">Login here</a>
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