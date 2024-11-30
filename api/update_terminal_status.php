<?php 
require_once 'database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['terminal_id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid input. 'terminal_id' and 'status' are required."]);
            exit;
        }

        $terminal_id = $data['terminal_id'];
        $status = $data['status'];

        $stmt = $conn->prepare("UPDATE terminals SET status = ? WHERE terminal_id = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(["error" => "Database error: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("ss", $status, $terminal_id);
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(["error" => "Database error: " . $stmt->error]);
            exit;
        }

        if ($stmt->affected_rows > 0) {
            // WebSocket broadcast
            $websocketClient = new WebSocket\Client("ws://84.247.187.38:9001/status");
            $websocketClient->send(json_encode([
                'action' => 'terminal_status',
                'terminal_id' => $terminal_id,
                'status' => $status
            ]));
            $websocketClient->close();

            echo json_encode(["message" => "Terminal status updated successfully."]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No terminal found with terminal_id: $terminal_id"]);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Unexpected error: " . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method. Use POST."]);
}

?>