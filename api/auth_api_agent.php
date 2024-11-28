<?php
require_once 'database.php';

// Set content type as JSON
header('Content-Type: application/json');

// Parse JSON body if sent
$input = json_decode(file_get_contents('php://input'), true);

// Extract email, password, and role
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';
$role = $input['role'] ?? '';

// Check if email, password, and role are provided
if (!empty($email) && !empty($password) && !empty($role)) {
    // Prepare the query to check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Use password_verify to check if the entered password matches the hashed password
        if (password_verify($password, $user['password'])) {

            // Check if the role matches the requested role
            if ($user['role'] === $role) {
                // Generate session token
                $sessionToken = bin2hex(random_bytes(32));

                // Save session token in the database
                $updateStmt = $conn->prepare("UPDATE users SET session_token = ?, check_in_time = NOW() WHERE id = ?");
                $updateStmt->bind_param("si", $sessionToken, $user['id']);
                $updateStmt->execute();

                // Successful login
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'token' => $sessionToken,
                    'user' => [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ]);
            } else {
                // Role mismatch
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Unauthorized role. Expected role: ' . $role
                ]);
            }
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
    // Missing email, password, or role
    echo json_encode([
        'status' => 'error',
        'message' => 'Email, password, and role are required'
    ]);
}
?>
