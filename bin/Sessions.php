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





