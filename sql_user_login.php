<?php
require_once 'common.php';
$newDB = new Database;
$db = $newDB->db;

try 
{ 	
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	//Get user id from DB
	$fetchUser = $db->prepare(
        "   SELECT id, username, identifier 
            FROM users 
            WHERE username 	= :username
            AND password 	= :password 	");
	$fetchUser->execute(array(
		':username' => $username,
		':password' => $password));	

	$userResult = $fetchUser->fetch();
	
	//DEBUG
	echo "User row: "; print_r($userResult); ?></br><?php
}    
catch(PDOException $e)
{
	echo $e->getMessage();
}

//Set session to logged in userResult	
$session->set('userid', $userResult['id']);
$session->set('username', $userResult['username']);
$session->set('identifier', $userResult['identifier']);

//Renew token and save to DB, if login exists
setToken();
setcookie("UserIdentifier", $userResult['identifier'], time() + 31536000, '/');

//DEBUG
 echo "Session All: "; print_r($session->all()); echo "\n";
