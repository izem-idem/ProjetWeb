<?php


session_start();
if (!isset($_SESSION['Email'])) {
    header("Location: LoginPage.php"); /*Si personne connectée redirige automatique vers Login*/
}
if(isset($_GET['id'])){
    $idd = $_GET['id'];
} else{ //https://stackoverflow.com/questions/20300789/show-404-error-page-from-php-file-without-redirecting/20300839
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found. No ID was given in the URL";
    exit();
}
require('gene-prot_result.php');


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Page for Gene/Protein</title>
    <script type="text/javascript" src="https://www.ncbi.nlm.nih.gov/projects/sviewer/js/sviewer.js"
            id="autoload"></script>
    <link rel="stylesheet" type="text/css" href="website.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!--CSS for log out button-->
</head>

<body>
<header>
    <h1>CALI</h1>
</header>

<div class="topnav"><!--Menu-->
    <?php require_once 'libphp/Menu.php';
    echo Menu($_SESSION['Status'], "") ?> <!--Affichera dans le menu toutes les pages accessibles par les lecteurs-->
</div>

<div class="center">

    <div class="container">

        <h2>Gene/Protein Information</h2>
        <p class='title'><?php echo $idd ?></p>
        <!---->
        <?php if ($validated==1) {
            echo "<table class='spaced_table annotation'>";
        }else {
            echo "<table class='spaced_table'>";
        } ?>
            <tr>
                <td>Organism name:</td>
                <td><p class='info'><?php echo $organism ?></p></td>
            </tr>
            <tr>
                <td>Strain:</td>
                <td><p class='info'><?php echo $strain ?></p></td>
            </tr>
            <tr>
                <td>Genetic support:</td>
                <td><p class='info'><?php echo $support ?></p></td>
            </tr>
            <tr>
                <td>Localisation:</td>
                <td><p class='info'><?php echo $loc ?></p></td>
            </tr>
            <tr>
                <td>Gene biotype:</td>
                <td><span class='info'><?php echo $biotype ?></span></td>
            </tr>
            <tr>
                <td>Sequence length(nt):</td>
                <td><p class='info'><?php echo $size_nt ?></p></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><span class='info'><?php echo $fonction ?></span></td>
            </tr>
            <tr>
                <td>Gene symbol:</td>
                <td><span class='info'><?php echo $symbol ?></span></td>
            </tr>
            <tr>
                <td>Annotated by:</td>
                <td><p class='info'><?php echo $mail ?></p></td>
            </tr>
        </table>


        <b>File to Download:</b>
        <select id="database_link" onChange="window.open(this.value)">
            <option value="" disabled selected>Please choose File to Download</option>
            <option value=<?php echo $url_gene ?>>Annotation with gene sequence</option>
            <option value=<?php echo $url_prot ?>>Annotation with protein sequence</option>

        </select>


        <br><br>

        <?php

        if (isset($_POST['BLAST'])) {

            $seqtype = $_POST['Blast_seq'];
            $database = $_POST['Blast_db'];
            $blast_type = $_POST['Blast_type'];

            require('rebond.php');
        }


        ?>

        <form class="BLast" method="post">
            <select name="Blast_seq">
                <option value="Protein"> Protein Sequence</option>
                <option value="Nucleic">Nucleic Acid Sequence</option>
            </select>


            <select name="Blast_db">
                <option value="pdb"> Protein Data Bank</option>
                <option value="nt">Nucleotide collection</option>
                <option value="nr"> Non-redundant Protein Sequence</option>
                <option value="swissprot">UNIPROT</option>
                <option value="refseq_rna">Transcript Reference Sequences</option>
            </select>

            <select name="Blast_type">
                <option value=tblastn> tBLASTn</option>
                <option value="blastp">BLASTp</option>
                <option value="blastn"> BLASTn</option>
                <option value="blastx">BLASTx</option>
            </select>

            <button class="little_submit_button" type="submit" name="BLAST">BLAST</button>

        </form>

        <br><br>


        <label for="database_link"><b>External links</b></label>

        <select id="database_link" onChange="window.open(this.value)">
            <option value="" disabled selected>Please choose a Database</option>
            <option value=<?php echo $url_ensembl ?>>Bacteria.Ensembl</option>
            <option value=<?php echo $goto_ncbi ?>>NCBI</option>
            <option value=<?php echo $goto_pfam ?>>PFAM</option>
            <option value=<?php echo $goto_uniprot ?>>Uniprot</option>

        </select>

        <p class="Visu"><b>Protein Visualisation via Sequence Viewer (NCBI)</b></p>

        <button class="button" onClick=<?php echo "window.open('${linker}');" ?>>
            <span class="icon">Access Genome Page</span>
        </button>
    </div>


    <div id="sv2" class="SeqViewerApp">


        <a href=<?php echo $lien_SV ?>></a> <!--lien vers seqViewerApp.js-->

    </div>
    <div class="Script_SV">

        <script>

            SeqViewOnReady(function () {
                var app = SeqView.App.findAppByDivId('sv2');
            });

        </script>

    </div>

</div>




<footer class="footer_2">
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
