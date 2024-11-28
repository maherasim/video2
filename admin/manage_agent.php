<?php
session_start();

require_once '../database.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Variables for success or error messages
$success_message = '';
$error_message = '';

// Handle adding an agent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_agent'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = 'agent'; // Fixed role as agent

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $error_message = "Email already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        $stmt->execute();
        $success_message = "Agent added successfully!";
    }
}

// Handle deleting an agent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_agent'])) {
    $agent_id = $_POST['agent_id'];

    // Delete agent
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $agent_id);
    $stmt->execute();
    $success_message = "Agent deleted successfully!";
}

// Handle updating an agent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_agent'])) {
    $agent_id = $_POST['agent_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $password, $agent_id);
    $stmt->execute();
    $success_message = "Agent updated successfully!";
}

// Fetch all agents
$result = $conn->query("SELECT * FROM users WHERE role = 'agent'");
$agents = $result->fetch_all(MYSQLI_ASSOC);

// Fetch the details of an agent if edit is requested
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_agent'])) {
    $agent_id = $_GET['agent_id'];

    // Fetch agent details for editing
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'agent'");
    $stmt->bind_param("i", $agent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $agent_to_edit = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Agent</title>
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
    <h1>Manage Agents</h1>

    <!-- Success/Error message -->
    <?php if (isset($success_message)): ?>
        <div class="alert-success">
            <?= $success_message; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert-danger">
            <?= $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Add Agent Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">Add New Agent</h4>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Agent Name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Agent Email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_agent" class="btn btn-primary">Add Agent</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Agent Table -->
    <h4>Agent List</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($agents as $agent): ?>
                <tr>
                    <td><?= $agent['name']; ?></td>
                    <td><?= $agent['email']; ?></td>
                    <td>
                        <!-- Delete Agent -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="agent_id" value="<?= $agent['id']; ?>">
                            <button type="submit" name="delete_agent" class="btn btn-danger">Delete</button>
                        </form>

                        <!-- Edit Agent -->
                        <form method="GET" style="display:inline;">
                            <input type="hidden" name="agent_id" value="<?= $agent['id']; ?>">
                            <button type="submit" name="edit_agent" class="btn btn-primary">Edit</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Form (if an agent is being edited) -->
    <?php if (isset($agent_to_edit)): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Edit Agent</h4>
                <form method="POST">
                    <input type="hidden" name="agent_id" value="<?= $agent_to_edit['id']; ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" name="name" class="form-control" value="<?= $agent_to_edit['name']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="email" name="email" class="form-control" value="<?= $agent_to_edit['email']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="password" name="password" class="form-control" placeholder="New Password" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="update_agent" class="btn btn-primary">Update Agent</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
