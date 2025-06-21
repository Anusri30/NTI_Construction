<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'niti_construction');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = intval($_POST['project_id']);
    $step = $conn->real_escape_string($_POST['step']);
    $responsible = $conn->real_escape_string($_POST['responsible']);
    $status = $conn->real_escape_string($_POST['status']);
    $remarks = $conn->real_escape_string($_POST['remarks']);

    $stmt = $conn->prepare("INSERT INTO daily_logs (project_id, step, responsible, status, remarks) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $project_id, $step, $responsible, $status, $remarks);

    if ($stmt->execute()) {
        header("Location: project.php?id=$project_id");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
