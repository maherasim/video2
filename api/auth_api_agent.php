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

        // Check if the password matches
        if ($password === $user['password']) { // Direct comparison for plain text

            // Check if the role matches the provided role
            if ($role === $user['role']) {
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
                    'role' => $user['role']
                ]);
            } else {
                // Role mismatch
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Unauthorized role. Expected role does not match.'
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
