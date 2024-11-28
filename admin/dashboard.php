<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJxg6gkH1pH9gJfM34v60rZ5z07bFZntgO4LhzF4buHsP7QzZg6tUHzIH6FfH" crossorigin="anonymous">
    <style>
        body {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .dashboard-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-top: 50px;
            width: 100%;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .dashboard-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .dashboard-container ul {
            list-style-type: none;
            padding: 0;
        }

        .dashboard-container ul li {
            margin: 15px 0;
            text-align: center;
        }

        .dashboard-container ul li a {
            display: inline-block;
            text-decoration: none;
            background-color: #0072ff;
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 18px;
            width: 200px;
            transition: background-color 0.3s ease;
        }

        .dashboard-container ul li a:hover {
            background-color: #005bb5;
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

<div class="dashboard-container">
    <h1>Welcome to Admin Dashboard</h1>

    <ul>
        <li><a href="restaurants.php">Manage Restaurants</a></li>
        <li><a href="terminals.php">Manage Terminals</a></li>
        <li><a href="agent_history.php">Manage Agent History</a></li>
        <li><a href="manage_agent.php">Manage Agent </a></li>
        <li><a href="manager_manage.php">Manage Manager </a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="footer">
    <p>&copy; 2024 Restaurant Management. All Rights Reserved.</p>
</div>

</body>
</html>
