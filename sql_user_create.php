<?php
require 'common.php';



try {
	$newDB = new Database;
	$db = $newDB->db;
	
	$db->beginTransaction();

	$username = $_POST['username'];
	$password = $_POST['password'];
	$startingPopcorn = 500;

	$sql = $db->prepare("INSERT INTO users(username, password, popcorn) VALUES ('$username', '$password', '$startingPopcorn')");
	$count = $sql->execute();	 	

	$db->commit();

	$db = null;
	print_r($sql);
	echo "</br>" . $count . "change committed.";       
    
}
catch(PDOException $e){
	$db->rollback();
    echo "Failed: " . $e->getMessage();
}
	
// try {

// }




// 	// HERE CURRENT SESSION BIND TO CREATED USER
// 	$session->set('name', 'Drak');
// 	$session getId()
// 	$session

