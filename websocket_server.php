<?php 
require_once 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class TerminalStatusServer implements MessageComponentInterface {
    private $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection: ({$conn->resourceId})\n";
    }
    
    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Message received from {$from->resourceId}: $msg\n";
    }
    
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection closed: ({$conn->resourceId})\n";
    }
    

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    // Method to broadcast terminal status updates to all connected clients
    public function broadcastStatusUpdate($status, $terminal_id) {
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'action' => 'terminal_status',
                'terminal_id' => $terminal_id,
                'status' => $status,
            ]));
        }
    }
}

$server = new Ratchet\App('0.0.0.0', 9001); // Bind to all interfaces
$server->route('/status', new TerminalStatusServer(), ['*']);
$server->run();


?>