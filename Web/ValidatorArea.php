<!--Change user after validation-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Camille RABIER">
    <meta name="description" content="Assignment of transcript to annotators and validation of annotations submitted">
    <title>Validator area</title>
    <link href="website.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="Tabs.js" type="text/javascript"></script>

</head>
<body>
<!--HEADER-->
<header>
    <h1>CALI</h1>
</header>

<!--MENU-->
<div class="topnav">
    <a href="menu.html">Home</a>
    <a href="AnnotatorArea.php"> Annotator area</a>
    <a class="active" href="ValidatorArea.php"> Validator area</a> <!--Page active-->
    <a href="usermanag.html"> User management</a>
    <a href="Add_genome.php"> Add genome</a>
    <button class="fa fa-sign-out LogOut" onclick="window.location.href = 'LoginPage.html'" type="button">Log out</button>
</div>

<!--PAGE CONTENT-->
<h2> Annotation assignment and validation</h2>
<div class="center">
    <!--TABS-->
    <div class="tab">
        <button class="tablinks active" onclick="openTab(event,'Assignment',false)">Annotations assignment</button> <!--Page displayed by default-->
        <button class="tablinks" onclick="openTab(event,'Validation',false)">Annotations validation</button>
    </div>
    <div class="tabcontent active" id="Assignment">
        <h3> Annotations assignment</h3>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <?php require_once 'Assignment.php'; ?>
        </form>
    </div>
    <div class="tabcontent" id="Validation">
        <h3> Annotations validation</h3>
        <?php require_once 'Validation.php'; ?>
    </div>
</div>
<footer class="footer_2">
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>