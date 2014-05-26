<?php
session_start();  
if(isset($_SESSION['loginID']))
   echo "Your USERID is " . $_SESSION['loginID'] . "</br> 
   You are logged in as ". $_SESSION['loginName'];

?>


<html>



<form name="input" action="sql_user_login.php" method="get">
	Username: <label>Username</label><input type="text" name="username"></br>
	Password: <label>Password</label><input type="password" name="password">

	<div class="checkbox">
        <input id="remember" type="checkbox"> <label for=
        "remember">Remember me on this computer</label>
    </div>

    
 
<input type="submit" value="Submit">
</form>


</html>