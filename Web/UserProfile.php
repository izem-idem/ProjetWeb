<?php
session_start();
if (!isset($_SESSION['Email'])){
    header("Location: LoginPage.php");
}

// Connect to database
require_once 'libphp/db_utils.php'; /*Functions to connect and disconnect the database*/
connect_db();

// Get User information in database
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
    echo Menu($_SESSION['Status'],"UserProfile.php")?>
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

      <div class='title'>
        <form action= <?php echo $_SERVER['PHP_SELF'] ; ?> method='POST'>
          <button class='little_submit_button' type='submit' name = 'submit'> Change Password </button>
        </form>
      </div>
      <?php
        if (isset($_POST['submit'])) {
          header('Location: ResetPassword.php') ;
        }
      ?>
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
