<?php
// Start a user session
session_start() ;
// Remove previous session variables
session_unset() ;
// Connect to database
require_once 'libphp/db_utils.php'; /*Functions to connect and disconnect the database*/
connect_db();

// Get the hashed password, check that this user still has access
$password_query = "SELECT Password FROM website.users WHERE Email = $1 AND Access=TRUE ";
// Get the role of the user in the database
$status_query = "SELECT Status FROM website.users WHERE Email = $1 " ;

// Change last connection time of the user
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
        <form action= <?php echo $_SERVER['PHP_SELF'] ; ?> method='POST'>
          <label for="email"><b>Email</b></label><br>
          <input type="text" placeholder="Enter email" name='email' id="email" required><br>
          <label for="psw"><b>Password</b></label><br>
          <input type="password" placeholder="Enter Password" name='psw' id="psw" required><br>
          <button name="submit" class="big_submit_button" type="submit">Login</button>
        </form>
        <?php
          if (isset($_POST['submit'])) { /*Find if the submit has been clicked*/
            /*Check that Email is valid*/
            if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) { //https://www.php.net/manual/fr/filter.examples.validation.php
              echo 'Email is invalid' ;
            } else {
              $Email = $_POST['email'] ;
              $psw = $_POST['psw'] ;
              $psw_res = pg_query_params($db_conn, $password_query, array($Email)) or die("Error " . pg_last_error());
              $hash_password = pg_fetch_result($psw_res, 0, 0) ;
              if(!password_verify($psw, $hash_password)) { //https://stackoverflow.com/questions/47602044/how-do-i-use-the-argon2-algorithm-with-password-hash?fbclid=IwAR3cRiQN_WSth5loXg1AxTKEobgrHS1qYQnbR7yU3JB95NKkKEZs7D3UkCI
                echo 'Wrong login/password combination' ;
              } else {
                $update_last_conn = pg_query_params($db_conn, $update_last_conn_query, array($Email)) or die("Error " . pg_last_error());
                $status_res = pg_query_params($db_conn, $status_query, array($Email)) or die("Error " . pg_last_error());
                $_SESSION['Status'] = pg_fetch_result($status_res, 0, 0) ;
                $_SESSION["Email"] = $Email ;
                header('Location: search_page.php');
              }
            }
          }
        ?>
        <div class="error_login" hidden>
            <p> Wrong login/password combination </p>
        </div>
        <div class="Sign up">
            <span>Don't have an account ?</span>
            <a href="AccountCreation.php"> Sign up </a>
        </div>
        <div class="Forgotten_Password">
            <a href="Forgotten_password.html"> Forgot password ? </a> <!--envoit sur Forgotten_password.html-->
        </div>
    </div>
</div>
<?php
disconnect_db(); /*Disconnect from database*/
?>

<footer>
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
