<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/Database.php';


use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

$newDB = new Database;
$pdo = $newDB->db;
$options = array(        
    'db_table'		=> 'sessions',
    'db_id_col'		=> 'sessid',
    'db_data_col'	=> 'sessdata',
    'db_time_col'	=> 'sesstime'
);
$storage = new NativeSessionStorage(array(), new PdoSessionHandler($pdo, $options));
$session = new Session($storage);


$session->start();

if ($session->has('LAST_ACTIVITY') && (time() - $session->get('LAST_ACTIVITY') > 1800)) {
    // last request was more than 30 minutes ago
    $session->invalidate();
}
$session->set('LAST_ACTIVITY', time()); // update last activity time stamp
//PUT THIS LINE IN ALL ACTIONS **************************

if (!$session->has('CREATED')) {
    $session->set('CREATED', time());
} else if (time() - $session->get('CREATED') > 1800) {
    // session started more than 30 minutes ago
    $session->migrate();    // change session ID for the current session and invalidate old session ID
    $session->set('CREATED', time());  // update creation time
}

// echo 'LAST_ACTIVITY: ' . $session->get('LAST_ACTIVITY') . "</br>"; 
// echo 'CREATED: ' . $session->get('CREATED') . "</br>"; 
// echo 'SessionAll: '; print_r($session->all()); echo "</br>";
