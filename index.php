<?php
 
include 'database.php';
 
 

$action = $_GET['action'] ?? '';

// Handle requests based on the action
switch ($action) {
    case 'get_restaurants':
        include 'api/get_restaurants.php';  // Include your restaurant API
        break;

    case 'get_terminals':
        include 'api/get_terminals.php';  // Include your terminal API
        break;

    // Add more cases for other endpoints here
    case 'login':
        include 'api/auth_api.php';    // Include ticket API
        break;
        case 'logout':
            include 'api/logout.php';    // Include ticket API
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
