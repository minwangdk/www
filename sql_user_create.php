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
	
	$sql = "INSERT INTO users(username, popcorn, password) VALUES ('$user_name', '500', '$password')";

	echo($sql);

    /*** INSERT data ***/
    $count = $dbh->exec($sql);
    
    /*** echo the number of affected rows ***/
    echo $count;

    /*** close the database connection ***/
    $dbh = null;
    }
catch(PDOException $e)
    {
    echo $e->getMessage();
	}
	
	
	
?>
