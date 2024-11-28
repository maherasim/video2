<?php
session_start();

require_once '../database.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch all agents' data (name, email, check_in_time, check_out_time)
$result = $conn->query("SELECT name, email, check_in_time, check_out_time FROM users WHERE check_in_time IS NOT NULL AND check_out_time IS NOT NULL");
$agents = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Agent History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-top: 50px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .form-control {
            border-radius: 5px;
            padding: 10px;
        }

        .btn-primary {
            background-color: #0072ff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #005bb5;
        }

        .table th, .table td {
            text-align: center;
        }

        .table td button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .table td button:hover {
            background-color: #d32f2f;
        }

        .alert-success {
            color: white;
            background-color: #28a745;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            color: white;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Display Agent History</h1>

    <!-- Success message -->
    <?php if (isset($success_message)): ?>
        <div class="alert-success">
            <?= $success_message; ?>
        </div>
    <?php endif; ?>

    <h4>Agent History List</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($agents as $agent): ?>
                <?php
                // Calculate the duration including seconds
                $check_in = new DateTime($agent['check_in_time']);
                $check_out = new DateTime($agent['check_out_time']);
                $interval = $check_in->diff($check_out);
                
                // Get the duration in hours, minutes, and seconds
                $duration = $interval->format('%h hours %i minutes %s seconds');
                ?>
                <tr>
                    <td><?= htmlspecialchars($agent['name']); ?></td> <!-- Display agent's name -->
                    <td><?= htmlspecialchars($agent['email']); ?></td>
                    <td><?= htmlspecialchars($agent['check_in_time']); ?></td>
                    <td><?= htmlspecialchars($agent['check_out_time']); ?></td>
                    <td><?= $duration; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
