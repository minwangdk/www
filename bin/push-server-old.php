<?php
use Ratchet\Server\IoServer;
use MyApp\Pusher;

    require dirname(__DIR__) . '/vendor/autoload.php';
    


    $loop   = React\EventLoop\Factory::create();
    $pusher = new MyApp\Pusher;


    // Set up our WebSocket server for clients wanting real-time updates
    $webSock = new React\Socket\Server($loop);
    $webSock->listen(8080, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect
    $webServer = new Ratchet\Server\IoServer(
        new Ratchet\Http\HttpServer(
            new Ratchet\WebSocket\WsServer(
                new Ratchet\Wamp\WampServer(
                    new Pusher();
                )
            )
        ),
        $webSock
    );

    $loop->run();