<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/Database.php';

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

$newDB = new Database;



//PdoSessionHandler
$pdo = $newDB->db;

$maxIdleTime = 1800; //session lifetime before expire
$NSSoptions = array(
    'gc_probability'    => 1,
    'gc_divisor'        => 100,
    'cookie_lifetime '  => 0,
    'gc_maxlifetime'    => ($maxIdleTime / 60 + 1)
);
$PSHoptions = array(        
    'db_table'		=> 'sessions',
    'db_id_col'		=> 'sessid',
    'db_data_col'	=> 'sessdata',
    'db_time_col'	=> 'sesstime'
);

$storage = new NativeSessionStorage($NSSoptions, new PdoSessionHandler($pdo, $PSHoptions));
$session = new Session($storage);

//START
$session->start();


//expire session on inactivity and regenerate on 30min life

if (time() - $session->getMetadataBag()->getLastUsed() > $maxIdleTime) {
    $session->invalidate();
    //redirect to expired session page
}

if (time() - $session->getMetadataBag()->getCreated() > $maxIdleTime) {
    // session started more than 30 minutes ago
    $session->migrate();    // change session ID for the current session and invalidate old session ID
}



// echo 'LAST_ACTIVITY: ' . $session->get('LAST_ACTIVITY') . "</br>"; 
// echo 'CREATED: ' . $session->get('CREATED') . "</br>"; 
// echo 'SessionAll: '; print_r($session->all()); echo "</br>";
