<?php

require dirname(__DIR__) . '/vendor/autoload.php';

//Symfony Session start    
use Symfony\Component\HttpFoundation\Session\Session;

$session = new Session();
$session->start();

// set and get session attributes
$session->set('name', 'Drak');
$session->get('name');

// set flash messages
$session->getFlashBag()->add('notice', 'Profile updated');

// retrieve messages
foreach ($session->getFlashBag()->get('notice', array()) as $message) {
    echo '<div class="flash-notice">'.$message.'</div>';
}

