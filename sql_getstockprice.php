<?php
$p = $_GET['p'];

/*** mysql hostname ***/
$db_hostname = 'localhost';

/*** mysql username ***/
$db_username = 'omnipwn';

/*** mysql password ***/
$db_password = 'godaddyIllus22';

$db_name = 'popcorn';



try {
    $dbh = new PDO("mysql:host=$db_hostname;dbname=$db_name", $db_username, $db_password);
    /*** echo a message saying we have connected ***/
    
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	
	$sql = $dbh->prepare("SELECT stockprice FROM pops WHERE id = '$p' ");
	$sql->execute();
    $result = $sql->fetchAll();
    $stockprice = $result[0]['stockprice'];

    /*** close the database connection ***/
    $dbh = null;
    }
catch(PDOException $e)
    {
    echo $e->getMessage();
	}
	
echo $stockprice;

?>