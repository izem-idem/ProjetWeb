<!DOCTYPE html>
<html>
<head>
    <title>Result Page for Genome</title>
    <script type="text/javascript" src="https://www.ncbi.nlm.nih.gov/projects/sviewer/js/sviewer.js" id ="autoload"></script><!--si je mets id ="autoload" y'a aucun effet apparent-->
</head>
<style>
        
     body {
    
        background-color: lavender ;
     }
     
     h2{
     
        text-align: center;
     }
     
   
    .topnav a:hover {
        background-color: #ddd;
        color: black;
    }

    /* Onglet actif*/
    .topnav a.active {
        background-color: slateblue;
        text-decoration: underline;
        color: white;
    }
    
    button:hover {
        opacity: 0.8;
    }
    
    .LogOut{
        float: right
    }
    

    .topnav {
       background-color: darkslateblue;
       overflow: hidden;
    }
    
    .topnav a {
        float: left;
        color: #f2f2f2; /*couleur texte du menu*/
        text-align: center;
        padding: 14px 16px;
        text-decoration: none; /*pas de surlignement*/
        font-size: 17px;
    }
    

    .Download-Button {
        background-color: cornflowerblue;
        color: white;
        
     }
         
</style>
        
<body>
    <div class="topnav">
    
            <a href="#Home">Home</a>
            <a href="Add_genome.html"> Add genome</a> 
            <a class="active" href="User gestion"> User database</a> <!--Page active-->
            <a href="#Annotation"> Annotation database</a>
            <a href="#Annotation"> Annotation to affect</a>
            <button type="button" class="LogOut"> <span class="glyphicon glyphicon-log-out"></span> Log out</button>
    </div>
    
    <div class ="genome information">
       
    <h2 class = "heading">Genome Information</h2>
    
    <p class="titre"><b>Genome ID</b></p>
    <p class="p1">

        Organism name: XXXXXX<br><br>
        Strain : XXXXXXX<br><br>
        Link to NCBI : XXXXX<br><br>
        Type of DNA: XXXXXXX<br><br>
        Sequence Length : XXXXXX <br><br>
        
    </p>
    
    <svg alt="download" class="dlbtn-icon" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 60 60" enable-background="new 0 0 60 60" xml:space="preserve">
        <g>
        <polygon points="48.75,36.25 48.75,48.75 11.25,48.75 11.25,36.25 5,36.25 5,55 55,55 55,36.25  "></polygon>
        <polygon points="42.5,23.75 36.25,23.75 36.25,5 23.75,5 23.75,23.75 17.5,23.75 30,42.5  "></polygon>
        </g>
    </svg>
      
    <button class = "Download-Button" type = "submit">Download Fasta Sequence</button><br>

    <p class = "Visu"><b>Visualisation via Sequence Viewer (NCBI)</b></p><br><br>
    
    </div>
    
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
        
      <a href="<?php echo $link.$id.$appname?>"></a> <!--si je met AE014075.1 (genbank) ça marche -->
    </div>
    <script>
      SeqViewOnReady(function() {
        var app = SeqView.App.findAppByDivId('sv1');
      });
      
    </script>

    
    
            

</body>
</html>

