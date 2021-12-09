<?php
/*
Author : Alex Westbrook
The user enters his email and password to access the rest of the website, he is first sent to search_page.php
This page is the default page for the website, if a user hasn't logged in yet he will be redirected to this page
The user can only access two pages (LoginPage.php, the Forgotten_password.php) if he hasn't logged in

Information about who is logged in is passed through a session with 2 variables :
Email : identifies the user
Status : role on the website
*/

// Start a user session or connect to existing one
session_start() ;
/* If a user session is on, send back to search_page.php
User must be logged out to access this page */
if (isset($_SESSION['Email'])) {
  header('Location: search_page.php') ;
}
// Connect to database
require_once 'libphp/db_utils.php'; // Functions to connect and disconnect the database
connect_db();

// SQL queries

// Get the encrypted password and role of the user in the database, check that this user still has access
$user_query = "SELECT Password,Status FROM website.users WHERE Email = $1 AND Access=TRUE ";

// Change last connection time of the user in database
$update_last_conn_query = "UPDATE website.users SET LastConnection='now' WHERE Email=$1";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="website.css">
</head>
<body>
<header>
    <h1>CALI</h1>
</header>
<div class="center">
    <h2> Log in </h2>
    <div class="container">
        <!--
        Start a form for login information
          If email is a valid user in database and the password is correct,
          then sets the user's session and sends him to search_page.php
        -->
        <form action= <?php echo $_SERVER['PHP_SELF'] ; ?> method='POST'>
          <label for="email"><b>Email</b></label><br>
          <input type="text" placeholder="Enter email" name='email' id="email" required><br>
          <label for="psw"><b>Password</b></label><br>
          <input type="password" placeholder="Enter Password" name='psw' id="psw" required><br>
          <button name="submit" class="big_submit_button" type="submit">Log in</button>
        </form>
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
                  $user = pg_fetch_assoc($user_res,0);
                  $hash_password = $user['password'];
                  // Check that password matches with the encrypted one in database
                  if(!password_verify($_POST['psw'], $hash_password)) { //https://stackoverflow.com/questions/47602044/how-do-i-use-the-argon2-algorithm-with-password-hash?fbclid=IwAR3cRiQN_WSth5loXg1AxTKEobgrHS1qYQnbR7yU3JB95NKkKEZs7D3UkCI
                      echo '<div class="error_login">
                              <p> Wrong password </p>
                            </div>' ;
                  } else { // Login checks out
                      // Update user's last connection time
                      $update_last_conn = pg_query_params($db_conn, $update_last_conn_query, array($Email)) or die("Error " . pg_last_error());
                      // Set session variables
                      $status = $user['status'];
                      $_SESSION['Status'] = $status;
                      $_SESSION["Email"] = $Email ;
                      // Send to home page
                      header('Location: search_page.php');
                  }
              }
            }
          }
        ?>

        <div class="Sign up">
            <span>Don't have an account ?</span>
            <a href="AccountCreation.php"> Sign up </a>
        </div>
        <div class="Forgotten_Password">
            <a href="Forgotten_password.php"> Forgot password ? </a>
        </div>
    </div>
</div>
<?php
disconnect_db(); // Disconnect from database
?>

<footer>
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
