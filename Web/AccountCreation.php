<?php
/*
Author : Alex Westbrook
This page is for creating an account on the website. Only one account can be created per email.
All fields are required, the password must have a minimal length of 7 characters and is encrypted in the database.
By default the account is set on Reader, if the user asks for another role, then a mail is sent to the Adminstrator for approval.
*/

// Connect to database
require_once 'libphp/db_utils.php'; /*Functions to connect and disconnect the database*/
connect_db();

// SQL queries

/* Insert user in database
Status is initialized at Reader
Access is True by default
LastConnection is the current time
All other fields are given with $_POST variables*/
$add_user_query = "INSERT INTO website.users(Email, Password, FirstName, LastName, TelNr, LastConnection, Status, Access) VALUES ($1,$2,$3,$4,$5,'now','Reader',TRUE)";
// Check if user exists
$check_user_query = "SELECT Access FROM website.users WHERE Email=$1";
// Update user information if his account was previously deleted
$update_user_query = "UPDATE website.users SET Password=$2, FirstName=$3, LastName=$4, TelNr=$5, LastConnection='now', Status='Reader', Access=TRUE WHERE Email=$1";
// Get Admin email to notify them of a new user
$query_admin = "SELECT Email FROM website.users WHERE Status='Admin'";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Registration</title>
    <link rel="stylesheet" type="text/css" href="website.css">
</head>


<body>
<header>
    <h1>CALI</h1>
</header>
<div class="center">
    <h2> Registration Form</h2>
    <div class="container">
        <!--
        Start a form for user information
          If the email address is valid and doesn't already have an account,
          if the password confirmation is correct,
          then create the account in the database

          If a special role is requested too,
          then send a mail to Admin for approval
        -->
        <form action= <?php echo $_SERVER['PHP_SELF'] ; ?> method='POST'>
          <label for="email"><b>Email</b></label><br>
          <input type="text" placeholder="Email" name="email" id="email" required><br>
          <label for="psw"><b>Password</b></label><br>
          <input type="password" placeholder="Password" name="psw" id="psw" required minlength="7"><br> <!-- Password must be at least 7 characters long-->
          <label for="psw2"><b>Confirm Password</b></label><br>
          <input type="password" placeholder="Confirm password" name="psw2" id="psw2" required minlength="7"><br>
          <label for="first_name"><b>First Name</b></label><br>
          <input type="text" placeholder="First Name" name="first_name" id="first_name" required><br>
          <label for="last_name"><b>Last Name</b></label><br>
          <input type="text" placeholder="Last Name" name="last_name" id="last_name" required><br>
          <label for="tel"><b>Telephone Number</b></label><br>
          <input type="text" placeholder="Telephone Number" name="tel" id="tel" required><br>
          <div class="role_select">
              <label for="role"> <b>Select role</b></label>
              <select name="role" id="role">
                  <option value="Reader">Reader</option>
                  <option value="Annotator">Annotator</option>
                  <option value="Validator">Validator</option>
              </select>
          </div>
          <button name ='submit' class="big_submit_button" type="submit">Register</button>
        </form>
        <div class="Log in">
            <span>Already have an account ?</span>
            <a href="LoginPage.php"> Log in </a>
        </div>

        <?php
        if (isset($_POST['submit'])) { // Find if the submit has been clicked
            // Check that Email is valid
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { // https://www.php.net/manual/fr/filter.examples.validation.php
              echo '<div class="error_login">
                      <p> Email is invalid </p>
                    </div>' ;
              die; // Stop here, require new submission
            }
            // Check that passwords match
            if ($_POST["psw"] != $_POST["psw2"]) {
              echo '<div class="error_login">
                      <p> Passwords do not match </p>
                    </div>' ;
              die ; // Stop here, require new submission
            }
            // Filter the input to verify that no harmul characters like script injections are given as input
            $Email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
            $FirstName = filter_var($_POST["first_name"], FILTER_SANITIZE_STRING);
            $LastName = filter_var($_POST["last_name"], FILTER_SANITIZE_STRING);
            $TelNr = filter_var($_POST["tel"], FILTER_SANITIZE_STRING);
            // Encrypt password in database
            $hash_password = password_hash($_POST["psw"],PASSWORD_ARGON2ID); // https://stackoverflow.com/questions/47602044/how-do-i-use-the-argon2-algorithm-with-password-hash?fbclid=IwAR3cRiQN_WSth5loXg1AxTKEobgrHS1qYQnbR7yU3JB95NKkKEZs7D3UkCI
            // Check if the user already exists in the database
            $check_user_res = pg_query_params($db_conn, $check_user_query, array($Email)) or die("Error " . pg_last_error());
            if (pg_num_rows($check_user_res)==1){ // Check that there is an entry for this Email
              $user_access = pg_fetch_result($check_user_res,0,0) ;
              if ($user_access) { // User already exists and account is active
                echo '<div class="error_login">
                        <p> There is already an account for this email address </p>
                      </div>' ;
                $sendmail = FALSE ;
              } else { // User existed and his account was deleted by removing access
                // Update user information in database and grant him access again
              	$update_user = pg_query_params($db_conn, $update_user_query, array($Email, $hash_password, $FirstName, $LastName, $TelNr)) or die("Error " . pg_last_error());
                echo '<div class="message">
                        <p> Your user profile was created successfully, please go back to the Login Page </p>
                      </div>';
                $sendmail = TRUE ;
              }
            } else { // There is no user for the email address
              // Add the user to the database
            	$add_user = pg_query_params($db_conn, $add_user_query, array($Email, $hash_password, $FirstName, $LastName, $TelNr)) or die("Error " . pg_last_error());
              echo '<div class="message">
                      <p> Your user profile was created successfully, please go back to the Login Page </p>
                    </div>';
              $sendmail = TRUE ;
            }
            // Send an email to Admin to get an other role than Reader
            if ($sendmail=TRUE and $_POST["role"] != 'Reader') {
              // Get the Email of the Administrators (potentially more than one)
              $res_admin = pg_query($db_conn, $query_admin);
              $admin_emails = pg_fetch_all_columns($res_admin);

              // The email will be sent as coming from the website
              $mail_header = "From : CALI <noreply@CALI.com> \r\n";
              // Subject of the mail
              $subject_mail = "CALI : New user wants a role";
              // Mail message with the desired role given in the form
              $message = "Hello, \n New user $Email would like to get a role as a ".$_POST["role"] ;
              foreach ($admin_emails as $admin_email){
                  // Send the mail, an error will be displayed if the mail cannot be sent
                  $mail= mail($admin_email,$subject_mail,$message, $mail_header) ;
              }
            }
        }
        ?>

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
