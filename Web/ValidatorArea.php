<!--Change user after validation-->
<!--afficher que 100 transcripts-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Validator area</title>
    <link href="website.css" rel="stylesheet" type="text/css">
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
    <a href="AnnotatorArea.html"> Annotator area</a>
    <a class="active" href="ValidatorArea.php"> Validator area</a> <!--Page active-->
    <a href="usermanag.html"> User management</a>
    <a href="Add_genome.html"> Add genome</a>
    <button class="LogOut" onclick="window.location.href = 'LoginPage.html'" type="button">Log out</button>
</div>

<!--PAGE CONTENT-->
<h2> Annotation assignment and validation</h2>
<div class="center">
    <!--TABS-->
    <div class="tab">
        <button class="tablinks" onclick="openTab(event,'Assignment',false)">Annotations assignment</button>
        <button class="tablinks" onclick="openTab(event,'Validation',false)">Annotations validation</button>
    </div>
    <div class="tabcontent" id="Assignment">
        <h3> Annotations assignment</h3>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
        <?php require 'Assignment.php';?>
        </form>
    </div>
    <div class="tabcontent" id="Validation">
        <h3> Annotations validation</h3>
        <div class="tab">
           <button class="tablinks" onclick="openTab(event,'Transcript1',true)">Transcript1</button>
        </div>
        <div class="tabcontent" id="Transcript1">
            <label for="id_transcript3"></label>
            <input class="info title" disabled id="id_transcript3" value="Transcript1">
            <a href="Gene-ProtPage.html"> Temporary page for transcript 1</a><br>
            Annotator :
            <label for="user1"></label>
            <input class="info" disabled id="user1" value="User1"><br>
            <label for="gene_id3">ID of gene</label>
            <input class="info" disabled id="gene_id3" value="XXXXX"><br>
            <label for="gene_biotype3">Biotype of gene</label>
            <input class="info" disabled id="gene_biotype3" value="XXXXX"><br>
            <label for="gene_symbol3">Gene symbol</label>
            <input class="info" disabled id="gene_symbol3" value="XXXXX"><br>
            <label for="prot_biotype3"> Biotype of protein</label>
            <input class="info" disabled id="prot_biotype3" value="XXXXX"><br>
            <label for="description3">Description of function</label>
            <input class="info" disabled id="description3" value="XXXXX"><br>
            <label for="comment1">Comment</label>
            <input id="comment1">
            <button class="little_submit_button" type="submit"> Validate</button>
            <button class="little_submit_button" type="submit"> Reject</button>
        </div>
    </div>
</div>
<footer>
    <a href="Contact.html">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>