<?php
// Connect to database
require_once 'libphp/db_utils.php'; /*Functions to connect and disconnect the database*/
connect_db();

// Insert user in DB
$add_user_query = "INSERT INTO website.users(Email, Password, FirstName, LastName, TelNr, LastConnection, Status, Access) VALUES ($1,$2,$3,$4,$5,'now','Reader',TRUE)";
/*The Status of the user is initialized at Reader and his Access is True by default. The LastConnection is the current time, all other fields are given with $_POST variables*/
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
        <form action= <?php echo $_SERVER['PHP_SELF'] ; ?> method='POST'>
          <label for="email"><b>Email</b></label><br>
          <input type="text" placeholder="Email" name="email" id="email" required><br> <!--Vérification que l'adresse mail a l'air valide-->
          <label for="psw"><b>Password</b></label><br>
          <input type="password" placeholder="Password" name="psw" id="psw" required minlength="7"><br>
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
          <button name ='submit' class="big_submit_button" type="submit">Register</button> <!--Crée utilisateur dans la base de données avec rôle reader par défaut et envoie mail à administrateur pour prévenir qu'il y a un nouveau utilisateur avec le rôle voulu-->
        </form>
        <?php
        if (isset($_POST['submit'])) { /*Find if the submit has been clicked*/
            /*Check that Email is valid*/
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) die('Email is invalid'); //https://www.php.net/manual/fr/filter.examples.validation.php
            /*Check that passwords match*/
            if ($_POST["psw"] != $_POST["psw2"]) die('Passwords do not match');

            /*Get all the information entered*/
            /*By filtering the input, we verify that no harmul characters like script injections are given as input*/
            $Email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
            $FirstName = filter_var($_POST["first_name"], FILTER_SANITIZE_STRING);
            $LastName = filter_var($_POST["last_name"], FILTER_SANITIZE_STRING);
            $TelNr = filter_var($_POST["tel"], FILTER_SANITIZE_STRING);
            /*Encrypt password in database*/
            $hash_password = password_hash($_POST["psw"],PASSWORD_ARGON2ID); //https://stackoverflow.com/questions/47602044/how-do-i-use-the-argon2-algorithm-with-password-hash?fbclid=IwAR3cRiQN_WSth5loXg1AxTKEobgrHS1qYQnbR7yU3JB95NKkKEZs7D3UkCI

            // Check if the user already exists in the database
            $check_user_res = pg_query_params($db_conn, $check_user_query, array($Email)) or die("Error " . pg_last_error());
            if (pg_num_rows($check_user_res)==1){ /*Check that there is an entry for this Email*/
              $user_access = pg_fetch_result($check_user_res,0,0) ;
              if ($user_access) { // User already exists and account is active
                echo '<div class="error_login">
                        <p> There is already an account for this email address </p>
                      </div>' ;
                $sendmail = FALSE ;
              } else { // User existed and his account was deactivated
                /*Add the user to the database*/
              	$update_user = pg_query_params($db_conn, $update_user_query, array($Email, $hash_password, $FirstName, $LastName, $TelNr)) or die("Error " . pg_last_error());
                echo "Your user profile was created successfully";
                $sendmail = TRUE ;
              }

            } else {
              /*Add the user to the database*/
            	$add_user = pg_query_params($db_conn, $add_user_query, array($Email, $hash_password, $FirstName, $LastName, $TelNr)) or die("Error " . pg_last_error());
            	echo "Your user profile was created successfully";
              $sendmail = TRUE ;
            }

            if ($sendmail=TRUE and $_POST["role"] != 'Reader') { /*Send an email to Admin to get an other role than Reader*/
              //Get the Email of the Administrators
              $res_admin = pg_query($db_conn, $query_admin);
              $admin_emails = pg_fetch_all_columns($res_admin);
              //Send them each an email
              foreach ($admin_emails as $admin_email){
                $to = $admin_email; // Send email to our user
                $subject = "New user role"; // Give the email a subject
                $emessage = "New user ".$Email." would like to get a role as a ".$_POST["role"] ;

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
              }
            }
        }
        ?>
        <div class="Log in">
            <span>Already have an account ?</span>
            <a href="LoginPage.php"> Log in </a>
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
