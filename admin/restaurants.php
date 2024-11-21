<?php
session_start();
require_once "../config/database.php";

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Variables for success or error messages
$success_message = '';
$error_message = '';

// Handle adding a restaurant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_restaurant'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO restaurants (name, location) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $location);
    $stmt->execute();
    $success_message = "Restaurant added successfully!";
}

// Handle deleting a restaurant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_restaurant'])) {
    $id = $_POST['restaurant_id'];

    // Delete terminals associated with the restaurant
    $stmt = $conn->prepare("DELETE FROM terminals WHERE restaurant_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Delete the restaurant itself
    $stmt = $conn->prepare("DELETE FROM restaurants WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $success_message = "Restaurant deleted successfully!";
}

// Handle updating a restaurant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_restaurant'])) {
    $restaurant_id = $_POST['restaurant_id'];
    $name = $_POST['name'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("UPDATE restaurants SET name = ?, location = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $location, $restaurant_id);
    $stmt->execute();
    $success_message = "Restaurant updated successfully!";
}

// Fetch all restaurants
$result = $conn->query("SELECT * FROM restaurants");
$restaurants = $result->fetch_all(MYSQLI_ASSOC);

// Fetch the details of a restaurant if edit is requested
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_restaurant'])) {
    $restaurant_id = $_GET['restaurant_id'];

    // Fetch restaurant details for editing
    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE id = ?");
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $restaurant_to_edit = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Restaurants</title>
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
    <h1>Manage Restaurants</h1>

    <!-- Success message -->
    <?php if (isset($success_message)): ?>
        <div class="alert-success">
            <?= $success_message; ?>
        </div>
    <?php endif; ?>

    <!-- Add Restaurant Card -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">Add New Restaurant</h4>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Restaurant Name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="text" name="location" class="form-control" placeholder="Location" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_restaurant" class="btn btn-primary">Add Restaurant</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Restaurant Table -->
    <h4>Restaurants List</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($restaurants as $restaurant): ?>
                <tr>
                    <td><?= $restaurant['name']; ?></td>
                    <td><?= $restaurant['location']; ?></td>
                    <td>
    <!-- Delete Restaurant -->
    <form method="POST" style="display:inline;">
        <input type="hidden" name="restaurant_id" value="<?= $restaurant['id']; ?>">
        <button type="submit" name="delete_restaurant" class="btn btn-danger">Delete</button>
    </form>

    <!-- Edit Restaurant -->
    <form method="GET" style="display:inline;">
        <input type="hidden" name="restaurant_id" value="<?= $restaurant['id']; ?>">
        <button type="submit" name="edit_restaurant" class="btn btn-primary">Edit</button>
    </form>
</td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Form (if a restaurant is being edited) -->
    <?php if (isset($restaurant_to_edit)): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Edit Restaurant</h4>
                <form method="POST">
                    <input type="hidden" name="restaurant_id" value="<?= $restaurant_to_edit['id']; ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" name="name" class="form-control" value="<?= $restaurant_to_edit['name']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="text" name="location" class="form-control" value="<?= $restaurant_to_edit['location']; ?>" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="update_restaurant" class="btn btn-primary">Update Restaurant</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
