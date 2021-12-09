<?php
/*
Author : Alex Westbrook
This page is for receiving a new random password via a mail.
The user can then change it on the website, see ResetPassword.php
If for some reason the mail cannot be sent then the password isn't changed.
*/

// Connect to database
require_once 'libphp/db_utils.php'; /*Functions to connect and disconnect the database*/
connect_db();

// SQL queries

// Update the user's password in the database
$update_password_query = "UPDATE website.users SET Password=$2 WHERE Email=$1";
// Check that the user in the database and that this user still has access
$user_query = "SELECT Email FROM website.users WHERE Email = $1 AND Access=TRUE ";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot your password ?</title>
    <link rel="stylesheet" type="text/css" href="website.css">
</head>
<body>
<header>
    <h1>CALI</h1>
</header>
<div class="announcement">
    <p><b>If you have forgotten your password, enter the email you used to sign up. An email will be sent with a new password.</b></p>
</div>
<div class="center">
    <div class="container">
      <!--
      Start a form for user email
        If there is an account for this user,
        then send a mail with new password

        Only if the mail is correctly sent,
        then change the password
      -->
      <form action= <?php echo $_SERVER['PHP_SELF'] ; ?> method='POST'>
        <label for="email"><b>Email</b></label><br>
        <input type="text" placeholder="Email" name="email" id="email" required><br>
        <button name="submit" type="submit" class="big_submit_button"> Send email</button>
      </form>
      <div class="Log in">
          <span>Back to</span>
          <a href="LoginPage.php"> Log in </a>
      </div>
    </div>
</div>

<?php
if (isset($_POST['submit'])) { // Find if the submit has been clicked
  // Check that Email is valid
  if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) { // https://www.php.net/manual/fr/filter.examples.validation.php
    echo '<div class="error_login">
            <p> Invalid email address </p>
          </div>' ;
  } else {
    $Email = $_POST['email'] ;
    // Get user information in the database
    $user_res = pg_query_params($db_conn, $user_query, array($Email)) or die("Error " . pg_last_error());
    if (pg_num_rows($user_res)==0){ // Check that user exists
      echo '<div class="error_login">
              <p> Unknown email address </p>
            </div>' ;
    } else {
      // Generate random password in hex
      $random_string = substr(md5(rand()),0, 100) ; // https://thewebtier.com/php/generate-secure-random-strings-php/

      // The email will be sent as coming from the submitter of the contact form
      $mail_header = "From : CALI <noreply@CALI.com> \r\n";
      //Subject of the mail
      $subject_mail = "CALI : New password request";
      // Mail message
      $message = "Hello, \n You have requested a new password for the CALI website. Here it is : ".$random_string ;
      // Send mail and check if it is sent
      if (!mail($Email,$subject_mail,$message, $mail_header)) {
        echo "<div class='error_login'>
                <p> Mail could not be sent, your password hasn't changed </p>
              </div>";
      } else { // Mail sent
        echo '<div class="message">
                <p> An email was sent to your adress with your new password </p>
              </div>';
        // Encrypt password
        $hash_password = password_hash($random_string,PASSWORD_ARGON2ID); // https://stackoverflow.com/questions/47602044/how-do-i-use-the-argon2-algorithm-with-password-hash?fbclid=IwAR3cRiQN_WSth5loXg1AxTKEobgrHS1qYQnbR7yU3JB95NKkKEZs7D3UkCI
        // Change the password in the database
        $update_password = pg_query_params($db_conn, $update_password_query, array($Email, $hash_password)) or die("Error " . pg_last_error());
        }
      }
    }
  }
  disconnect_db();
?>

<footer>
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
