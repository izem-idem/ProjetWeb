<?php
    session_start();
if (!isset($_SESSION['Email'])) {
    header("Location: LoginPage.php"); /*Si personne connectée redirige automatique vers Login*/
}
if(isset($_GET['id'])){
    $id = $_GET['id'];
} else{ //https://stackoverflow.com/questions/20300789/show-404-error-page-from-php-file-without-redirecting/20300839
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found. No ID was given in the URL";
    exit();
}
require('Genome_result.php');
    #require('test_2.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Page for Genome</title>
    <script type="text/javascript" src="https://www.ncbi.nlm.nih.gov/projects/sviewer/js/sviewer.js" id ="autoload"></script>
    <link rel="stylesheet" type="text/css" href="website.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> <!--CSS for log out button-->
</head>

<body>
<header>
    <h1>CALI</h1>
</header>
<div class="topnav"> <!--Menu-->
    <?php require_once 'libphp/Menu.php';
    echo Menu($_SESSION['Status'],"")?> <!--Affichera dans le menu toutes les pages accessibles par les lecteurs-->
</div>

<!------------------------------------------------------------PARTIE INFORMATION ---------------------------------------------------------->   





<div class="center">

    <h2>Genome Information</h2>
    <div class="container">
    <form class = "Inputs" method = "post">
        <p class='title'><?php echo $id_genome ?></p>
        <table class='spaced_table'>
            <tr>
                <td>Organism name: : </td>
                <td><p class='info'><?php echo $species   ?></p></td>
            </tr>
            <tr>
                <td>Strain : </td>
                <td><p class='info'><?php echo $strain   ?></p></td>
            </tr>
            <tr>
                <td>Sequence length (bp) : </td>
                <td><p class='info'><?php echo $size   ?></p></td>
            </tr>
        </table>
       
        <svg alt="download" class="dlbtn-icon" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 60 60"
             enable-background="new 0 0 60 60" xml:space="preserve">
            <g>
            <polygon points="48.75,36.25 48.75,48.75 11.25,48.75 11.25,36.25 5,36.25 5,55 55,55 55,36.25  "></polygon>
                <polygon points="42.5,23.75 36.25,23.75 36.25,5 23.75,5 23.75,23.75 17.5,23.75 30,42.5  "></polygon>
            </g>
        </svg>
        <button class="little_submit_button" type="submit" name="Load">Download Fasta Sequence</button>
    </form>

<!------------------------------------------------------------PARTIE INFORMATION ---------------------------------------------------------->   
    <br><br>
<!------------------------------------------------------------PARTIE LIENS EXTERNES ---------------------------------------------------------->   

        
        <label for="database_link"><b>External links</b></label>
        
        <select id="database_link" onChange = "window.open(this.value)">
            <option value="" disabled selected >Please choose a Database</option>
            <option value=<?php print $link_GTDB ?> > GTDB </option>
            <option value=<?php echo $link_Bensembl?>>Bacteria.Ensembl</option>
            <option value=<?php echo $link_ncbi ?>>NCBI</option>
        </select>
        
        

    
    
    
<!------------------------------------------------------------PARTIE LIENS EXTERNES ---------------------------------------------------------->   

  <!------------------------------------------------------------PARTIE VISUALISATION -------------------------------------------------------->   
   
   
    <p class="Visu"><b>Genome Visualisation via Sequence Viewer (NCBI)</b></p><br><br>

    <div id="sv1" class="SeqViewerApp">
     
      <a href="<?php echo $link ?>"></a> <!--lien vers seqViewerApp.js-->

    </div>
    <script>
      SeqViewOnReady(function() {
        var app = SeqView.App.findAppByDivId('sv1');
      });
      
    </script>
 <!------------------------------------------------------------PARTIE VISUALISATION ---------------------------------------------------------->
    </div>
</div>
<footer class="footer_2">
    <a href="Contact.php">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
