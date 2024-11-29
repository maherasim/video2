<?php
//require_once 'database.php';
require_once 'database.php';

header('Content-Type: application/json');

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
            $error = "Database error: Failed to prepare statement. " . $conn->error;
            echo json_encode(["error" => $error]);
            error_log($error); // Log error
            exit;
        }

        $stmt->bind_param("ss", $status, $terminal_id);
        if (!$stmt->execute()) {
            http_response_code(500);
            $error = "Database error: Failed to execute update. " . $stmt->error;
            echo json_encode(["error" => $error]);
            error_log($error); // Log error
            exit;
        }

        if ($stmt->affected_rows > 0) {
            echo json_encode(["message" => "Terminal status updated successfully."]);
        } else {
            http_response_code(404);
            $error = "No terminal found with terminal_id: $terminal_id";
            echo json_encode(["error" => $error]);
            error_log($error); // Log error
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        http_response_code(500);
        $error = "Unexpected error: " . $e->getMessage();
        echo json_encode(["error" => $error]);
        error_log($error); // Log exception
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method. Use POST."]);
}
?>
