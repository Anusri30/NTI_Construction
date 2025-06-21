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

// Get project ID from URL
$project_id = isset($_GET['project_id']) ? (int) $_GET['project_id'] : 0;
if ($project_id === 0) {
    die('Invalid project ID');
}

// Get selected date
$selected_date = isset($_GET['date']) ? $_GET['date'] : '';
$where_clause = $selected_date ? "AND update_date = '$selected_date'" : '';

// Fetch updates for the selected date (or all dates if no date is selected)
$updates_query = "SELECT * FROM project_updates WHERE project_id = $project_id $where_clause ORDER BY update_date ASC";
$updates = $conn->query($updates_query);
if (!$updates) {
    die('Error fetching updates: ' . $conn->error);
}

// Prepare graph data for the selected date (or all dates)
$graph_query = "SELECT update_date, progress FROM project_updates WHERE project_id = $project_id $where_clause ORDER BY update_date ASC";
$graph_data = $conn->query($graph_query);
if (!$graph_data) {
    die('Error fetching graph data: ' . $conn->error);
}

// Prepare graph points
$graph_points = [];
while ($row = $graph_data->fetch_assoc()) {
    $graph_points[] = ['date' => $row['update_date'], 'progress' => $row['progress']];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Progress - NITI Construction</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: black;
            color: white;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h1, h2 {
            text-align: center;
            color: red;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        input, button {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid red;
            border-radius: 5px;
            background: #333;
            color: white;
        }
        button {
            background: red;
            cursor: pointer;
        }
        button:hover {
            background: darkred;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #222;
            color: white;
        }
        table, th, td {
            border: 1px solid red;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background: red;
        }
        tbody tr:nth-child(even) {
            background: #333;
        }
        tbody tr:hover {
            background: #444;
        }
        canvas {
            margin-top: 20px;
            background: #222;
            border: 1px solid red;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Project Progress</h1>
        <h2>Updates for Project ID: <?= $project_id ?></h2>

        <!-- Date Filter Form -->
        <form method="get" action="project_progress.php">
            <input type="hidden" name="project_id" value="<?= $project_id ?>">
            <label for="date">Select Date:</label>
            <input type="date" name="date" id="date" value="<?= htmlspecialchars($selected_date) ?>">
            <button type="submit">Filter</button>
        </form>

        <!-- Updates Table -->
        <?php if ($updates->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Work Done</th>
                        <th>Progress (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($update = $updates->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($update['update_date']) ?></td>
                            <td><?= htmlspecialchars($update['work_done']) ?></td>
                            <td><?= htmlspecialchars($update['progress']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No updates found for the selected date.</p>
        <?php endif; ?>

        <!-- Progress Graph -->
        <canvas id="progressChart"></canvas>
    </div>

    <script>
        const graphData = <?= json_encode($graph_points) ?>;

        // Extract labels (dates) and data (progress)
        const labels = graphData.map(data => data.date);
        const progress = graphData.map(data => data.progress);

        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Progress (%)',
                    data: progress,
                    backgroundColor: 'rgba(173, 216, 230, 0.4)', // Light blue background
                    borderColor: 'rgba(135, 206, 250, 0.8)', // Light sky blue border
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: 'white' // Legend text color
                        }
                    },
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date',
                            color: 'lightgray',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            color: 'white' // X-axis label color
                        },
                        grid: {
                            color: 'white' // X-axis grid color
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Progress (%)',
                            color: 'lightgray',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            color: 'white' // Y-axis label color
                        },
                        grid: {
                            color: 'white' // Y-axis grid color
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
