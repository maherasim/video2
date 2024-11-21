<?php
require_once 'database.php';

// Set content type as JSON
header('Content-Type: application/json');

// Parse JSON body if sent
$input = json_decode(file_get_contents('php://input'), true);

// Extract username and password
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

// Check if username and password are provided
if (!empty($username) && !empty($password)) {
    // Prepare the query to check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the password matches
        if ($password === $user['password']) { // Direct comparison for plain text
            // Generate session token
            $sessionToken = bin2hex(random_bytes(32));

            // Save session token in the database
            $updateStmt = $conn->prepare("UPDATE users SET session_token = ? WHERE id = ?");
            $updateStmt->bind_param("si", $sessionToken, $user['id']);
            $updateStmt->execute();

            // Successful login
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $sessionToken,
                'role' => $user['role'] // 'agent' or 'manager'
            ]);
        } else {
            // Incorrect password
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid password'
            ]);
        }
    } else {
        // User not found
        echo json_encode([
            'status' => 'error',
            'message' => 'User not found'
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
