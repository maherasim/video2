<?php 

// websocket_server.php
require_once 'vendor/autoload.php';
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class TerminalStatusServer implements MessageComponentInterface {
    private $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Handle incoming message from clients if needed
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    // Method to broadcast terminal status updates to all connected clients
    public function broadcastStatusUpdate($status, $terminal_id) {
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'action' => 'update_terminal_status',
                'terminal_id' => $terminal_id,
                'status' => $status
            ]));
        }
    }
}

$server = new Ratchet\App('84.247.187.38', 9001); 
$server->route('/status', new TerminalStatusServer(), ['*']);
$server->run();

?>