<?php   
     #idd = $_GET['id'];
     $idd="AAN78503";
     require('gene-prot_result.php');
     
      
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Page for Gene/Protein</title>
    <script type="text/javascript" src="https://www.ncbi.nlm.nih.gov/projects/sviewer/js/sviewer.js" id ="autoload"></script>
    <link rel="stylesheet" type="text/css" href="web/website.css">
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
    <a href="Add_genome.html"> Add genome</a>
    <button type="button" class="LogOut" onclick="window.location.href = 'LoginPage.html'">Log out </button>
</div>

<div class ="Inception">

<div class="center">

    <h2>Gene/Protein Information</h2>
    
    <label for="gene"> GENE-PROTEIN ID</label>
    <input class="gene_id" value=<?php echo $idd ?> disabled><br>
    <label for="organism_name"> Organism name:</label>
    <input class="info" id="organism_name" type="text" value=<?php echo $organism  ?> disabled><br>
    <label for="strain"> Strain:</label>
    <input class="info" id="strain" type="text" value=<?php echo $strain  ?> disabled><br>
    <label for="genetic_support"> Genetic support:</label>
    <input class="info" id="genetic_support" type="text" value=<?php echo $support?> disabled><br>
    <label for="localisation"> Localisation:</label>
    <input class="info" id="localisation" type="text" value=<?php echo $loc ?> disabled><br>
    <label for="gene_type"> Gene type:</label>
    <input class="info" id="gene_type" type="text" value= <?php echo $biotype?> disabled><br>
    <label for="seq_length"> Sequence length(nt):</label>
    <input class="info" id="seq_length" type="text" value=<?php echo  $size_nt ?> disabled><br>
    <label for="desc"> Description:</label>
    <input class="info" id="desc" type="text" value=<?php echo $fonction  ?> disabled><br>
    <label for="Gene_symbol"> Gene Symbol </label>
    <input class="info" id="Gen_symbol" type="text" value=<?php echo $symbol  ?> disabled><br>
    <label for="annot"> Annotated by:</label>
    <input class="info" id="annot" type="text" value=<?php echo $mail?> disabled><br>


    <b>File to Download:</b>
     <select id="database_link" onChange = "window.open(this.value)">
            <option value="" disabled selected >Please choose File to Download</option>
            <option value=<?php echo $url_gene ?>>Gene sequence</option>
            <option value=<?php echo $url_prot ?>>Protein sequence</option>
           
     </select>
    
    
    <br><br>
    
    <?php
    
    if (isset($_POST['BLAST'])){
        
        $seqtype=$_POST['Blast_seq'];
        $database=$_POST['Blast_db'];
        $blast_type=$_POST['Blast_type'];
        
        require('rebond.php');   
    }
    
    
    ?>
    
    <form class = "BLast" method = "post">
        <select name = "Blast_seq">
            <option value="Protein"> Protein Sequence</option>
            <option value="Nucleic">Nucleic Acid Sequence</option>
        </select>
    
    
        <select name = "Blast_db">
            <option value="pdb"> Protein Data Bank</option>
            <option value="nt">Nucleotide collection</option>
            <option value="nr"> Non-redundant Protein Sequence</option>
            <option value="swissprot">UNIPROT</option>
            <option value="refseq_rna">Transcript Reference Sequences</option>
        </select>
       
         <select name = "Blast_type">
            <option value=tblastn> tBLASTn </option>
            <option value="blastp">BLASTp</option>
            <option value="blastn"> BLASTn</option>
            <option value="blastx">BLASTx</option>
        </select>
        
        <button class="little_submit_button" type="submit" name ="BLAST">BLAST</button>
    
    </form>
    
    <br><br>
    
     
       
    
    
    <label for="database_link"><b>External links</b></label>
    
        <select id="database_link" onChange = "window.open(this.value)">
            <option value="" disabled selected >Please choose a Database</option>
            <option value=<?php echo $url_ensembl ?>>Bacteria.Ensembl</option>
            <option value=<?php echo $goto_ncbi ?>>NCBI</option>
            <option value=<?php echo $goto_pfam ?>>PFAM</option>
            <option value=<?php echo $goto_uniprot ?>>Uniprot</option>
           
        </select>
    
        <p class="Visu"><b>Protein Visualisation via Sequence Viewer (NCBI)</b></p>
        
       <button class="button" onClick=<?php echo "window.open('${linker}');"?>> 
        <span class="icon">Access Genome Page</span>
       </button>
</div>
   
        

    

        
    <div id="sv2" class="SeqViewerApp">
        
       
      <a href=<?php echo $lien_SV ?>></a> <!--lien vers seqViewerApp.js-->

    </div>


</div>

<div class="Script_SV">

<script>
      
      SeqViewOnReady(function() {
        var app = SeqView.App.findAppByDivId('sv2');
      });
      
</script>

</div>

</div>


<footer>
    <a href="Contact.html">Contact</a><br>
    <p>© CALI 2021</p>
</footer>
</body>
</html>
