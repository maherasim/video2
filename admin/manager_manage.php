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

// Handle adding a manager
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_manager'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = 'manager'; // Fixed role as manager

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
        $success_message = "Manager added successfully!";
    }
}

// Handle deleting a manager
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_manager'])) {
    $manager_id = $_POST['manager_id'];

    // Delete manager
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $manager_id);
    $stmt->execute();
    $success_message = "Manager deleted successfully!";
}

// Handle updating a manager
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_manager'])) {
    $manager_id = $_POST['manager_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $password, $manager_id);
    $stmt->execute();
    $success_message = "Manager updated successfully!";
}

// Fetch all managers
$result = $conn->query("SELECT * FROM users WHERE role = 'manager'");
$managers = $result->fetch_all(MYSQLI_ASSOC);

// Fetch the details of a manager if edit is requested
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_manager'])) {
    $manager_id = $_GET['manager_id'];

    // Fetch manager details for editing
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'manager'");
    $stmt->bind_param("i", $manager_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $manager_to_edit = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Manager</title>
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
    <h1>Manage Managers</h1>

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

    <!-- Add Manager Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">Add New Manager</h4>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Manager Name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Manager Email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_manager" class="btn btn-primary">Add Manager</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Manager Table -->
    <h4>Manager List</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($managers as $manager): ?>
                <tr>
                    <td><?= $manager['name']; ?></td>
                    <td><?= $manager['email']; ?></td>
                    <td>
                        <!-- Delete Manager -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="manager_id" value="<?= $manager['id']; ?>">
                            <button type="submit" name="delete_manager" class="btn btn-danger">Delete</button>
                        </form>

                        <!-- Edit Manager -->
                        <form method="GET" style="display:inline;">
                            <input type="hidden" name="manager_id" value="<?= $manager['id']; ?>">
                            <button type="submit" name="edit_manager" class="btn btn-primary">Edit</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Form (if a manager is being edited) -->
    <?php if (isset($manager_to_edit)): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Edit Manager</h4>
                <form method="POST">
                    <input type="hidden" name="manager_id" value="<?= $manager_to_edit['id']; ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" name="name" class="form-control" value="<?= $manager_to_edit['name']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="email" name="email" class="form-control" value="<?= $manager_to_edit['email']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="password" name="password" class="form-control" placeholder="New Password" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="update_manager" class="btn btn-primary">Update Manager</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
