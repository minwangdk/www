<?php
require_once 'common.php';
$newDB = new Database;
$db = $newDB->db;

try 
{ 	
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	//Get user id from DB
	$fetchUserID = $db->prepare(
        "   SELECT id, username 
            FROM users 
            WHERE username 	= :username
            AND password 	= :password 	");
	$fetchUserID->execute(array(
		':username' => $username,
		':password' => $password));	

	$userID = $fetchUserID->fetch();
	
	//DEBUG
	echo "User row: "; print_r($userID); ?></br><?php
}    
catch(PDOException $e)
{
	echo $e->getMessage();
}

//Set session to logged in userID	
$session->set('userid', $userID['id']);
$session->set('username', $userID['username']);
require 'setToken.php';


//DEBUG
 echo "Session All: "; print_r($session->all()); echo "\n";
