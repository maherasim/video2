<?php
 
require_once 'database.php';
// Set content type as JSON
header('Content-Type: application/json');

// SQL query to fetch terminals with restaurant names
$query = "
    SELECT t.id, t.terminal_id, r.name AS restaurant_name 
    FROM terminals t
    JOIN restaurants r ON t.restaurant_id = r.id
";
$result = $conn->query($query);

// Check if any terminals were found
if ($result->num_rows > 0) {
    $terminals = [];

    // Fetch the data and store it in the array
    while ($row = $result->fetch_assoc()) {
        $terminals[] = $row;
    }

    // Return the terminal data as a JSON response
    echo json_encode([
        'status' => 'success',
        'data' => $terminals
    ]);
} else {
    // Return an empty array if no terminals are found
    echo json_encode([
        'status' => 'error',
        'message' => 'No terminals found'
    ]);
}
?>
