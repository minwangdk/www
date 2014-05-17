<?php

require 'db_config.php';


try {
    $popid = $_GET['p'];
    $dbh = new PDO("mysql:host=$db_hostname;dbname=$db_name", $db_username, $db_password);    
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
    $sql = $dbh->prepare("SELECT stockprice FROM pops WHERE id = '$popid' ");
	$sql->execute();
    $result = $sql->fetchAll();
    $stockprice = $result[0]['stockprice'];

    /*** close the database connection ***/
    $dbh = null;
}

catch(PDOException $e){
    echo $e->getMessage();
}
	
echo $stockprice;

?>