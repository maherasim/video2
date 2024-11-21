<?php
 
require_once 'database.php';
// Get the username and password from the request
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Set content type as JSON
header('Content-Type: application/json');

// Check if username and password are provided
if (!empty($username) && !empty($password)) {
    // Prepare the query to check if the admin exists
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Check if the password matches
        if (password_verify($password, $admin['password'])) {
            // Successful login
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful'
            ]);
        } else {
            // Incorrect password
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid password'
            ]);
        }
    } else {
        // Admin not found
        echo json_encode([
            'status' => 'error',
            'message' => 'Admin not found'
        ]);
    }
} else {
    // Missing username or password
    echo json_encode([
        'status' => 'error',
        'message' => 'Username and password are required'
    ]);
}
?>
