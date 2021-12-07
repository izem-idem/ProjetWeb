<?php
session_start();
if (isset($_SESSION['Email'])){
    if ($_SESSION['Status']!='Admin'){
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
    <meta name="description" content="Adding of non-annotated genome">
    <meta name="viewport" content="width=device, initial-scale=1.0">
    <title>Add genome</title>
    <link rel="stylesheet" type="text/css" href="website.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
<!--HEADER-->
<header>
    <h1>CALI</h1>
</header>

<!--MENU-->
<div class="topnav">
<?php require_once 'libphp/Menu.php';
echo Menu($_SESSION['Status'],"Add_genome.php")?>
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
        <?php require 'Parser_other.php';?>
    </div>
</div>

<!--FOOTER-->
<footer>
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>