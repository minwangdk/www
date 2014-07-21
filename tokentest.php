<?php

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

//tester $j iterations of test, $i max generation of tokens

$results = array();
$tokens = array();
for ( $j = 1; $j <= 10; $j++) {
    unset($tokens);
    $tokens = array();

    for ( $i = 0; $i < 10000; $i++) {    
        $newtoken = random_text("alnum", 3);
        if (isset($tokens[$newtoken])) {
            echo $i . " duplicate found: " . $newtoken . "</br>"; 
            $results[] = $i;
            break 1;
        }
        
        $tokens[$newtoken] = $newtoken;
        echo $i . " " . end($tokens) . "</br>";    

    }
    echo "duplicate number: " . $j . "</br>";
}
$result = 0;
for ($i = 0; $i < count($results); $i++ ) {

    $result += $results[$i];
}
echo "total iterations: " . $result;
$result = $result / count($results);

echo "average duplicate at " . $result . " iterations";