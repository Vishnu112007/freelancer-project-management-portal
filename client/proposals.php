<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "client") {
    header("Location: ../login.php");
    exit();
}

$client_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT 
        proposals.proposal_id,
        proposals.proposal_text,
        proposals.proposed_budget,
        proposals.status,
        proposals.submitted_at,
        projects.project_id,
        projects.title AS project_title,
        users.full_name AS freelancer_name,
        users.email AS freelancer_email
     FROM proposals
     INNER JOIN projects
        ON proposals.project_id = projects.project_id
     INNER JOIN users
        ON proposals.freelancer_id = users.user_id
     WHERE projects.client_id = ?
     ORDER BY proposals.submitted_at DESC"
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

    <title>Proposals - Freelancer Portal</title>

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

        <a href="proposals.php">
            Proposals
        </a>

        <a href="../logout.php">
            Logout
        </a>

    </nav>

</header>


<main>

    <section class="projects-section">

        <h2>Freelancer Proposals</h2>

        <p>
            View proposals submitted for your projects.
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
                            <strong>Freelancer:</strong>
                            <?php
                            echo htmlspecialchars(
                                $proposal["freelancer_name"]
                            );
                            ?>
                        </p>

                        <p>
                            <strong>Email:</strong>
                            <?php
                            echo htmlspecialchars(
                                $proposal["freelancer_email"]
                            );
                            ?>
                        </p>

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
                            <strong>Status:</strong>
                            <?php
                            echo htmlspecialchars(
                                $proposal["status"]
                            );
                            ?>
                        </p>
<?php if ($proposal["status"] == "Pending"): ?>

    <a href="accept_proposal.php?proposal_id=<?php echo $proposal["proposal_id"]; ?>"
       class="btn">

        Accept Freelancer

    </a>

<?php endif; ?>
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
                    No proposals have been submitted yet.
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