<?php
        
    $id=$_GET['id'];
    require('Genome_result.php');
        
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Page for Genome</title>
    <script type="text/javascript" src="https://www.ncbi.nlm.nih.gov/projects/sviewer/js/sviewer.js" id ="autoload"></script>
    <link rel="stylesheet" type="text/css" href="web/website.css">
</head>

<body>
<header>
    <h1>CALI</h1>
</header>
<div class="topnav">
    <a href="AnnotatorArea.html"> Annotator area</a>
    <a href="ValidatorArea.html"> Validator area</a>
    <a href="usermanag.html"> User management</a>
    <a href="Add_genome.html"> Add genome</a>
    <button type="button" class="LogOut" onclick="window.location.href = 'LoginPage.html'">Log out </button>
</div>

<!------------------------------------------------------------PARTIE INFORMATION ---------------------------------------------------------->   





<div class="center">

    <h2>Genome Information</h2>

    <form class = "Inputs" method = "post">
        <input class="info title" value= <?php echo $id_genome ?> disabled><br>
        <label for="organism_name"> Organism name:</label>
        <input class="info" id="organism_name" type="text" value=<?php echo $species   ?> disabled><br>
        <label for="strain"> Strain:</label>
        <input class="info" id="strain" type="text" value=<?php echo $strain ?> disabled><br>
        <label for="seq_length"> Sequence length(nt):</label>
        <input class="info" id="seq_length" type="text" value=<?php echo $size?> disabled><br><br>
       
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

<footer>
    <a href="Contact.html">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
