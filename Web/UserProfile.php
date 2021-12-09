<?php
/*
Author : Alex Westbrook
This page is for users to view their own profile on the website. It is accessible to any Reader.
The user can choose to change his password from here, see ResetPassword.php
*/

// Connect to user session
session_start();
if (!isset($_SESSION['Email'])){ // If no user session is on (direct access from url), send back to LoginPage.php
    header("Location: LoginPage.php");
}

// Connect to database
require_once 'libphp/db_utils.php'; // Functions to connect and disconnect the database
connect_db();

// SQL queries

// Get user information in database
$user_query = "SELECT * FROM website.users WHERE Email=$1";
$user_res = pg_query_params($db_conn, $user_query, array($_SESSION['Email'])) or die("Error " . pg_last_error());
$user = pg_fetch_assoc($user_res,0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Search </title>
    <link rel="stylesheet" type="text/css" href="website.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> <!--CSS for log out button-->
</head>
<body>
<header>
    <h1>CALI</h1>
</header>
<div class="topnav">
    <?php require_once 'libphp/Menu.php';
    echo Menu($_SESSION['Status'],"UserProfile.php")?> <!-- Displays the menu on top of the page, see Menu.php for detail-->
</div>
<div class="center">
    <div class="container">
      <div class='double'>
          Email address: <br>
      </div>
      <div class='double'>
        <p class='info'> <?php echo $user['email'] ?> </p><br>
      </div>

      <div class='double'>
          First name: <br>
      </div>
      <div class='double'>
        <p class='info'> <?php echo $user['firstname'] ?> </p>
      </div>

      <div class='double'>
          Last name: <br>
      </div>
      <div class='double'>
        <p class='info'> <?php echo $user['lastname'] ?> </p><br>
      </div>

      <div class='double'>
          Telephone number: <br>
      </div>
      <div class='double'>
        <p class='info'> <?php echo $user['telnr'] ?> </p><br>
      </div>

      <div class='double'>
          Status: <br>
      </div>
      <div class='double'>
        <p class='info'> <?php echo $user['status'] ?> </p><br>
      </div>

      <div class="title">
          <a href="ResetPassword.php">
            <button class='little_submit_button'> Change password </button>
          </a>
      </div>
    </div>
</div>

<?php
disconnect_db() ;
?>

<footer>
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
