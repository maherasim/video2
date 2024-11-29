<?php
require_once '../database.php';

header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(["error" => "Database error: Failed to prepare statement."]);
            error_log("Statement preparation failed: " . $conn->error); // Log error
            exit;
        }

        $stmt->bind_param("ss", $status, $terminal_id);
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to execute update."]);
            error_log("Statement execution failed: " . $stmt->error); // Log error
            exit;
        }

        if ($stmt->affected_rows > 0) {
            echo json_encode(["message" => "Terminal status updated successfully."]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Terminal not found with the provided terminal_id."]);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "An unexpected error occurred."]);
        error_log("Exception: " . $e->getMessage()); // Log exception
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method. Use POST."]);
}
?>
