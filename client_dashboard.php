<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: index.php');
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'niti_construction');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Fetch projects assigned to the logged-in client
$client_id = $_SESSION['user_id'];
$projects = $conn->query("SELECT * FROM projects WHERE client_id = $client_id");
if (!$projects) {
    die('Error fetching projects: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - NITI Construction</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #111;
            color: #fff;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            font-size: 36px;
            margin-bottom: 10px;
        }
        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
        }
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .project-card {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.2s ease;
        }
        .project-card:hover {
            transform: translateY(-5px);
        }
        .project-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #ff9c00;
        }
        .project-card p {
            margin: 10px 0;
            color: #ccc;
        }
        .project-card a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #ff3c00;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        .project-card a:hover {
            background-color: #e63900;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Client Dashboard</h1>
        <h2>Your Projects</h2>

        <div class="projects-grid">
            <?php if ($projects->num_rows > 0): ?>
                <?php while ($project = $projects->fetch_assoc()): ?>
                    <div class="project-card">
                        <h3><?= htmlspecialchars($project['name']) ?></h3>
                        <p>City: <?= isset($project['city']) ? htmlspecialchars($project['city']) : 'Not specified' ?></p>
                        <p>Description: <?= isset($project['description']) ? htmlspecialchars($project['description']) : 'Not specified' ?></p>
                        <a href="project_progress.php?project_id=<?= $project['id'] ?>">View Progress</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No projects assigned to you yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
