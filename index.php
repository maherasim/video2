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
    case 'manager_login':
        include 'api/auth_api_manager.php';    // Include ticket API
        break;
   case 'manager_logout':
            include 'api/manager_logout.php';    // Include ticket API
            break;
    case 'agent_login':
            include 'api/auth_api_agent.php';    
            break;
    case 'agent_logout':
                include 'api/agent_logout.php';    // Include ticket API
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

 
    default:
        echo json_encode(['error' => 'Invalid action.']);  // Default case for invalid action
        break;
}
?>
