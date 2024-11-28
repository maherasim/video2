<?php
require_once 'database.php';

// Set content type as JSON
header('Content-Type: application/json');

// Parse JSON body if sent
$input = json_decode(file_get_contents('php://input'), true);

// Extract email and session token
$email = $input['email'] ?? '';
$sessionToken = $input['session_token'] ?? '';

// Check if email and session token are provided
if (!empty($email) && !empty($sessionToken)) {
    // Prepare the query to check if the user exists and the session token matches
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND session_token = ?");
    $stmt->bind_param("ss", $email, $sessionToken);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Update the session token to NULL, effectively logging the user out
        $updateStmt = $conn->prepare("UPDATE users SET session_token = NULL, check_out_time = NOW() WHERE id = ?");
        $updateStmt->bind_param("i", $user['id']);
        $updateStmt->execute();

        // Successful logout
        echo json_encode([
            'status' => 'success',
            'message' => 'Logout successfully'
        ]);
    } else {
        // Invalid session token or user not found
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid session token or user not found'
        ]);
    }
} else {
    // Missing email or session token
    echo json_encode([
        'status' => 'error',
        'message' => 'Email and session token are required'
    ]);
}
?>
