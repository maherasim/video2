<?php
// Include database configuration
require_once 'database.php';

// Set content type as JSON
header('Content-Type: application/json');

// SQL query to fetch all restaurants
$query = "SELECT id, name FROM restaurants";
$result = $conn->query($query);

// Check if any restaurants were found
if ($result->num_rows > 0) {
    $restaurants = [];

    // Fetch the data and store it in the array
    while ($row = $result->fetch_assoc()) {
        $restaurants[] = $row;
    }

    // Return the restaurant data as a JSON response
    echo json_encode([
        'status' => 'success',
        'data' => $restaurants
    ]);
} else {
    // Return an empty array if no restaurants are found
    echo json_encode([
        'status' => 'error',
        'message' => 'No restaurants found'
    ]);
}
?>
