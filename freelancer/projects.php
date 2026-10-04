<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "freelancer") {
    header("Location: ../login.php");
    exit();
}

$stmt = $conn->prepare(
    "SELECT
        projects.project_id,
        projects.title,
        projects.description,
        projects.budget,
        projects.deadline,
        projects.status,
        users.full_name AS client_name
     FROM projects
     INNER JOIN users
        ON projects.client_id = users.user_id
     WHERE projects.status = 'Open'
     ORDER BY projects.created_at DESC"
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Browse Projects - Freelancer Portal</title>

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

    <section class="projects-section">

        <h2>Available Projects</h2>

        <p>
            Browse projects posted by clients.
        </p>


        <?php if ($result->num_rows > 0): ?>

            <div class="project-container">

                <?php while ($project = $result->fetch_assoc()): ?>

                    <div class="project-card">

                        <h3>
                            <?php
                            echo htmlspecialchars(
                                $project["title"]
                            );
                            ?>
                        </h3>

                        <p>
                            <strong>Client:</strong>
                            <?php
                            echo htmlspecialchars(
                                $project["client_name"]
                            );
                            ?>
                        </p>

                        <p>
                            <strong>Description:</strong><br>

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $project["description"]
                                )
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

                        <a href="submit_proposal.php?project_id=<?php echo $project["project_id"]; ?>"
                           class="btn">

                            Submit Proposal

                        </a>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="no-projects">

                <p>
                    No open projects are currently available.
                </p>

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