<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Registration</title>
    <link rel="stylesheet" type="text/css" href="website.css">
</head>

<?php
// Connect to database
require_once 'libphp/db_utils.php'; /*Functions to connect and disconnect the database*/
connect_db();

// Insert user in DB
$add_user_query = "INSERT INTO website.users(Email, Password, FirstName, LastName, TelNr, LastConnection, Status) VALUES ($1,$2,$3,$4,$5,'now','Reader')";
/*The Status of the user is initialized at Reader and the LastConnection is the current time, all other fields are given with $_POST variables*/

?>
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
          <input type="password" placeholder="Password" name="psw" id="psw" required><br>
          <label for="psw2"><b>Confirm Password</b></label><br>
          <input type="password" placeholder="Confirm password" name="psw2" id="psw2" required><br>
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
            if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) die('Email is invalid'); //https://www.php.net/manual/fr/filter.examples.validation.php
            /*Check that passwords match*/
            if($_POST["psw"] != $_POST["psw2"]) die('Passwords do not match');

            /*Get all the information entered*/
            /*By filtering the input, we verify that no harmul characters like script injections are given as input*/
            $Email = filter_var($_POST["email"],FILTER_SANITIZE_STRING);
          	$FirstName = filter_var($_POST["first_name"],FILTER_SANITIZE_STRING);
          	$LastName = filter_var($_POST["last_name"],FILTER_SANITIZE_STRING);
          	$TelNr = filter_var($_POST["tel"],FILTER_SANITIZE_STRING);
            /*Encrypt password in database*/
            $hash_password = password_hash($_POST["psw"],PASSWORD_ARGON2ID); //https://stackoverflow.com/questions/47602044/how-do-i-use-the-argon2-algorithm-with-password-hash?fbclid=IwAR3cRiQN_WSth5loXg1AxTKEobgrHS1qYQnbR7yU3JB95NKkKEZs7D3UkCI
            /*Add the user to the database*/
          	$add_user = pg_query_params($db_conn, $add_user_query, array($Email, $hash_password, $FirstName, $LastName, $TelNr)) or die("Error " . pg_last_error());
          	echo "Your user profile was created successfully";

            if ($_POST["role"] != 'Reader') { /*Send an email to Admin to get an other role than Reader*/
              /*The email will be sent as coming from the new user*/
              $mail_header = "From : $Email \r\n";
              /*Subject of the mail*/
              $subject_mail = "New user role";
              /*Message in the mail */
              $message = "Hi, I am a new user of your website and I would like to have a role as ".$_POST["role"] ;

              foreach ($admin as $admin_email){
                  $mail= mail($admin_email,$subject_mail,$message, $mail_header) or die("Error the mail could not be sent !");
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
    <a href="Contact.html">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
