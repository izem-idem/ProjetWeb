<?php
/*
Author : Alex Westbrook
This does not show a page, whenever someone clicks the Logout button he is redirected here, and then to the Login Page
*/

// Connect to user session
session_start() ;
// Remove previous session variables
unset($_SESSION['Email']) ;
unset($_SESSION['Status']) ;
// Go back to Login Page
header('Location: LoginPage.php');
?>
