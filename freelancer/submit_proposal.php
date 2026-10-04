<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "freelancer") {
    header("Location: ../login.php");
    exit();
}

$freelancer_id = $_SESSION["user_id"];

if (!isset($_GET["project_id"])) {
    header("Location: projects.php");
    exit();
}

$project_id = intval($_GET["project_id"]);

$stmt = $conn->prepare(
    "SELECT * FROM projects
     WHERE project_id = ? AND status = 'Open'"
);

$stmt->bind_param("i", $project_id);
$stmt->execute();

$project_result = $stmt->get_result();

if ($project_result->num_rows != 1) {
    die("Project not found or no longer available.");
}

$project = $project_result->fetch_assoc();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $proposal_text = trim($_POST["proposal_text"]);
    $proposed_budget = trim($_POST["proposed_budget"]);

    if ($proposal_text == "" || $proposed_budget == "") {

        $message = "Please fill all required fields.";

    } else {

        $check = $conn->prepare(
            "SELECT proposal_id
             FROM proposals
             WHERE project_id = ?
             AND freelancer_id = ?"
        );

        $check->bind_param(
            "ii",
            $project_id,
            $freelancer_id
        );

        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $message = "You have already submitted a proposal for this project.";

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO proposals
                (project_id, freelancer_id, proposal_text, proposed_budget)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "iisd",
                $project_id,
                $freelancer_id,
                $proposal_text,
                $proposed_budget
            );

            if ($stmt->execute()) {

                $message = "Proposal submitted successfully!";

            } else {

                $message = "Failed to submit proposal.";
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

    <title>Submit Proposal - Freelancer Portal</title>

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

            <h2>Submit Proposal</h2>

            <p>
                Apply for the selected project.
            </p>


            <?php if ($message != ""): ?>

                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <div class="project-summary">

                <h3>
                    <?php
                    echo htmlspecialchars($project["title"]);
                    ?>
                </h3>

                <p>
                    <strong>Project Description:</strong><br>

                    <?php
                    echo nl2br(
                        htmlspecialchars(
                            $project["description"]
                        )
                    );
                    ?>
                </p>

                <p>
                    <strong>Client Budget:</strong>
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

            </div>


            <form method="POST"
                  action="submit_proposal.php?project_id=<?php echo $project_id; ?>">

                <label>Proposal</label>

                <textarea name="proposal_text"
                          rows="6"
                          placeholder="Explain why you are suitable for this project..."
                          required></textarea>


                <label>Proposed Budget</label>

                <input type="number"
                       name="proposed_budget"
                       min="1"
                       step="0.01"
                       placeholder="Enter your proposed budget"
                       required>


                <button type="submit">
                    Submit Proposal
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