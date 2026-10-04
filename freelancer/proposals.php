<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "freelancer") {
    header("Location: ../login.php");
    exit();
}

$freelancer_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT
        proposals.proposal_id,
        proposals.proposal_text,
        proposals.proposed_budget,
        proposals.status,
        proposals.submitted_at,
        projects.title AS project_title,
        projects.deadline
     FROM proposals
     INNER JOIN projects
        ON proposals.project_id = projects.project_id
     WHERE proposals.freelancer_id = ?
     ORDER BY proposals.submitted_at DESC"
);

$stmt->bind_param("i", $freelancer_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Proposals - Freelancer Portal</title>

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

        <h2>My Proposals</h2>

        <p>
            View the proposals you have submitted.
        </p>


        <?php if ($result->num_rows > 0): ?>

            <div class="project-container">

                <?php while ($proposal = $result->fetch_assoc()): ?>

                    <div class="project-card">

                        <h3>
                            <?php
                            echo htmlspecialchars(
                                $proposal["project_title"]
                            );
                            ?>
                        </h3>

                        <p>
                            <strong>Proposal:</strong><br>

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $proposal["proposal_text"]
                                )
                            );
                            ?>
                        </p>

                        <p>
                            <strong>Proposed Budget:</strong>
                            ₹<?php
                            echo number_format(
                                $proposal["proposed_budget"],
                                2
                            );
                            ?>
                        </p>

                        <p>
                            <strong>Project Deadline:</strong>
                            <?php
                            echo htmlspecialchars(
                                $proposal["deadline"]
                            );
                            ?>
                        </p>

                        <p>
                            <strong>Status:</strong>
                            <?php
                            echo htmlspecialchars(
                                $proposal["status"]
                            );
                            ?>
                        </p>

                        <p>
                            <strong>Submitted:</strong>
                            <?php
                            echo htmlspecialchars(
                                $proposal["submitted_at"]
                            );
                            ?>
                        </p>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="no-projects">

                <p>
                    You have not submitted any proposals yet.
                </p>

                <br>

                <a href="projects.php" class="btn">
                    Browse Projects
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