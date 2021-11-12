<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Camille RABIER">
    <meta name="description" content="Annotations of assigned transcript and visualisation of submitted annotations' status">
    <title>AnnotatorArea</title>
    <link href="website.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="Tabs.js" type="text/javascript"></script>
</head>
<body>
<header>
    <h1>CALI</h1>
</header>
<div class="topnav">
    <a href="menu.html">Home</a>
    <a class="active" href="AnnotatorArea.php"> Annotator area</a> <!--Page active-->
    <a href="ValidatorArea.php"> Validator area</a>
    <a href="usermanag.html"> User management</a>
    <a href="Add_genome.php"> Add genome</a>
    <button class="LogOut" onclick="window.location.href = 'LoginPage.html'" type="button">Log out</button>
</div>
<div class="center">
    <h2> Annotations</h2>
    <div class="tab">
        <button class="tablinks active" onclick="openTab(event,'ToDo',false)">Annotations to do</button>
        <button class="tablinks" onclick="openTab(event,'Status',false)">Annotations status</button>
    </div>
    <div class="tabcontent" id="ToDo">
        <h3> Annotations to do</h3>
        <?php require "ToDo.php"?>

    </div>
    <div class="tabcontent" id="Status">
        <h3> Annotations status</h3>
        <?php require "Status.php" ?>
    </div>
</div>
<footer class="footer">
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>