<?php
session_start();  
if(isset($_SESSION['loginID']))
   echo "Your USERID is " . $_SESSION['loginID'] . "</br> 
   You are logged in as ". $_SESSION['loginName'];

?>


<html>



<form name="input" action="sql_user_login.php" method="get">
Username: <input type="text" name="username"></br>
Password: <input type="password" name="password">
<input type="submit" value="Submit">
</form>


</html>