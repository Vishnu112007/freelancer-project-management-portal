<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "freelancer") {
    header("Location: ../login.php");
    exit();
}

$freelancer_id = $_SESSION["user_id"];

if (!isset($_GET["project_id"])) {
    header("Location: assigned_projects.php");
    exit();
}

$project_id = intval($_GET["project_id"]);

$message = "";

// Verify that this project is assigned to the logged-in freelancer
$stmt = $conn->prepare(
    "SELECT projects.project_id,
            projects.title,
            projects.status
     FROM project_assignments
     INNER JOIN projects
        ON project_assignments.project_id = projects.project_id
     WHERE project_assignments.project_id = ?
     AND project_assignments.freelancer_id = ?"
);

$stmt->bind_param(
    "ii",
    $project_id,
    $freelancer_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Project not found or not assigned to you.");
}

$project = $result->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $status = $_POST["status"];

    if ($status != "In Progress" && $status != "Completed") {

        $message = "Invalid project status.";

    } else {

        $update = $conn->prepare(
            "UPDATE projects
             SET status = ?
             WHERE project_id = ?"
        );

        $update->bind_param(
            "si",
            $status,
            $project_id
        );

        if ($update->execute()) {

            $message = "Project status updated successfully.";

            $project["status"] = $status;

        } else {

            $message = "Failed to update project status.";
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

    <title>Update Status - Freelancer Portal</title>

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

            <h2>Update Project Status</h2>

            <p>
                Update the progress of your assigned project.
            </p>


            <?php if ($message != ""): ?>

                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <div class="project-summary">

                <h3>
                    <?php
                    echo htmlspecialchars(
                        $project["title"]
                    );
                    ?>
                </h3>

                <p>
                    <strong>Current Status:</strong>
                    <?php
                    echo htmlspecialchars(
                        $project["status"]
                    );
                    ?>
                </p>

            </div>


            <form method="POST"
                  action="update_status.php?project_id=<?php echo $project_id; ?>">

                <label>Project Status</label>

                <select name="status" required>

                    <option value="">
                        Select Status
                    </option>

                    <option value="In Progress"
                        <?php
                        if ($project["status"] == "In Progress") {
                            echo "selected";
                        }
                        ?>>
                        In Progress
                    </option>

                    <option value="Completed"
                        <?php
                        if ($project["status"] == "Completed") {
                            echo "selected";
                        }
                        ?>>
                        Completed
                    </option>

                </select>


                <button type="submit">
                    Update Status
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