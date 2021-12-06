<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Page for Genome</title>
    <script type="text/javascript" src="https://www.ncbi.nlm.nih.gov/projects/sviewer/js/sviewer.js" id ="autoload"></script>
    <link rel="stylesheet" type="text/css" href="website.css">
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

<div class="center">

    <h2>Genome Information</h2>

    <input class="info title" value="Genome ID" disabled><br>
    <label for="organism_name"> Organism name:</label>
    <input class="info" id="organism_name" type="text" value="XXXXXX" disabled><br>
    <label for="strain"> Strain:</label>
    <input class="info" id="strain" type="text" value="XXXXXX" disabled><br>
    <label for="genetic_support"> Genetic support:</label>
    <input class="info" id="genetic_support" type="text" value="XXXXXX" disabled><br>
    <label for="seq_length"> Sequence length(nt):</label>
    <input class="info" id="seq_length" type="text" value="XXXXXX" disabled><br>
    <svg alt="download" class="dlbtn-icon" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 60 60"
         enable-background="new 0 0 60 60" xml:space="preserve">
        <g>
        <polygon points="48.75,36.25 48.75,48.75 11.25,48.75 11.25,36.25 5,36.25 5,55 55,55 55,36.25  "></polygon>
            <polygon points="42.5,23.75 36.25,23.75 36.25,5 23.75,5 23.75,23.75 17.5,23.75 30,42.5  "></polygon>
        </g>
    </svg>
    <button class="little_submit_button" type="submit">Download Fasta Sequence</button>
    <br><br>

    <label for="database_link"><b>External links</b></label>
    <select id="database_link">
        <option value="Biocyc"> BioCyc</option>
        <option value="Bacteria.Ensembl">Bacteria.Ensembl</option>
    </select>
    
    <button class="little_submit_button" type="submit">Access</button> <br><br>
    <p class="Visu"><b>Visualisation via Sequence Viewer (NCBI)</b></p><br><br>
    <div id="sv1" class="SeqViewerApp">
    
        <?php 
        
        require('simple_html_dom.php');
        
        $link = "embedded=true&id=";
        $appname = "&appname=IZEM";
        $html =file_get_html("https://www.ncbi.nlm.nih.gov/assembly/?term=ASM744v1");
        foreach($html->find(".refseq") as $element)  { 
            $id=strip_tags($element);
        }
        
        #$id= "AE014075.1";
        
        ?>        
    
      <a href="<?php echo $link.$id.$appname?>"></a> <!--lien vers seqViewerApp.js-->

    </div>
    <script>
      SeqViewOnReady(function() {
        var app = SeqView.App.findAppByDivId('sv1');
      });
      
    </script>

<footer>
    <a href="Contact.html">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
