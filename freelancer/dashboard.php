<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "freelancer") {
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

    <title>Freelancer Dashboard - Freelancer Portal</title>

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

        <a href="projects.php">
            Browse Projects
        </a>

        <a href="proposals.php">
            My Proposals
        </a>

        <a href="assigned_projects.php">
            Assigned Projects
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
                Welcome,
                <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!
            </h2>

            <p>
                You are logged in as a Freelancer.
            </p>

            <br>

            <div class="dashboard-links">

                <a href="projects.php" class="btn">
                    Browse Projects
                </a>

                <br><br>

                <a href="proposals.php" class="btn secondary">
                    My Proposals
                </a>

                <br><br>

                <a href="assigned_projects.php" class="btn secondary">
                    Assigned Projects
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