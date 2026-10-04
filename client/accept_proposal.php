<?php

session_start();
require_once "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "client") {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET["proposal_id"])) {
    header("Location: proposals.php");
    exit();
}

$proposal_id = intval($_GET["proposal_id"]);
$client_id = $_SESSION["user_id"];

$conn->begin_transaction();

try {

    // Get proposal and verify that it belongs to this client's project
    $stmt = $conn->prepare(
        "SELECT
            proposals.proposal_id,
            proposals.project_id,
            proposals.freelancer_id,
            proposals.status
         FROM proposals
         INNER JOIN projects
            ON proposals.project_id = projects.project_id
         WHERE proposals.proposal_id = ?
         AND projects.client_id = ?"
    );

    $stmt->bind_param(
        "ii",
        $proposal_id,
        $client_id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows != 1) {
        throw new Exception("Proposal not found.");
    }

    $proposal = $result->fetch_assoc();

    if ($proposal["status"] != "Pending") {
        throw new Exception("This proposal has already been processed.");
    }

    $project_id = $proposal["project_id"];
    $freelancer_id = $proposal["freelancer_id"];


    // Check whether the project is already assigned
    $check = $conn->prepare(
        "SELECT assignment_id
         FROM project_assignments
         WHERE project_id = ?"
    );

    $check->bind_param("i", $project_id);
    $check->execute();

    $assignment_result = $check->get_result();

    if ($assignment_result->num_rows > 0) {
        throw new Exception("This project is already assigned.");
    }


    // Accept selected proposal
    $accept = $conn->prepare(
        "UPDATE proposals
         SET status = 'Accepted'
         WHERE proposal_id = ?"
    );

    $accept->bind_param("i", $proposal_id);
    $accept->execute();


    // Reject other pending proposals
    $reject = $conn->prepare(
        "UPDATE proposals
         SET status = 'Rejected'
         WHERE project_id = ?
         AND proposal_id != ?
         AND status = 'Pending'"
    );

    $reject->bind_param(
        "ii",
        $project_id,
        $proposal_id
    );

    $reject->execute();


    // Create project assignment
    $assign = $conn->prepare(
        "INSERT INTO project_assignments
        (project_id, freelancer_id)
        VALUES (?, ?)"
    );

    $assign->bind_param(
        "ii",
        $project_id,
        $freelancer_id
    );

    $assign->execute();


    // Update project status
    $update_project = $conn->prepare(
        "UPDATE projects
         SET status = 'In Progress'
         WHERE project_id = ?"
    );

    $update_project->bind_param(
        "i",
        $project_id
    );

    $update_project->execute();


    $conn->commit();

    header("Location: proposals.php");
    exit();

} catch (Exception $e) {

    $conn->rollback();

    echo "<h2>Unable to accept proposal</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo '<p><a href="proposals.php">Back to Proposals</a></p>';
}

?>