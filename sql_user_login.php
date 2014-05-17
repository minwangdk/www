<?php
session_start();  

?>



<?php
/*** mysql hostname ***/
$db_hostname = 'localhost';

/*** mysql username ***/
$db_username = 'omnipwn';

/*** mysql password ***/
$db_password = 'godaddyIllus22';

$db_name = 'popcorn';


try {
	$user_name = $_GET['username'];
	$password = $_GET['password'];
	
	
    $dbh = new PDO("mysql:host=$db_hostname;dbname=$db_name", $db_username, $db_password);
   
   
    /*** echo a message saying we have connected ***/
    echo 'Connected to database<br />';
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	//Get user id from DB
	$login = $dbh->prepare("SELECT id FROM users WHERE username ='$user_name' AND password='$password' ");
	$login -> execute();
	$result = $login->fetchAll();
	$userID = $result[0]['id']; 
	
	

	// Get username from DB
	$getUsername = $dbh->prepare("SELECT username FROM users WHERE id ='$userID' AND password='$password' ");
	$getUsername -> execute();
	$result = $getUsername->fetchAll();
	$username = $result[0]['username'];
	
	//Set current logged in userID
	$_SESSION['loginID'] = $userID;
	$_SESSION['loginName'] = $username;
	
	if(isset($_SESSION['loginName']))
	echo "Your USERID is " . $_SESSION['loginID'] . "</br>
	You are logged in as ". $_SESSION['loginName'];
	
	?>
	<!-- <pre><? var_dump($result); ?></pre> -->
	<?
	


    /*** close the database connection ***/
    $dbh = null;
    }
    
	catch(PDOException $e)
    {
    echo $e->getMessage();
	}
	
	
	
?>