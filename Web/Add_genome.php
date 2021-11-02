<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add genome</title>
    <link rel="stylesheet" type="text/css" href="website.css">

</head>
<body>
<header>
    <h1>CALI</h1>
</header>
<div class="topnav">
    <a href="menu.html">Home</a>
    <a href="AnnotatorArea.html"> Annotator area</a>
    <a href="ValidatorArea.html"> Validator area</a>
    <a href="usermanag.html"> User management</a>
    <a class= "active" href="Add_genome.html"> Add genome</a> <!--Page active-->
    <button type="button" class="LogOut" onclick="window.location.href = 'LoginPage.html'">Log out </button>
</div>

<h2> Adding of a genome</h2>
<div class="center">
    <div class="container">
        <form action="Parser.php" method="POST" enctype="multipart/form-data">
            <label for="genome_name"><b>Species name</b> </label>
            <input type="text" id="genome_name" name="genome_name" placeholder="Species name" required><br>
            <label for="strain_name"><b>Strain name</b></label>
            <input type="text" id="strain_name" name="strain_name" placeholder="Strain name"><br>
            <label for="genome_file">Please add the fasta file of the genome :</label><br>
            <input type="file" id="genome_file" name="genome_file" accept=".fa, .fasta, .fas" required><br>
            <label for="cds_file">Please add the cds description file for the genome :</label><br>
            <input type="file" id="cds_file" name="cds_file" accept=".fa, .fasta, .fas" required><br>
            <label for="pep_file">Please add the peptide description file for the genome :</label><br>
            <input type="file" id="pep_file" name="pep_file" accept=".fa, .fasta, .fas" required><br>
            <button type="submit" name="submit" class="big_submit_button">Add genome</button>
        </form>
    </div>

</div>
<footer>
    <a href="Contact.html">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>