<?php
require_once 'database.php';

// Get session token from headers
$headers = getallheaders();
$sessionToken = $headers['Authorization'] ?? '';

// Set content type as JSON
header('Content-Type: application/json');

if (!empty($sessionToken)) {
    // Prepare the query to invalidate the session token
    $stmt = $conn->prepare("UPDATE users SET session_token = NULL WHERE session_token = ?");
    $stmt->bind_param("s", $sessionToken);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Successful logout
        echo json_encode([
            'status' => 'success',
            'message' => 'Logout successful'
        ]);
    } else {
        // Invalid session token
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid session token'
        ]);
    }
} else {
    // Missing session token
    echo json_encode([
        'status' => 'error',
        'message' => 'Session token is required'
    ]);
}
?>
