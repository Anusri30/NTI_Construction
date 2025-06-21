<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$role = $_SESSION['role'] ?? null;
if (!$role) {
    die("Error: User role is not defined. Please check your login process.");
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'niti_construction');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Get project ID from URL
$project_id = $_GET['id'] ?? die("No project ID provided.");

// Fetch logs from daily_logs table
$logs = $conn->query("SELECT * FROM daily_logs WHERE project_id = $project_id ORDER BY log_time DESC");
if (!$logs) {
    die('Error fetching logs: ' . $conn->error);
}

// Handle log submission
if (isset($_POST['submit_log']) && $role === 'site_engineer') {
    // Collect form data
    $step = $_POST['step'];
    $responsible = $_POST['responsible'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];
    $log_time = $_POST['log_time'];

    // Validation: Ensure no field is left empty
    if (empty($step) || empty($responsible) || empty($status) || empty($remarks) || empty($log_time)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        // Insert data into the `daily_logs` table
        $stmt = $conn->prepare("INSERT INTO daily_logs (project_id, step, responsible, status, remarks, log_time) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('isssss', $project_id, $step, $responsible, $status, $remarks, $log_time);

        if ($stmt->execute()) {
            // Redirect to success.php after successful insertion
            header('Location: success.php');
            exit;
        } else {
            echo "<script>alert('Error submitting log: {$stmt->error}');</script>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <title>Project Logs - NITI Construction</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            color: white;
            margin: 0;
            padding: 0;
        }
        h1 {
            font-size: 1.8rem;
            margin: 0;
            color: red;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            font-family: 'Poppins', sans-serif;
            text-align: center;
        }
        table {
            width: 90%;
            margin: 0 auto 100px auto;
            border-collapse: collapse;
            background-color: #222;
        }
        thead {
            background-color: red;
            color: white;
        }
        th, td {
            padding: 15px;
            border: 1px solid #555;
            text-align: center;
        }
        tbody tr:nth-child(even) {
            background-color: #333;
        }
        tbody tr:hover {
            background-color: #444;
        }
        .form-container {
            width: 60%;
            margin: 40px auto 100px auto;
            padding: 20px;
            border: 1px solid red;
            border-radius: 10px;
            background: #222;
        }
        .form-container h2 {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
        .form-container label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
        }
        .form-container input,
        .form-container select,
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #555;
            background: #333;
            color: white;
            font-size: 1rem;
        }
        .form-container button {
            width: 100%;
            padding: 15px;
            font-size: 1rem;
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: rgb(154, 54, 21);
        }
    </style>
</head>
<body>
    <?php if ($role === 'chief_engineer'): ?>
        <div class="header">
            <h1>Daily Coordination Log</h1>
        </div>
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Step</th>
                        <th>Responsible</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = $logs->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['step']) ?></td>
                            <td><?= htmlspecialchars($log['responsible']) ?></td>
                            <td><?= htmlspecialchars($log['status']) ?></td>
                            <td><?= htmlspecialchars($log['remarks']) ?></td>
                            <td><?= htmlspecialchars($log['log_time']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($role === 'site_engineer'): ?>
        <div class="form-container">
            <h1>Daily Coordination Log</h1>
            <h2>Submit New Log</h2>
            <form method="POST">
                <label for="step">Step:</label>
                <input type="text" id="step" name="step" required>

                <label for="responsible">Responsible:</label>
                <input type="text" id="responsible" name="responsible" required>

                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="✓">✓</option>
                    <option value="✗">✗</option>
                </select>

                <label for="remarks">Remarks:</label>
                <textarea id="remarks" name="remarks" rows="4" required></textarea>

                <label for="log_time">Date & Time:</label>
                <input type="datetime-local" id="log_time" name="log_time" required>

                <button type="submit" name="submit_log">Submit Log</button>
            </form>
        </div>
    <?php else: ?>
        <div style="text-align: center; margin: 20px;">
            <h2>You do not have access to this page.</h2>
        </div>
    <?php endif; ?>
</body>
</html>
