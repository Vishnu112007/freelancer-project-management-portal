<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "client") {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Client Dashboard - Freelancer Portal</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <div class="logo">
        Freelancer Portal
    </div>

    <nav>

        <a href="dashboard.php">Dashboard</a>

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

            <h2>
                Welcome, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!
            </h2>

            <p>
                You are logged in as a Client.
            </p>

            <br>

            <div class="dashboard-links">

                <a href="post_project.php" class="btn">
                    Post a Project
                </a>

                <br><br>

                <a href="projects.php" class="btn secondary">
                    View My Projects
                </a>
                <br><br>

<a href="proposals.php" class="btn secondary">
    View Proposals
</a>

            </div>

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