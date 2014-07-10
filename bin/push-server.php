<?php
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\Session\SessionProvider;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use React\Socket\Server;
use React\EventLoop\Factory;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use MyApp\Pusher;

    require dirname(__DIR__) . '/vendor/autoload.php';
    require dirname(__DIR__) . '/Database.php';


    $loop = Factory::create();
    $newDB = new Database;
    $pdo = $newDB->db; 
    $options = array(        
        'db_table' => 'sessions',
        'db_id_col' => 'sessid',
        'db_data_col' => 'sessdata',
        'db_time_col' => 'sesstime'
    );
    $handler = new PdoSessionHandler($pdo, $options);
       

    $webSock = new Server($loop);
    $webSock->listen(8080, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect

    $webServer = new IoServer(
        new HttpServer(
            new WsServer(
                new SessionProvider(
                    new WampServer(
                        new Pusher()),                 
                    $handler
                )
            )
            
        ),
        $webSock
    );

    $loop->run();

    
        
  
         
