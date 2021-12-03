<!--Based on https://code.tutsplus.com/tutorials/create-a-contact-form-in-php--cms-32314-->
<!--For sending mail : modified based on https://stackoverflow.com/questions/3175488/test-phps-mail-function-from-localhost/19625975-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Camille RABIER">
    <meta name="description" content="Contact form">
<!--    This page is to contact the administrator of the website-->
    <title>Contact</title>
    <link rel="stylesheet" type="text/css" href="website.css">
</head>
<body>
<header>
    <h1>CALI</h1>
</header>
<?php
//The form has been submitted
if (isset($_POST["Submit"])){
//    Get all infos given in the contact form
    if(!filter_var($_POST['Email'],FILTER_VALIDATE_EMAIL)){ //https://www.php.net/manual/fr/filter.examples.validation.php
        echo "Email is not valid";
    }
    else{
        $email = $_POST['Email'];
    }

    if (empty($_POST['Name'])){ /*The name is not required, if not given his name will be anonumous person*/
        $name = "An anonymous person";
    } else{
        $name = filter_var($_POST['Name'], FILTER_SANITIZE_STRING);
    }
    $subject =filter_var($_POST['Subject'], FILTER_SANITIZE_STRING);

    /*The email will be sent as coming from the submitter of the contact form*/
    $mail_header = "From : $email \r\n";

    /*Subject of the mail*/
    $subject_mail = "Contact form";

    /*Mail message*/
    $message = filter_var($_POST['Message'], FILTER_SANITIZE_STRING);
    $message = "From: $name \n About: $subject Message: $message";

    $mail= mail("camille.rabier@universite-paris-saclay.fr",$subject_mail,$message, $mail_header) or die("Error the mail could not be sent !");
    echo "Thank you for contacting us. You will get a reply within 24 hours";
}
?>
<div class="center">
    <h2>Contact us:</h2>
    <div class="container">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
            <label for="Name"> <b>Name</b></label>
            <input id="Name" name="Name" placeholder="Name"> <!--If no name is given, the name will be "Anonymous"-->
            <label for="Email"><b> Email </b></label>
            <input id="Email" name="Email" placeholder="Email" required>
            <label for="Subject"><b> Object</b></label>
            <input id="Subject" name="Subject" placeholder="Subject" required>
            <label for="Message"><b>Message</b></label><br>
            <textarea id="Message" name="Message" rows="10" cols="60"></textarea><br>
            <button class="little_submit_button" type="Submit" name="Submit">Send</button> <!--Sends email to administrator with info given-->
        </form>
    </div>
</div>
<footer>
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>