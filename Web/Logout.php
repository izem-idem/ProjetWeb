<?php
// Start a user session or connect to existing one
session_start() ;
// Remove previous session variables
unset($_SESSION['Email']) ;
unset($_SESSION['Status']) ;
// Go back to Login Page
header('Location: LoginPage.php');
?>
