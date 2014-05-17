<?php
session_start();
if(isset($_SESSION['loginID']))
   echo "Your USERID is " . $_SESSION['loginID'] . "</br> 
   You are logged in as ". $_SESSION['loginName'];
?>