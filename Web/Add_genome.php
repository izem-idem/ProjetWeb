<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Camille RABIER">
    <meta name="description" content="Adding of non-annotated genome">
    <meta name="viewport" content="width=device, initial-scale=1.0">
    <title>Add genome</title>
    <link rel="stylesheet" type="text/css" href="website.css">
</head>

<body>
<!--HEADER-->
<header>
    <h1>CALI</h1>
</header>

<!--MENU-->
<div class="topnav">
    <!--Link to pages-->
    <a href="menu.html">Home</a>
    <a href="AnnotatorArea.html"> Annotator area</a>
    <a href="ValidatorArea.php"> Validator area</a>
    <a href="usermanag.html"> User management</a>
    <a class= "active" href="Add_genome.html"> Add genome</a> <!--Page active-->
    <!--Link to logout ==> redirect to login page-->
    <button type="button" class="LogOut" onclick="window.location.href = 'LoginPage.html'">Log out </button>
</div>

<!--PAGE CONTENT-->
<h2> Adding of a genome</h2>
<div class="center">
    <div class="container">
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" enctype="multipart/form-data">
            <!--Genome specie/strain names -->
            <label for="genome_name"><b>Species name</b> </label>
            <input type="text" id="genome_name" name="genome_name" placeholder="Species name" required><br>
            <label for="strain_name"><b>Strain name</b></label>
            <input type="text" id="strain_name" name="strain_name" placeholder="Strain name"><br>
            <!-- User input of the 3 files : genome, CDS description, peptides description -->
            <label for="genome_file">Please add the fasta file of the genome :</label><br>
            <input type="file" id="genome_file" name="genome_file" accept=".fa, .fasta, .fas" required><br>
            <label for="cds_file">Please add the cds description file for the genome :</label><br>
            <input type="file" id="cds_file" name="cds_file" accept=".fa, .fasta, .fas" required><br>
            <label for="pep_file">Please add the peptide description file for the genome :</label><br>
            <input type="file" id="pep_file" name="pep_file" accept=".fa, .fasta, .fas" required><br>
            <!-- Submitting of names and file : it launches the php script-->
            <button type="submit" name="submit" class="big_submit_button">Add genome</button>
        </form>
        <?php require 'Parser.php';?>
    </div>
</div>

<!--FOOTER-->
<footer>
    <a href="Contact.html">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>