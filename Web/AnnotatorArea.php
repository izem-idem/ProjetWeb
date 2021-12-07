<!--Page displaying tabs to annotate transcript and see status of submitted annotations-->
<!--It is only accessible for annotators (and validator/admin)-->
<!--A more precise description for the tabs are in their respective PHP pages (ToDo.php and Status.php)-->

<?php
session_start();
if (isset($_SESSION['Email'])){
    if (!in_array($_SESSION['Status'],['Admin','Annotator','Validator']) ){
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested is not accessible for you.";
        echo "<a href='search_page.php'>Go back to search page</a>";
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
    <meta name="description"
          content="Annotations of assigned transcript and visualisation of submitted annotations' status">
    <title>AnnotatorArea</title>
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
    <?php require_once 'libphp/Menu.php';
    echo Menu($_SESSION['Status'],"AnnotatorArea.php")?>
</div>

<!--PAGE CONTENT-->
<div class="page_container">
    <div class="center">
        <h2> Annotations</h2>
        <!--TABS LINKS : Annotations to do and status of validation for annotation done-->
        <div class="tab">
            <button class="tablinks active" onclick="openTab(event,'ToDo',false)">Annotations to do</button><!--Page displayed by default-->
            <button class="tablinks" onclick="openTab(event,'Status',false)">Annotations status</button>
        </div>

        <!--TABS-->
        <!--Annotations to do-->
        <div class="tabcontent active" id="ToDo">
            <h3> Annotations to do</h3>
            <?php require "ToDo.php" ?>
        </div>
        <!--Annotations validation status-->
        <div class="tabcontent" id="Status">
            <h3> Annotations status</h3>
            <?php require "Status.php" ?>
        </div>
    </div>

    <!--FOOTER-->
    <footer class="footer_2">
        <a href="Contact.php">Contact</a><br>
        <p>© CALI 2021</p>
    </footer>
</div>
</body>
</html>