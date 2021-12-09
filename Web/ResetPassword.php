<?php
/*
Author : Alex Westbrook
This page is for changing the user's password on the website. It is accessed from UserProfile.php
*/

// Connect to user session
session_start();
if (!isset($_SESSION['Email'])){ // If no user session is on (direct access from url), send back to LoginPage.php
    header("Location: LoginPage.php");
}
// Connect to database
require_once 'libphp/db_utils.php'; // Functions to connect and disconnect the database
connect_db();

// Update the user's password in the database
$update_password_query = "UPDATE website.users SET Password=$2 WHERE Email=$1";

// Get the encrypted password of the user in the database
$password_query = "SELECT Password FROM website.users WHERE Email = $1";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset password</title>
    <link rel="stylesheet" type="text/css" href="website.css">
</head>
<body>
<header>
    <h1>CALI</h1>
</header>
<div class="announcement">
    <p><b> Please complete this form with your new password </b></p>
</div>
<div class="center">
    <div class="container">
      <!--
      Start a form for old and new passwords
        If the old password is correct,
        if the password confirmation matches the first one,
        then change the password in the database
      -->
      <form action= <?php echo $_SERVER['PHP_SELF'] ; ?> method='POST'>
        <label for="old_psw"><b>Current password</b></label><br>
        <input type="password" placeholder="Current password" name="old_psw" id="old_psw" required><br>
        <label for="psw"><b>New password</b></label><br>
        <input type="password" placeholder="New password" name="psw" id="psw" required minlength="7"><br> <!-- Password must be at least 7 characters long-->
        <label for="psw2"><b>Confirm new password</b></label><br>
        <input type="password" placeholder="Confirm new password" name="psw2" id="psw2" required minlength="7"><br>
        <button name="submit" type="submit" class="big_submit_button"> Reset password</button>
      </form>
      <div>
          <span> Back to </span>
          <a href="UserProfile.php"> profile page </a>
      </div>
    </div>
</div>

<?php
  if (isset($_POST['submit'])) { // Find if the submit has been clicked
    $old_psw = $_POST['old_psw'];
    // Get the old password in the database
    $psw_res = pg_query_params($db_conn, $password_query, array($_SESSION['Email'])) or die("Error " . pg_last_error());
    $old_hash_password = pg_fetch_result($psw_res,0,0);
    // Check that old password matches with the encrypted one in database
    if(!password_verify($old_psw, $old_hash_password)) { // https://stackoverflow.com/questions/47602044/how-do-i-use-the-argon2-algorithm-with-password-hash?fbclid=IwAR3cRiQN_WSth5loXg1AxTKEobgrHS1qYQnbR7yU3JB95NKkKEZs7D3UkCI
        echo '<div class="error_login">
                <p> Wrong password </p>
              </div>' ;
    } else {
      // Check that new passwords match
      if($_POST["psw"] != $_POST["psw2"]) {
        echo '<div class="error_login">
                <p> Passwords do not match </p>
              </div>' ;
      } else { // Everything checks out
      // Encrypt password
      $hash_password = password_hash($_POST["psw"],PASSWORD_ARGON2ID); // https://stackoverflow.com/questions/47602044/how-do-i-use-the-argon2-algorithm-with-password-hash?fbclid=IwAR3cRiQN_WSth5loXg1AxTKEobgrHS1qYQnbR7yU3JB95NKkKEZs7D3UkCI
      // Change the password in the database
      $update_password = pg_query_params($db_conn, $update_password_query, array($_SESSION['Email'], $hash_password)) or die("Error " . pg_last_error());
      echo '<div class="message">
              <p> Your password was reset successfully </p>
            </div>';
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
