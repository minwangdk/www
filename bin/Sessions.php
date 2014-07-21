<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/Database.php';

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

//high security random token generator
function random_text( $type = 'alnum', $length = 8 )
{
    switch ( $type ) {
        case 'alnum':
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'alpha':
            $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'hexdec':
            $pool = '0123456789abcdef';
            break;
        case 'numeric':
            $pool = '0123456789';
            break;
        case 'nozero':
            $pool = '123456789';
            break;
        case 'distinct':
            $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
            break;
        default:
            $pool = (string) $type;
            break;
    }
 
 
    $crypto_rand_secure = function ( $min, $max ) {
        $range = $max - $min;
        if ( $range < 0 ) return $min; // not so random...
        $log    = log( $range, 2 );
        $bytes  = (int) ( $log / 8 ) + 1; // length in bytes
        $bits   = (int) $log + 1; // length in bits
        $filter = (int) ( 1 << $bits ) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ( $rnd >= $range );
        return $min + $rnd;
    };
 
    $token = "";
    $max   = strlen( $pool );
    for ( $i = 0; $i < $length; $i++ ) {
        $token .= $pool[$crypto_rand_secure( 0, $max )];
    }
    return $token;
}

//PdoSessionHandler
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

//START
$session->start();

//expire session on inactivity and regenerate on 30min life
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

//Renew token and save to DB, if login exists
require dirname(__DIR__) . '/setToken.php';

// echo 'LAST_ACTIVITY: ' . $session->get('LAST_ACTIVITY') . "</br>"; 
// echo 'CREATED: ' . $session->get('CREATED') . "</br>"; 
// echo 'SessionAll: '; print_r($session->all()); echo "</br>";
