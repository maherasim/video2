<?php
require_once '../database.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate input
    if (!isset($data['terminal_id']) || !isset($data['status'])) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid input. 'terminal_id' and 'status' are required."]);
        exit;
    }

    $terminal_id = $data['terminal_id'];
    $status = $data['status'];

    // Update terminal status
    $stmt = $conn->prepare("UPDATE terminals SET status = ? WHERE terminal_id = ?");
    $stmt->bind_param("ss", $status, $terminal_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["message" => "Terminal status updated successfully."]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Terminal not found with the provided terminal_id."]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update terminal status."]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method. Use POST."]);
}
?>
