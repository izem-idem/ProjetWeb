<?php
// Connect to database
require_once 'libphp/db_utils.php'; /*Functions to connect and disconnect the database*/
connect_db();


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
      <form action= <?php echo $_SERVER['PHP_SELF'] ; ?> method='POST'>
        <label for="email"><b>Email</b></label><br>
        <input type="text" placeholder="Email" name="email" id="email" required><br>
        <button name="submit" type="submit" class="big_submit_button"> Send email</button> <!--envoie après vérification que l'email existe dans la BD un email avec un lien personnalisé pour Reset_password-->
      </form>
    </div>
</div>
<?php
if (isset($_POST['submit'])) { /*Find if the submit has been clicked*/
  /*Check that Email is valid*/
  if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) { //https://www.php.net/manual/fr/filter.examples.validation.php
    echo '<div class="error_login">
            <p> Invalid email address </p>
          </div>' ;
  } else {
    $Email = $_POST['email'] ;
    $user_res = pg_query_params($db_conn, $user_query, array($Email)) or die("Error " . pg_last_error());
    if (pg_num_rows($user_res)==0){ /*Verify that user exists*/
      echo '<div class="error_login">
              <p> Unknown email address </p>
            </div>' ;
    } else {
      // Generate random password
      $random_string = substr(md5(rand()),0, 100) ; // https://thewebtier.com/php/generate-secure-random-strings-php/
      /*Encrypt password*/
      $hash_password = password_hash($random_string,PASSWORD_ARGON2ID); //https://stackoverflow.com/questions/47602044/how-do-i-use-the-argon2-algorithm-with-password-hash?fbclid=IwAR3cRiQN_WSth5loXg1AxTKEobgrHS1qYQnbR7yU3JB95NKkKEZs7D3UkCI
      /*Change the password in the database*/
      $update_password = pg_query_params($db_conn, $update_password_query, array($Email, $hash_password)) or die("Error " . pg_last_error());


      $to = $Email; // Send email to our user
      $subject = "CALI : New password"; // Give the email a subject
      $emessage = "You have requested a new password for the CALI website. Here it is : ".$random_string ;

      // if emessage is more than 70 chars
      $emessage = wordwrap($emessage, 70, "\r\n");

      // Our emessage above including the link
      $headers   = array();
      $headers[] = "MIME-Version: 1.0";
      $headers[] = "Content-type: text/plain; charset=iso-8859-1";
      $headers[] = "From: no-reply <noreply@yourdomain.com>";
      $headers[] = "Subject: {$subject}";
      $headers[] = "X-Mailer: PHP/".phpversion(); // Set from headers

      mail($to, $subject, $emessage, implode("\r\n", $headers));

      echo '<div class="message">
              <p> An email was sent to your adress with your new password </p>
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
