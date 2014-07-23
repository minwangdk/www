<?php
require 'common.php';

$newDB = new Database;
$db = $newDB->db;

$userID;
$startingPopcorn = 500;
$identifier = random_text( 'alnum', 16 );

try 
{ //insert new user, fetch new userID, insert popcorn into currency table with new userID
	$username = $_POST['username'];
	$password = $_POST['password'];

	//beginTransaction
	$db->beginTransaction();

	//create user
	$createUser 	= $db->prepare(
		"	INSERT INTO users(username, password, identifier)
			VALUES (:username, :password, :identifier)	");
	$count = $createUser->execute(array(
		':username' 	=> $username,
		':password' 	=> $password,
		':identifier'	=> $identifier			));	 	

	//fetch UserID after creation
	$fetchUserID = $db->prepare(
        "   SELECT id 
            FROM users 
            WHERE username = :username
            AND password = :password 	");
	$fetchUserID->execute(array(
		':username' => $username,
		':password' => $password));	

	$userID = $fetchUserID->fetch();

	//create popcorn for user using fetched userID
	$createPopcorn	= $db->prepare(
		"	INSERT INTO users_own_currency(users_id, popcorn)
			VALUES (:userID, :startingPopcorn) 	");
	$createPopcorn->execute(array(
		':userID'			=> $userID['id'],
		':startingPopcorn'	=> $startingPopcorn));







	//commit and close db
	$db->commit();
	$db = null;

	//debug
	echo "</br>" . $count . "change committed.";       
    
}
catch(PDOException $e)
{
	$db->rollback();
    echo "Failed: " . $e->getMessage();
}
	





// 	// HERE CURRENT SESSION BIND TO CREATED USER
// 	$session->set('name', 'Drak');
// 	$session getId()
// 	$session

