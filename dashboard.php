<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'niti_construction');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$projects = $conn->query("SELECT * FROM projects");
if (!$projects) {
    die('Error fetching projects: ' . $conn->error);
}

// Function to fetch weather using OpenWeatherMap API
function getWeather($city) {
    $apiKey = "b0d011c942dfc7c45b6208392d547b55";
    $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric";
    $response = @file_get_contents($apiUrl);
    if ($response === FALSE) {
        return "Weather data unavailable";
    }
    $data = json_decode($response, true);
    if ($data && $data['cod'] === 200) {
        return "{$data['main']['temp']}°C, {$data['weather'][0]['description']}";
    } else {
        return "Weather data unavailable";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NITI Construction</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #111;
            color: #fff;
        }
        .header {
            text-align: center;
            margin: 20px;
        }
        .header h1 {
            font-size: 36px;
            font-weight: bold;
            color: #ff3c00;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
            margin: 0;
        }
        .header h2 {
            font-size: 28px;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(90deg, #ff3c00, #ff9c00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(255, 156, 0, 0.4);
            margin: 10px 0;
        }
        .projects {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .project-card {
            border: 1px solid #333;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            background: #1e1e1e;
        }
        .project-card img {
            width: 100%;
            height: auto;
        }
        .project-card .content {
            padding: 15px;
            text-align: center;
        }
        .project-card h3 {
            margin: 0;
            font-size: 20px;
            color: #ff3c00;
        }
        .project-card p {
            margin: 5px 0;
            color: #aaa;
            font-size: 14px;
        }
        .project-card a {
            text-decoration: none;
            color: #ff3c00;
            font-weight: bold;
            font-size: 14px;
        }
        .project-card a:hover {
            color: #ff9c00;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dashboard</h1>
        <h2>Ongoing Projects</h2>
    </div>
    <div class="projects">
        <?php while ($project = $projects->fetch_assoc()): ?>
            <div class="project-card">
                <!-- Dynamically display the image -->
                <?php if ($project['id'] == 1): ?>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvmf0nbjuj3meGULc_cYrhPTOgrgHuYOLmsg&s" alt="Residential Building Image">
                <?php elseif ($project['id'] == 2): ?>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQg3qm9o2FaW5TQkABMHDdmpG2AMRJ0a2HVU8Igke7g1Mf-wHmp30z_NtxYQYN1ccRbMrU&usqp=CAU" alt="Commercial Complex Image">
                <?php elseif ($project['id'] == 3): ?>
                    <img src="https://i0.wp.com/thelocalnews.news/wp-content/uploads/2025/05/Swampscott-Front-Entry-4-1170x762-1-e1746215651588.jpeg?resize=696%2C453&ssl=1" alt="School Building Image">
                <?php elseif ($project['id'] == 4): ?>
                    <img src="https://previews.123rf.com/images/goce/goce1506/goce150600003/40801771-new-shopping-mall-construction-site.jpg" alt="Shopping Mall Image">
                <?php elseif ($project['id'] == 5): ?>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_vkosgnZgTkwjoNSYSMBMPX1uPOqlPqrstg&s" alt="Hospital Project Image">
                <?php else: ?>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvmf0nbjuj3meGULc_cYrhPTOgrgHuYOLmsg&s" alt="Default Project Image">
                <?php endif; ?>
                <!-- Project name -->
                <div class="content">
                    <h3><?= $project['name'] ?></h3>
                    <p>
                        <?php 
                        echo "Weather in {$project['city']}: " . getWeather($project['city']); 
                        ?>
                    </p>
                    <a href="project.php?id=<?= $project['id'] ?>">View Project</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
