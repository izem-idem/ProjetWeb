<?php
session_start();
if (isset($_SESSION['Email'])){
    if (!in_array($_SESSION['Status'],['Admin','Validator']) ){
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested is not accessible for you.";
        echo "<a href='Search_page.php'>Go back to search page</a>";
        exit();
    }
}else {
    header("Location: LoginPage.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Camille RABIER">
    <meta name="description" content="Assignment of transcript to annotators and validation of annotations submitted">
    <title>Validator area</title>
    <!--CSS-->
    <link href="website.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> <!--CSS for log out button-->
    <!--Javascript-->
    <script src="Tabs.js" type="text/javascript"></script> <!--Script used to create tabs-->

</head>
<body>

<!--HEADER-->
<header>
    <h1>CALI</h1>
</header>

<!--NAVIGATION MENU-->
<div class="topnav">
    <a href="search_page.php">Search</a> <!--TODO Modify-->
    <a href="AnnotatorArea.php"> Annotator area</a>
    <a class="active" href="ValidatorArea.php"> Validator area</a> <!--Page active-->
    <a href="usermanag.php"> User management</a>
    <a href="Add_genome.php"> Add genome</a>
    <button class="fa fa-sign-out LogOut" onclick="window.location.href = 'LoginPage.html'" type="button">Log out</button><!--TODO modify-->
</div>

<!--PAGE CONTENT-->
<h2> Annotation assignment and validation</h2>
<div class="center">
    <!--TABS LINKS : Annotations assignment and validation of annotations-->
    <div class="tab">
        <button class="tablinks active" onclick="openTab(event,'Assignment',false)">Annotations assignment</button> <!--Page displayed by default-->
        <button class="tablinks" onclick="openTab(event,'Validation',false)">Annotations validation</button>
    </div>
    <!--TABS-->
    <!--Assignment of transcripts-->
    <div class="tabcontent active" id="Assignment">
        <h3> Annotations assignment</h3>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <?php require_once 'Assignment.php'; ?>
        </form>
    </div>
    <!--Validation of annotations-->
    <div class="tabcontent" id="Validation">
        <h3> Annotations validation</h3>
        <?php require_once 'Validation.php'; ?>
    </div>
</div>

<!--FOOTER-->
<footer class="footer_2">
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>