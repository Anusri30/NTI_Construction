<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'niti_construction');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['project_id'];
    $step = $_POST['step'];
    $responsible = $_POST['responsible'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    // Insert the log into the database
    $stmt = $conn->prepare("INSERT INTO daily_logs (project_id, step, responsible, status, remarks) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $project_id, $step, $responsible, $status, $remarks);

    if ($stmt->execute()) {
        echo "Log submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
