<?php
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Http\HttpServer as ReactHttpServer;
use React\Socket\SocketServer;
use React\EventLoop\Factory;



class NotificationServer implements MessageComponentInterface {
    public $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Optionally broadcast or handle message
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}


$loop = React\EventLoop\Factory::create();
$notificationServer = new NotificationServer();

// WebSocket Server
$wsServer = new IoServer(
    new HttpServer(
        new WsServer($notificationServer)
    ),
    new React\Socket\SocketServer('0.0.0.0:8080', [], $loop),
    $loop
);

// HTTP Server
$httpServer = new React\Http\HttpServer(function (ServerRequestInterface $request) use ($notificationServer) {
    if ($request->getUri()->getPath() === '/notify' && $request->getMethod() === 'POST') {
        $body = (string) $request->getBody();
        $data = json_decode($body, true);

        if (isset($data['type']) && $data['type'] === 'new_order') {
            $notification = json_encode($data);
            foreach ($notificationServer->clients as $client) {
                $client->send($notification);
            }
            return new Response(200, ['Content-Type' => 'application/json'], json_encode(['status' => 'ok']));
        }
        return new Response(400, [], 'Invalid data');
    }

    return new Response(404, [], 'Not Found');
});

$httpSocket = new React\Socket\SocketServer('0.0.0.0:8081', [], $loop); // different port than WebSocket
$httpServer->listen($httpSocket);

$loop->run();
