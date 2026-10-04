<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "client") {
    header("Location: ../login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $budget = trim($_POST["budget"]);
    $deadline = $_POST["deadline"];

    if ($title == "" || $description == "" || $budget == "" || $deadline == "") {

        $message = "Please fill all required fields.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO projects
            (client_id, title, description, budget, deadline)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "issds",
            $_SESSION["user_id"],
            $title,
            $description,
            $budget,
            $deadline
        );

        if ($stmt->execute()) {

            $message = "Project posted successfully!";

        } else {

            $message = "Failed to post project.";
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

    <title>Post Project - Freelancer Portal</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <div class="logo">
        Freelancer Portal
    </div>

    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="post_project.php">
            Post Project
        </a>

        <a href="projects.php">
            My Projects
        </a>

        <a href="../logout.php">
            Logout
        </a>

    </nav>

</header>


<main>

    <section class="form-section">

        <div class="form-container">

            <h2>Post a Project</h2>

            <p>
                Enter your project requirements
            </p>


            <?php if ($message != ""): ?>

                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <form method="POST"
                  action="post_project.php">

                <label>Project Title</label>

                <input type="text"
                       name="title"
                       placeholder="Enter project title"
                       required>


                <label>Project Description</label>

                <textarea name="description"
                          placeholder="Describe your project"
                          rows="5"
                          required></textarea>


                <label>Budget</label>

                <input type="number"
                       name="budget"
                       placeholder="Enter budget"
                       min="1"
                       step="0.01"
                       required>


                <label>Deadline</label>

                <input type="date"
                       name="deadline"
                       required>


                <button type="submit">
                    Post Project
                </button>

            </form>

        </div>

    </section>

</main>


<footer>

    <p>
        © 2026 Freelancer Project Management Portal
    </p>

</footer>

</body>

</html>