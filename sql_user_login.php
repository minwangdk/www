<?php
require 'common.php';

try 
{ 
	$newDB = new Database;
	$db = $newDB->db;
	//Fetch user session
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	//Get user id from DB
	$sql = $db->prepare("SELECT id, username, popcorn, created_date FROM users WHERE username ='$username' AND password='$password' ");
	$sql -> execute();
	$user = $sql->fetchAll();
	
	print_r($user);	
?>
</br>
<?php
	
   
}
    
catch(PDOException $e)
{
	echo $e->getMessage();
}
	
try
{
	$userid = $user[0]['id'];
	$sql = $db->prepare("SELECT pops_id, quantity FROM users_own_pops WHERE users_id = '$userid'");
	$sql -> execute();
	$pops = $sql->fetchAll(PDO::FETCH_KEY_PAIR);
	print_r($pops);
?>
</br>
<?php

 /*** close the database connection ***/
    $db = null;
}

catch(PDOException $e)
{
	echo $e->getMessage();
}





//Set current logged in userID
	// $session->invalidate();
	$session->replace(
		array(	
			'userid' 	=> $user[0]['id'],
			'username'	=> $user[0]['username'],
			// 'popcorn'	=> $user[0]['popcorn'],
			// 'stocks'	=> $pops
		)
	);
	print_r($session->all());
	
?>