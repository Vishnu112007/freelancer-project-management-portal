<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "client") {
    header("Location: ../login.php");
    exit();
}

$client_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT * FROM projects
     WHERE client_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param("i", $client_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Projects - Freelancer Portal</title>

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

    <section class="projects-section">

        <h2>My Projects</h2>

        <p>
            Projects posted by you
        </p>


        <?php if ($result->num_rows > 0): ?>

            <div class="project-container">

                <?php while ($project = $result->fetch_assoc()): ?>

                    <div class="project-card">

                        <h3>
                            <?php
                            echo htmlspecialchars($project["title"]);
                            ?>
                        </h3>

                        <p>
                            <strong>Description:</strong><br>

                            <?php
                            echo nl2br(
                                htmlspecialchars($project["description"])
                            );
                            ?>
                        </p>

                        <p>
                            <strong>Budget:</strong>
                            ₹<?php
                            echo number_format(
                                $project["budget"],
                                2
                            );
                            ?>
                        </p>

                        <p>
                            <strong>Deadline:</strong>
                            <?php
                            echo htmlspecialchars(
                                $project["deadline"]
                            );
                            ?>
                        </p>

                        <p>
                            <strong>Status:</strong>

                            <?php
                            echo htmlspecialchars(
                                $project["status"]
                            );
                            ?>
                        </p>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="no-projects">

                <p>
                    You have not posted any projects yet.
                </p>

                <br>

                <a href="post_project.php" class="btn">
                    Post Your First Project
                </a>

            </div>

        <?php endif; ?>

    </section>

</main>


<footer>

    <p>
        © 2026 Freelancer Project Management Portal
    </p>

</footer>

</body>

</html>