<?php
// Include your database connection or other necessary files
include 'database.php';


header("Location: ../admin/login.php");

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_restaurants':
        include 'api/get_restaurants.php';
        break;
    
    case 'signin':
        include 'signin.php';
        break;

    // Add more cases for other endpoints here
    case 'get_tickets':
        include 'api/get_tickets.php';    // Include ticket API
        break;

    case 'signin':
        include 'admin/signin.php';  // Handle sign-in logic
        break;

    case 'signout':
        include 'admin/signout.php';  // Handle sign-out logic
        break;

    // Handle any other necessary cases
    default:
        echo json_encode(['error' => 'Invalid action.']);  // Default case for invalid action
        break;
}
?>
