<?php
session_start();
require_once '../database.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new terminal
    if (isset($_POST['add_terminal'])) {
        $restaurant_id = $_POST['restaurant_id'];
        $terminal_id = $_POST['terminal_id'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("INSERT INTO terminals (restaurant_id, terminal_id, status) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $restaurant_id, $terminal_id, $status);
        $stmt->execute();
    }

    // Delete terminal
    if (isset($_POST['delete_terminal'])) {
        $id = $_POST['terminal_id'];
        $stmt = $conn->prepare("DELETE FROM terminals WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    // Update terminal
    if (isset($_POST['update_terminal'])) {
        $id = $_POST['terminal_id'];
        $restaurant_id = $_POST['restaurant_id'];
        $terminal_id = $_POST['new_terminal_id'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE terminals SET restaurant_id = ?, terminal_id = ?, status = ? WHERE id = ?");
        $stmt->bind_param("issi", $restaurant_id, $terminal_id, $status, $id);
        $stmt->execute();
    }
}

// Fetch all terminals with restaurant names and status
$query = "
    SELECT t.id, t.terminal_id, r.name AS restaurant_name, t.status
    FROM terminals t 
    JOIN restaurants r ON t.restaurant_id = r.id
";
$result = $conn->query($query);
$terminals = $result->fetch_all(MYSQLI_ASSOC);

// Fetch all restaurants for dropdown
$result = $conn->query("SELECT * FROM restaurants");
$restaurants = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Terminals</title>
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
    <h1>Manage Terminals</h1>

    <!-- Add Terminal Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">Add New Terminal</h4>
            <form method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <select name="restaurant_id" class="form-control" required>
                            <option value="" disabled selected>Select Restaurant</option>
                            <?php foreach ($restaurants as $restaurant): ?>
                                <option value="<?= $restaurant['id'] ?>"><?= $restaurant['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="text" name="terminal_id" class="form-control" placeholder="Terminal ID" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <select name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_terminal" class="btn btn-primary">Add Terminal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Terminal Form -->
    <?php if (isset($_GET['edit'])): ?>
        <?php
        $edit_id = $_GET['edit'];
        $edit_query = "SELECT * FROM terminals WHERE id = ?";
        $stmt = $conn->prepare($edit_query);
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $edit_result = $stmt->get_result();
        $terminal_to_edit = $edit_result->fetch_assoc();
        ?>
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Edit Terminal</h4>
                <form method="POST">
                    <input type="hidden" name="terminal_id" value="<?= $terminal_to_edit['id'] ?>">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <select name="restaurant_id" class="form-control" required>
                                <option value="" disabled>Select Restaurant</option>
                                <?php foreach ($restaurants as $restaurant): ?>
                                    <option value="<?= $restaurant['id'] ?>" <?= $restaurant['id'] == $terminal_to_edit['restaurant_id'] ? 'selected' : '' ?>>
                                        <?= $restaurant['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <input type="text" name="new_terminal_id" class="form-control" value="<?= $terminal_to_edit['terminal_id'] ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <select name="status" class="form-control" required>
                                <option value="active" <?= $terminal_to_edit['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $terminal_to_edit['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="update_terminal" class="btn btn-primary">Update Terminal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Terminals Table -->
    <h4>Existing Terminals</h4>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Terminal ID</th>
            <th>Restaurant</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($terminals as $terminal): ?>
            <tr>
                <td><?= $terminal['id'] ?></td>
                <td><?= $terminal['terminal_id'] ?></td>
                <td><?= $terminal['restaurant_name'] ?></td>
                <td><?= ucfirst($terminal['status']) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="terminal_id" value="<?= $terminal['id'] ?>">
                        <button type="submit" name="delete_terminal" class="btn btn-danger">Delete</button>
                    </form>
                    <a href="?edit=<?= $terminal['id'] ?>" class="btn btn-warning">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
